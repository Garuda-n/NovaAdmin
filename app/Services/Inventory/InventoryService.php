<?php

namespace App\Services\Inventory;

use App\Enums\InventoryTransactionType;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\StockInward;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Central gateway: Create a stock movement record.
     * ALL stock increases/decreases MUST go through this method.
     *
     * @param array $data
     * @return StockMovement
     */
    public function recordMovement(array $data): StockMovement
    {
        $movementDate = $data['movement_date'] ?? now()->toDateString();
        $businessDate = $data['business_date'] ?? $this->resolveBusinessDate($movementDate);

        return StockMovement::create([
            'company_id'       => $data['company_id'],
            'branch_id'        => $data['branch_id'],
            'counter_id'       => $data['counter_id'] ?? null,
            'product_id'       => $data['product_id'],
            'stock_item_id'    => $data['stock_item_id'] ?? null,
            'movement_type'    => $data['movement_type'],
            'transaction_type' => $data['transaction_type'] ?? null,
            'quantity'         => $data['quantity'],
            'unit_cost'        => $data['unit_cost'] ?? null,
            'reference_type'   => $data['reference_type'] ?? null,
            'reference_id'     => $data['reference_id'] ?? null,
            'movement_date'    => $movementDate,
            'business_date'    => $businessDate,
            'remarks'          => $data['remarks'] ?? null,
            'created_by'       => $data['created_by'] ?? (Auth::id() ?? 1),
        ]);
    }

    /**
     * Resolve current active business date from day_closings table if open, or fallback date.
     *
     * @param string|null $fallbackDate
     * @return string
     */
    public function resolveBusinessDate(?string $fallbackDate = null): string
    {
        if (Schema::hasTable('day_closings')) {
            $closingDate = DB::table('day_closings')->where('status', 'open')->value('business_date');
            if ($closingDate) {
                return $closingDate;
            }
        }

        return $fallbackDate ?? now()->toDateString();
    }

    /**
     * Record stock inward movements for a completed Bulk Stock Inward.
     *
     * @param StockInward $stockInward
     * @return void
     */
    public function recordInward(StockInward $stockInward): void
    {
        $stockInward->loadMissing('items');

        foreach ($stockInward->items as $item) {
            $invoiceDateStr = $stockInward->invoice_date ? $stockInward->invoice_date->format('Y-m-d') : now()->toDateString();
            $this->recordMovement([
                'company_id'       => $stockInward->company_id,
                'branch_id'        => $stockInward->branch_id,
                'counter_id'       => $stockInward->counter_id,
                'product_id'       => $item->product_id,
                'stock_item_id'    => null,
                'movement_type'    => StockMovement::TYPE_PURCHASE,
                'transaction_type' => InventoryTransactionType::ALLOCATION->value,
                'quantity'         => (float) $item->qty,
                'unit_cost'        => $item->purchase_price,
                'reference_type'   => StockInward::class,
                'reference_id'     => $stockInward->id,
                'movement_date'    => $invoiceDateStr,
                'business_date'    => $this->resolveBusinessDate($invoiceDateStr),
                'remarks'          => "Bulk Inward Invoice '{$stockInward->invoice_no}'",
                'created_by'       => Auth::id() ?? $stockInward->created_by,
            ]);
        }
    }

    /**
     * Validate stock availability before completing a sale transaction.
     *
     * @param array $items
     * @param int $branchId
     * @return void
     * @throws ValidationException
     */
    public function checkAvailability(array $items, int $branchId): void
    {
        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $quantity = (float) ($item['quantity'] ?? 1);
            $itemType = (int) ($item['item_type'] ?? SalesDetail::ITEM_UNALLOCATED);
            $allocatedItemId = $item['allocated_item_id'] ?? null;

            $product = Product::find($productId);
            $productName = $product ? $product->name : ($item['product_name'] ?? 'Product');

            if ($itemType === SalesDetail::ITEM_ALLOCATED) {
                if (!$allocatedItemId) {
                    throw ValidationException::withMessages([
                        'items' => ["Allocated item selection missing for product {$productName}."],
                    ]);
                }

                $stockItem = StockItem::find($allocatedItemId);
                if (!$stockItem) {
                    throw ValidationException::withMessages([
                        'items' => ["Allocated stock item ID #{$allocatedItemId} for product {$productName} does not exist."],
                    ]);
                }

                // Status 5 represents SOLD
                if ($stockItem->status === \App\Enums\StockItemStatus::SOLD->value) {
                    throw ValidationException::withMessages([
                        'items' => ["Allocated item code {$stockItem->item_code} has already been sold."],
                    ]);
                }
            } else {
                // Unallocated quantity-based check
                $available = $this->getAvailableStockQuantity($productId, $branchId);
                if ($available < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for product '{$productName}'. Available: {$available}, Requested: {$quantity}."],
                    ]);
                }
            }
        }
    }

    /**
     * Reduce stock after successful sale invoice creation.
     *
     * @param Sale $sale
     * @return void
     */
    public function reduceStock(Sale $sale): void
    {
        $sale->loadMissing('details');

        foreach ($sale->details as $detail) {
            if ($detail->item_type === SalesDetail::ITEM_ALLOCATED) {
                $this->processAllocatedItem($detail, $sale);
            } else {
                $this->processUnallocatedItem($detail, $sale);
            }
        }
    }

    /**
     * Handle serialized / item-based inventory deduction.
     *
     * @param SalesDetail $salesDetail
     * @param Sale $sale
     * @return void
     * @throws ValidationException
     */
    public function processAllocatedItem(SalesDetail $salesDetail, Sale $sale): void
    {
        if (!$salesDetail->allocated_item_id) {
            return;
        }

        $stockItem = StockItem::findOrFail($salesDetail->allocated_item_id);

        if ($stockItem->status === \App\Enums\StockItemStatus::SOLD->value) {
            throw ValidationException::withMessages([
                'allocated_item_id' => ["Allocated item {$stockItem->item_code} is already marked as SOLD."],
            ]);
        }

        // Mark allocated inventory item as SOLD (Status = 5)
        $stockItem->update([
            'status' => \App\Enums\StockItemStatus::SOLD->value,
            'allocated_by' => Auth::id() ?? $sale->created_by,
            'allocated_at' => now(),
        ]);

        $stockItem->loadMissing('stockInwardItem');
        $unitCost = $stockItem->stockInwardItem ? $stockItem->stockInwardItem->purchase_price : null;
        $invoiceDateStr = $sale->invoice_date ? $sale->invoice_date->format('Y-m-d') : now()->toDateString();

        // Create negative stock movement entry for sale via recordMovement
        $this->recordMovement([
            'company_id'       => $sale->company_id,
            'branch_id'        => $sale->branch_id,
            'counter_id'       => $sale->counter_id,
            'product_id'       => $salesDetail->product_id,
            'stock_item_id'    => $stockItem->id,
            'movement_type'    => StockMovement::TYPE_SALE,
            'transaction_type' => InventoryTransactionType::SALES->value,
            'quantity'         => -1.00,
            'unit_cost'        => $unitCost,
            'reference_type'   => Sale::class,
            'reference_id'     => $sale->id,
            'movement_date'    => $invoiceDateStr,
            'business_date'    => $this->resolveBusinessDate($invoiceDateStr),
            'remarks'          => "Sales Invoice #{$sale->invoice_no_display}",
            'created_by'       => Auth::id() ?? $sale->created_by,
        ]);
    }

    /**
     * Handle unallocated quantity-based inventory deduction.
     *
     * @param SalesDetail $salesDetail
     * @param Sale $sale
     * @return void
     */
    public function processUnallocatedItem(SalesDetail $salesDetail, Sale $sale): void
    {
        $quantity = (float) $salesDetail->quantity;
        $invoiceDateStr = $sale->invoice_date ? $sale->invoice_date->format('Y-m-d') : now()->toDateString();

        // Create negative stock movement entry for sale quantity via recordMovement
        $this->recordMovement([
            'company_id'       => $sale->company_id,
            'branch_id'        => $sale->branch_id,
            'counter_id'       => $sale->counter_id,
            'product_id'       => $salesDetail->product_id,
            'stock_item_id'    => null,
            'movement_type'    => StockMovement::TYPE_SALE,
            'transaction_type' => InventoryTransactionType::SALES->value,
            'quantity'         => -abs($quantity),
            'unit_cost'        => $salesDetail->rate,
            'reference_type'   => Sale::class,
            'reference_id'     => $sale->id,
            'movement_date'    => $invoiceDateStr,
            'business_date'    => $this->resolveBusinessDate($invoiceDateStr),
            'remarks'          => "Sales Invoice #{$sale->invoice_no_display}",
            'created_by'       => Auth::id() ?? $sale->created_by,
        ]);
    }

    /**
     * Reverse stock movement when a sales invoice is cancelled.
     *
     * @param Sale $sale
     * @return void
     */
    public function reverseStock(Sale $sale): void
    {
        $sale->loadMissing('details');

        foreach ($sale->details as $detail) {
            if ($detail->item_type === SalesDetail::ITEM_ALLOCATED && $detail->allocated_item_id) {
                $stockItem = StockItem::find($detail->allocated_item_id);
                if ($stockItem) {
                    // Restore item status to Available (Status = 1)
                    $stockItem->update(['status' => \App\Enums\StockItemStatus::AVAILABLE->value]);
                }

                $this->recordMovement([
                    'company_id'       => $sale->company_id,
                    'branch_id'        => $sale->branch_id,
                    'counter_id'       => $sale->counter_id,
                    'product_id'       => $detail->product_id,
                    'stock_item_id'    => $detail->allocated_item_id,
                    'movement_type'    => StockMovement::TYPE_RETURN,
                    'transaction_type' => InventoryTransactionType::CANCELLED->value,
                    'quantity'         => 1.00,
                    'unit_cost'        => null,
                    'reference_type'   => Sale::class,
                    'reference_id'     => $sale->id,
                    'movement_date'    => now()->toDateString(),
                    'business_date'    => $this->resolveBusinessDate(now()->toDateString()),
                    'remarks'          => "Cancellation reversal of Sale Invoice #{$sale->invoice_no_display}",
                    'created_by'       => Auth::id() ?? $sale->created_by,
                ]);
            } else {
                $this->recordMovement([
                    'company_id'       => $sale->company_id,
                    'branch_id'        => $sale->branch_id,
                    'counter_id'       => $sale->counter_id,
                    'product_id'       => $detail->product_id,
                    'stock_item_id'    => null,
                    'movement_type'    => StockMovement::TYPE_RETURN,
                    'transaction_type' => InventoryTransactionType::CANCELLED->value,
                    'quantity'         => abs((float) $detail->quantity),
                    'unit_cost'        => $detail->rate,
                    'reference_type'   => Sale::class,
                    'reference_id'     => $sale->id,
                    'movement_date'    => now()->toDateString(),
                    'business_date'    => $this->resolveBusinessDate(now()->toDateString()),
                    'remarks'          => "Cancellation reversal of Sale Invoice #{$sale->invoice_no_display}",
                    'created_by'       => Auth::id() ?? $sale->created_by,
                ]);
            }
        }
    }

    /**
     * Calculate current net available stock quantity for a product in a branch.
     *
     * @param int $productId
     * @param int $branchId
     * @return float
     */
    public function getAvailableStockQuantity(int $productId, int $branchId): float
    {
        $product = Product::find($productId);
        if ($product && $product->tracking_type == 2) {
            return (float) StockItem::where('product_id', $productId)
                ->where('branch_id', $branchId)
                ->where('status', \App\Enums\StockItemStatus::AVAILABLE->value)
                ->count();
        }

        $netStock = StockMovement::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->sum('quantity');

        return (float) max(0, $netStock);
    }

    /**
     * Search available inventory stock (individual & bulk tracking).
     *
     * @param string $search
     * @param int|null $branchId
     * @param int $limit
     * @return array
     */
    public function search(string $search, ?int $branchId = null, int $limit = 20): array
    {
        $search = trim($search);
        if (strlen($search) < 2) {
            return [];
        }

        if (!$branchId && Auth::check()) {
            $branchId = Auth::user()->branch_id;
        }
        if (!$branchId) {
            $branchId = \App\Models\Branch::where('status', true)->value('id');
        }
        if (!$branchId) {
            return [];
        }

        $results = [];

        // 1. Search Individual Item Tracking
        $individualItems = StockItem::with(['product.uom', 'product.tax', 'stockInwardItem', 'branch'])
            ->where('status', \App\Enums\StockItemStatus::AVAILABLE->value)
            ->where('branch_id', $branchId)
            ->whereHas('product', function ($q) {
                $q->where('status', true)
                  ->where('tracking_type', Product::TRACKING_INDIVIDUAL);
            })
            ->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('code', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            })
            ->limit($limit)
            ->get();

        foreach ($individualItems as $item) {
            $rate = $item->stockInwardItem ? $item->stockInwardItem->selling_price : 0.00;
            $results[] = [
                'product_id'     => $item->product_id,
                'product_name'   => $item->product->name,
                'product_code'   => $item->product->code,
                'stock_item_id'  => $item->id,
                'item_code'      => $item->item_code,
                'available_qty'  => 1.00,
                'rate'           => (float) $rate,
                'tax_percent'    => $item->product->tax ? (float) $item->product->tax->percentage : 0.00,
                'uom_id'         => $item->product->uom_id,
                'uom_name'       => $item->product->uom ? $item->product->uom->name : '',
                'tracking_type'  => Product::TRACKING_INDIVIDUAL,
                'branch_id'      => $item->branch_id,
                'branch_name'    => $item->branch ? $item->branch->name : '',
            ];
        }
        // 2. Search Bulk Quantity Tracking
        $bulkProducts = Product::with(['uom', 'tax'])
            ->where('status', true)
            ->where('tracking_type', Product::TRACKING_QUANTITY)
            ->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            })
            ->limit($limit)
            ->get();
        foreach ($bulkProducts as $product) {
            $availableQty = $this->getAvailableStockQuantity($product->id, $branchId);
            if ($availableQty > 0) {
                $latestInwardItem = \App\Models\StockInwardItem::where('product_id', $product->id)
                    ->whereHas('stockInward', function ($q) use ($branchId) {
                        $q->where('branch_id', $branchId);
                    })
                    ->latest('id')
                    ->first();
                if (!$latestInwardItem) {
                    $latestInwardItem = \App\Models\StockInwardItem::where('product_id', $product->id)
                        ->latest('id')
                        ->first();
                }
                $rate = $latestInwardItem ? $latestInwardItem->selling_price : 0.00;
                $results[] = [
                    'product_id'     => $product->id,
                    'product_name'   => $product->name,
                    'product_code'   => $product->code,
                    'stock_item_id'  => null,
                    'item_code'      => null,
                    'available_qty'  => $availableQty,
                    'rate'           => (float) $rate,
                    'tax_percent'    => $product->tax ? (float) $product->tax->percentage : 0.00,
                    'uom_id'         => $product->uom_id,
                    'uom_name'       => $product->uom ? $product->uom->name : '',
                    'tracking_type'  => Product::TRACKING_QUANTITY,
                    'branch_id'      => $branchId,
                    'branch_name'    => '',
                ];
            }
        }
        return array_slice($results, 0, $limit);
    }
}
