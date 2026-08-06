<?php

namespace App\Services\Sales;

use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\StockItem;
use App\Enums\StockItemStatus;
use Illuminate\Validation\ValidationException;

class ReturnValidationService
{
    protected ReturnPolicyService $policyService;

    public function __construct(ReturnPolicyService $policyService)
    {
        $this->policyService = $policyService;
    }

    /**
     * Perform exhaustive validations on the Sales Return payload.
     *
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $salesId = $data['sales_id'] ?? null;
        $returnDate = $data['return_date'] ?? now()->toDateString();

        if (!$salesId) {
            throw ValidationException::withMessages([
                'sales_id' => ['The sales invoice reference is required.'],
            ]);
        }

        $sale = Sale::find($salesId);
        if (!$sale) {
            throw ValidationException::withMessages([
                'sales_id' => ['The referenced sales invoice was not found.'],
            ]);
        }

        if ($sale->status !== Sale::STATUS_COMPLETED) {
            throw ValidationException::withMessages([
                'sales_id' => ['Returns can only be created against completed sales invoices.'],
            ]);
        }

        $items = $data['items'] ?? [];
        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => ['At least one item must be selected for return.'],
            ]);
        }

        foreach ($items as $index => $item) {
            $salesDetailId = $item['sales_detail_id'] ?? null;
            $qtyToReturn = (float) ($item['returned_quantity'] ?? 0);

            if (!$salesDetailId) {
                throw ValidationException::withMessages([
                    "items.{$index}.sales_detail_id" => ['Line item reference is missing.'],
                ]);
            }

            $detail = SalesDetail::with('product')->find($salesDetailId);
            if (!$detail || $detail->sales_id !== $sale->id) {
                throw ValidationException::withMessages([
                    "items.{$index}.sales_detail_id" => ['Invalid sales invoice detail line reference.'],
                ]);
            }

            if ($qtyToReturn <= 0) {
                throw ValidationException::withMessages([
                    "items.{$index}.returned_quantity" => ['Returned quantity must be greater than zero.'],
                ]);
            }

            // 1. Check cumulative quantity constraints
            $previouslyReturned = (float) SalesReturnDetail::whereHas('salesReturn', function ($query) use ($sale) {
                $query->where('sales_id', $sale->id)
                    ->where('status', SalesReturn::STATUS_COMPLETED);
            })->where('sales_detail_id', $detail->id)->sum('returned_quantity');

            $availableToReturn = $detail->quantity - $previouslyReturned;

            if ($qtyToReturn > $availableToReturn) {
                throw ValidationException::withMessages([
                    "items.{$index}.returned_quantity" => [
                        "Quantity to return ({$qtyToReturn}) exceeds available return limit for product '{$detail->product_name}'. Sold: {$detail->quantity}, Already Returned: {$previouslyReturned}, Max returnable: {$availableToReturn}."
                    ],
                ]);
            }

            // 2. Policy Timelines Check
            $this->policyService->validateCategoryPolicy($detail->product, $returnDate, $sale->invoice_date->toDateString());

            // 3. Serialized validation check
            if ($detail->item_type === SalesDetail::ITEM_ALLOCATED) {
                if ($qtyToReturn != 1.00) {
                    throw ValidationException::withMessages([
                        "items.{$index}.returned_quantity" => ['Serialized items must be returned with a quantity of exactly 1.'],
                    ]);
                }

                $originalStockItem = StockItem::find($detail->allocated_item_id);
                if (!$originalStockItem) {
                    throw ValidationException::withMessages([
                        "items.{$index}.allocated_item_id" => ['Allocated serial stock unit reference is missing on invoice detail.'],
                    ]);
                }

                if ($originalStockItem->status !== StockItemStatus::SOLD->value) {
                    throw ValidationException::withMessages([
                        "items.{$index}.allocated_item_id" => ["Selected stock item '{$originalStockItem->item_code}' is not in SOLD status."],
                    ]);
                }
            }
        }
    }
}
