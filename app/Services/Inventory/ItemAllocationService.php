<?php

namespace App\Services\Inventory;

use App\Enums\InventoryReferenceType;
use App\Enums\InventoryTransactionType;
use App\Enums\StockItemStatus;
use App\Models\Product;
use App\Models\ProductSequence;
use App\Models\StockItem;
use App\Models\StockItemLog;
use App\Models\StockInwardItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemAllocationService
{
    /**
     * Get remaining pending quantity for a stock inward detail line item.
     */
    public function getPendingQuantity(StockInwardItem $stockInwardItem): float
    {
        $allocatedCount = StockItem::where('stock_inward_item_id', $stockInwardItem->id)->count();
        return max(0, (float) $stockInwardItem->qty - $allocatedCount);
    }

    /**
     * Execute individual item allocation within a thread-safe database transaction.
     *
     * @param array $data ['stock_inward_item_id', 'counter_id', 'quantity', 'size_id', 'sub_product_id', 'user_id']
     * @return array Summary of allocation
     * @throws ValidationException|\InvalidArgumentException|\Exception
     */
    public function allocateItems(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Lock StockInwardItem row to prevent concurrent allocations exceeding quantity
            $stockInwardItem = StockInwardItem::with(['product', 'stockInward'])
                ->where('id', $data['stock_inward_item_id'])
                ->lockForUpdate()
                ->first();

            if (!$stockInwardItem) {
                throw new \InvalidArgumentException('Bulk Stock Inward line item not found.');
            }

            $product = $stockInwardItem->product;

            // Validate product configuration
            if ($product->tracking_type !== Product::TRACKING_INDIVIDUAL) {
                throw ValidationException::withMessages([
                    'stock_inward_item_id' => ['Selected product is not configured for Individual Item Tracking.'],
                ]);
            }

            $stockInward = $stockInwardItem->stockInward;
            if (!$stockInward) {
                throw new \InvalidArgumentException('Stock Inward header record missing.');
            }

            // Calculate current pending quantity
            $alreadyAllocated = StockItem::where('stock_inward_item_id', $stockInwardItem->id)->count();
            $pendingQty = (int) max(0, $stockInwardItem->qty - $alreadyAllocated);

            $requestedQty = (int) ($data['quantity'] ?? 1);

            // Handle Individual Generation Mode vs Bulk Generation Mode
            if ($product->item_generation_mode === Product::ITEM_GEN_INDIVIDUAL) {
                $requestedQty = 1;
            }

            if ($requestedQty <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => ['Allocation quantity must be greater than zero.'],
                ]);
            }

            if ($requestedQty > $pendingQty) {
                throw ValidationException::withMessages([
                    'quantity' => ["Requested quantity ({$requestedQty}) exceeds current pending quantity ({$pendingQty})."],
                ]);
            }

            // Lock Product Sequence row
            $sequence = ProductSequence::firstOrCreate(
                ['product_id' => $product->id],
                ['next_sequence' => 1]
            );

            $sequence = ProductSequence::where('id', $sequence->id)
                ->lockForUpdate()
                ->first();

            $currentSeq = (int) $sequence->next_sequence;
            $rawPrefix = strtoupper(trim($product->code ?? 'PROD'));
            $prefix = preg_replace('/[-\s\d]+$/', '', $rawPrefix);
            if (empty($prefix)) {
                $prefix = $rawPrefix;
            }

            $now = now();
            $userId = $data['user_id'] ?? auth()->id();
            $createdItemsData = [];

            for ($i = 0; $i < $requestedQty; $i++) {
                $seqNumber = $currentSeq + $i;
                $itemCode = $prefix . str_pad((string) $seqNumber, 5, '0', STR_PAD_LEFT);

                $createdItemsData[] = [
                    'stock_inward_id' => $stockInwardItem->stock_inward_id,
                    'stock_inward_item_id' => $stockInwardItem->id,
                    'product_id' => $stockInwardItem->product_id,
                    'branch_id' => $stockInward->branch_id,
                    'counter_id' => $data['counter_id'],
                    'sub_product_id' => $data['sub_product_id'] ?? $stockInwardItem->sub_product_id,
                    'size_id' => $data['size_id'] ?? null,
                    'item_code' => $itemCode,
                    'status' => StockItemStatus::AVAILABLE->value,
                    'allocated_by' => $userId,
                    'allocated_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Insert stock items
            StockItem::insert($createdItemsData);

            // Retrieve created stock items for log generation
            $insertedStockItems = StockItem::where('stock_inward_item_id', $stockInwardItem->id)
                ->whereIn('item_code', array_column($createdItemsData, 'item_code'))
                ->get();

            $createdLogsData = [];
            foreach ($insertedStockItems as $stockItem) {
                $createdLogsData[] = [
                    'stock_item_id' => $stockItem->id,
                    'transaction_type' => InventoryTransactionType::ALLOCATION->value,
                    'reference_type' => InventoryReferenceType::BULK_INWARD->value,
                    'reference_id' => $stockInward->id,
                    'branch_id' => $stockInward->branch_id,
                    'counter_id' => $data['counter_id'],
                    'remarks' => "Initial allocation from Bulk Inward Invoice '{$stockInward->invoice_no}'",
                    'created_by' => $userId,
                    'created_at' => $now,
                ];
            }

            StockItemLog::insert($createdLogsData);

            // Increment sequence
            $sequence->next_sequence = $currentSeq + $requestedQty;
            $sequence->save();

            return [
                'success' => true,
                'allocated_count' => $requestedQty,
                'remaining_pending' => $pendingQty - $requestedQty,
                'item_codes' => array_column($createdItemsData, 'item_code'),
            ];
        });
    }
}
