<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InventoryService
{
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

        // Create negative stock movement entry for sale
        StockMovement::create([
            'company_id' => $sale->company_id,
            'branch_id' => $sale->branch_id,
            'product_id' => $salesDetail->product_id,
            'stock_item_id' => $stockItem->id,
            'movement_type' => StockMovement::TYPE_SALE,
            'quantity' => -1.00,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'movement_date' => $sale->invoice_date,
            'created_by' => Auth::id() ?? $sale->created_by,
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

        // Create negative stock movement entry for sale quantity
        StockMovement::create([
            'company_id' => $sale->company_id,
            'branch_id' => $sale->branch_id,
            'product_id' => $salesDetail->product_id,
            'stock_item_id' => null,
            'movement_type' => StockMovement::TYPE_SALE,
            'quantity' => -abs($quantity),
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'movement_date' => $sale->invoice_date,
            'created_by' => Auth::id() ?? $sale->created_by,
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

                StockMovement::create([
                    'company_id' => $sale->company_id,
                    'branch_id' => $sale->branch_id,
                    'product_id' => $detail->product_id,
                    'stock_item_id' => $detail->allocated_item_id,
                    'movement_type' => StockMovement::TYPE_RETURN,
                    'quantity' => 1.00,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'movement_date' => now()->toDateString(),
                    'created_by' => Auth::id() ?? $sale->created_by,
                ]);
            } else {
                StockMovement::create([
                    'company_id' => $sale->company_id,
                    'branch_id' => $sale->branch_id,
                    'product_id' => $detail->product_id,
                    'stock_item_id' => null,
                    'movement_type' => StockMovement::TYPE_RETURN,
                    'quantity' => abs((float) $detail->quantity),
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'movement_date' => now()->toDateString(),
                    'created_by' => Auth::id() ?? $sale->created_by,
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
