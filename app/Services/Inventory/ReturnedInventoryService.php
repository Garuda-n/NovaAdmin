<?php

namespace App\Services\Inventory;

use App\Models\SalesReturnDetail;
use App\Models\StockItem;
use App\Models\ProductSequence;
use App\Models\Product;
use App\Enums\StockItemStatus;
use App\Enums\InventoryTransactionType;
use App\Enums\InventoryReferenceType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnedInventoryService
{
    /**
     * Recreate inventory for a returned serialized stock item.
     *
     * @param int $salesReturnDetailId
     * @param int $userId
     * @return StockItem
     * @throws ValidationException
     */
    public function recreate(int $salesReturnDetailId, int $userId): StockItem
    {
        return DB::transaction(function () use ($salesReturnDetailId, $userId) {
            // 1. Lock the detail line to prevent concurrent recreations
            $detail = SalesReturnDetail::where('id', $salesReturnDetailId)
                ->lockForUpdate()
                ->first();

            if (!$detail) {
                throw new \InvalidArgumentException('Sales return detail line not found.');
            }

            if ($detail->item_type !== 1) { // 1 = Allocated/Serialized
                throw ValidationException::withMessages([
                    'sales_return_detail' => ['Inventory recreation is only supported for allocated serialized items.'],
                ]);
            }

            if ($detail->recreated_stock_item_id !== null) {
                throw ValidationException::withMessages([
                    'sales_return_detail' => ['A recreated stock item has already been generated for this return line.'],
                ]);
            }

            $originalStockItem = StockItem::with('product')->find($detail->original_stock_item_id);
            if (!$originalStockItem) {
                throw new \InvalidArgumentException('Original returned stock item was not found.');
            }

            if ($originalStockItem->status !== StockItemStatus::RETURNED->value) {
                throw ValidationException::withMessages([
                    'sales_return_detail' => ["Original stock item '{$originalStockItem->item_code}' is not in RETURNED status."],
                ]);
            }

            // 2. Lock sequence block for the product
            $sequence = ProductSequence::firstOrCreate(
                ['product_id' => $detail->product_id],
                ['next_sequence' => 1]
            );

            $sequence = ProductSequence::where('id', $sequence->id)
                ->lockForUpdate()
                ->first();

            $currentSeq = (int) $sequence->next_sequence;

            // Extract prefix according to standard ItemAllocationService logic
            $rawPrefix = strtoupper(trim($originalStockItem->product->code ?? 'PROD'));
            $prefix = preg_replace('/[-\s\d]+$/', '', $rawPrefix);
            if (empty($prefix)) {
                $prefix = $rawPrefix;
            }

            $now = now();
            $newItemCode = $prefix . str_pad((string) $currentSeq, 5, '0', STR_PAD_LEFT);

            // 3. Create the recreated stock item
            $recreatedStockItem = StockItem::create([
                'stock_inward_id' => $originalStockItem->stock_inward_id,
                'stock_inward_item_id' => $originalStockItem->stock_inward_item_id,
                'product_id' => $originalStockItem->product_id,
                'branch_id' => $originalStockItem->branch_id,
                'counter_id' => $originalStockItem->counter_id,
                'sub_product_id' => $originalStockItem->sub_product_id,
                'size_id' => $originalStockItem->size_id,
                'item_code' => $newItemCode,
                'status' => StockItemStatus::AVAILABLE->value,
                'allocated_by' => $userId,
                'allocated_at' => $now,
            ]);

            // 4. Update return details mapping link
            $detail->update([
                'recreated_stock_item_id' => $recreatedStockItem->id,
            ]);

            // 5. Add logs
            // Log for the new item allocation
            DB::table('stock_item_logs')->insert([
                'stock_item_id' => $recreatedStockItem->id,
                'transaction_type' => InventoryTransactionType::SALES_RETURN->value,
                'reference_type' => InventoryReferenceType::SALES_RETURN->value,
                'reference_id' => $detail->sales_return_id,
                'branch_id' => $recreatedStockItem->branch_id,
                'counter_id' => $recreatedStockItem->counter_id,
                'remarks' => "Recreated from returned item '{$originalStockItem->item_code}'",
                'created_by' => $userId,
                'created_at' => $now,
            ]);

            // Log for the original item stating it has been recreated
            DB::table('stock_item_logs')->insert([
                'stock_item_id' => $originalStockItem->id,
                'transaction_type' => InventoryTransactionType::SALES_RETURN->value,
                'reference_type' => InventoryReferenceType::SALES_RETURN->value,
                'reference_id' => $detail->sales_return_id,
                'branch_id' => $originalStockItem->branch_id,
                'counter_id' => $originalStockItem->counter_id,
                'remarks' => "Re-allocated and recreated as '{$newItemCode}'",
                'created_by' => $userId,
                'created_at' => $now,
            ]);

            // 6. Increment sequence number
            $sequence->next_sequence = $currentSeq + 1;
            $sequence->save();

            return $recreatedStockItem;
        });
    }
}
