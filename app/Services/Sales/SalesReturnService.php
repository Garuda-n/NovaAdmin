<?php

namespace App\Services\Sales;

use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\StockItem;
use App\Enums\StockItemStatus;
use App\Models\StockMovement;
use App\Models\CustomerReceivable;
use App\Enums\InventoryTransactionType;
use App\Enums\InventoryReferenceType;
use App\Services\Inventory\InventoryService;
use App\Services\SettingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesReturnService
{
    protected ReturnValidationService $validationService;
    protected InventoryService $inventoryService;
    protected ReceivableService $receivableService;
    protected RefundService $refundService;

    public function __construct(
        ReturnValidationService $validationService,
        InventoryService $inventoryService,
        ReceivableService $receivableService,
        RefundService $refundService
    ) {
        $this->validationService = $validationService;
        $this->inventoryService = $inventoryService;
        $this->receivableService = $receivableService;
        $this->refundService = $refundService;
    }

    /**
     * Process customer return transaction.
     *
     * @param array $data
     * @param int|null $userId
     * @return SalesReturn
     */
    public function createReturn(array $data, ?int $userId = null): SalesReturn
    {
        $creatorId = $userId ?? Auth::id() ?? 1;

        return DB::transaction(function () use ($data, $creatorId) {
            // 1. Perform Business and Schema Validations
            $this->validationService->validate($data);

            $sale = Sale::findOrFail($data['sales_id']);
            $companyId = $sale->branch->company_id ?? 1;
            $branchId = $sale->branch_id;

            $returnNo = $this->generateNextReturnNo();
            $returnNoDisplay = $this->formatReturnNoDisplay($returnNo, $branchId);
            $returnDate = $data['return_date'] ?? now()->toDateString();
            $businessDate = $this->inventoryService->resolveBusinessDate($returnDate);

            // 2. Compute Lines & Header Totals
            $lineDetails = [];
            $headerSubtotal = 0.00;
            $headerItemDiscount = 0.00;
            $headerInvoiceDiscount = 0.00;
            $headerCgstAmount = 0.00;
            $headerSgstAmount = 0.00;
            $headerIgstAmount = 0.00;
            $headerTaxAmount = 0.00;

            $items = $data['items'] ?? [];
            foreach ($items as $item) {
                $detail = SalesDetail::findOrFail($item['sales_detail_id']);
                $qtyToReturn = (float) $item['returned_quantity'];

                // Mathematical formulas according to SFS
                $lineSubtotal = round($qtyToReturn * $detail->rate, 2);

                // Pro-rata line-item discount
                $lineItemDiscount = 0.00;
                if ($detail->quantity > 0) {
                    $lineItemDiscount = round($detail->discount_amount * ($qtyToReturn / $detail->quantity), 2);
                }

                // Pro-rata global invoice discount
                $lineInvoiceDiscount = 0.00;
                if ($sale->subtotal > 0) {
                    $lineInvoiceDiscount = round(($lineSubtotal / $sale->subtotal) * $sale->invoice_discount, 2);
                }

                $taxableValue = round($lineSubtotal - $lineItemDiscount - $lineInvoiceDiscount, 2);

                // Re-calculate GST based on original tax configuration
                $cgstAmount = 0.00;
                $sgstAmount = 0.00;
                $igstAmount = 0.00;

                if ($sale->gst_type == Sale::GST_CGST_SGST) {
                    $cgstAmount = round($taxableValue * ($detail->cgst_percentage / 100), 2);
                    $sgstAmount = round($taxableValue * ($detail->sgst_percentage / 100), 2);
                } elseif ($sale->gst_type == Sale::GST_IGST) {
                    $igstAmount = round($taxableValue * ($detail->igst_percentage / 100), 2);
                }

                $lineTaxAmount = round($cgstAmount + $sgstAmount + $igstAmount, 2);
                $lineTotal = round($taxableValue + $lineTaxAmount, 2);

                // Add to header accumulators
                $headerSubtotal += $lineSubtotal;
                $headerItemDiscount += $lineItemDiscount;
                $headerInvoiceDiscount += $lineInvoiceDiscount;
                $headerCgstAmount += $cgstAmount;
                $headerSgstAmount += $sgstAmount;
                $headerIgstAmount += $igstAmount;
                $headerTaxAmount += $lineTaxAmount;

                $lineDetails[] = [
                    'sales_detail_id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'uom_id' => $detail->uom_id,
                    'original_stock_item_id' => $detail->item_type === SalesDetail::ITEM_ALLOCATED ? $detail->allocated_item_id : null,
                    'recreated_stock_item_id' => null, // Left null until warehouse recreation phase
                    'item_type' => $detail->item_type,
                    'returned_quantity' => $qtyToReturn,
                    'rate' => $detail->rate,
                    'discount_amount' => round($lineItemDiscount + $lineInvoiceDiscount, 2),
                    'tax_percentage' => $detail->tax_percentage,
                    'cgst_percentage' => $detail->cgst_percentage,
                    'cgst_amount' => $cgstAmount,
                    'sgst_percentage' => $detail->sgst_percentage,
                    'sgst_amount' => $sgstAmount,
                    'igst_percentage' => $detail->igst_percentage,
                    'igst_amount' => $igstAmount,
                    'tax_amount' => $lineTaxAmount,
                    'line_total' => $lineTotal,
                ];
            }

            // Apply round off matching sale round off logic
            $exactGrandTotal = round($headerSubtotal - $headerItemDiscount - $headerInvoiceDiscount + $headerTaxAmount, 2);
            $roundedGrandTotal = round($exactGrandTotal);
            $roundOff = round($roundedGrandTotal - $exactGrandTotal, 2);

            // 3. Save Sales Return Header
            $salesReturn = SalesReturn::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'counter_id' => $sale->counter_id,
                'sales_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'sales_person_id' => $sale->sales_person_id,
                'return_no' => $returnNo,
                'return_no_display' => $returnNoDisplay,
                'return_date' => $returnDate,
                'business_date' => $businessDate,
                'gst_type' => $sale->gst_type,
                'subtotal' => $headerSubtotal,
                'item_discount' => $headerItemDiscount,
                'invoice_discount' => $headerInvoiceDiscount,
                'cgst_amount' => $headerCgstAmount,
                'sgst_amount' => $headerSgstAmount,
                'igst_amount' => $headerIgstAmount,
                'tax_amount' => $headerTaxAmount,
                'round_off' => $roundOff,
                'grand_total' => $roundedGrandTotal,
                'status' => SalesReturn::STATUS_COMPLETED,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => $creatorId,
                'updated_by' => $creatorId,
            ]);

            // 4. Save Lines and Replenish Stock Movements
            foreach ($lineDetails as $line) {
                $line['sales_return_id'] = $salesReturn->id;
                $salesReturnDetail = SalesReturnDetail::create($line);

                if ($line['item_type'] === SalesDetail::ITEM_ALLOCATED) {
                    // Update original stock item status to RETURNED (9)
                    $stockItem = StockItem::findOrFail($line['original_stock_item_id']);
                    $stockItem->update([
                        'status' => StockItemStatus::RETURNED->value,
                    ]);

                    // Add log history for status update
                    DB::table('stock_item_logs')->insert([
                        'stock_item_id' => $stockItem->id,
                        'transaction_type' => InventoryTransactionType::SALES_RETURN->value,
                        'reference_type' => InventoryReferenceType::SALES_RETURN->value,
                        'reference_id' => $salesReturn->id,
                        'branch_id' => $salesReturn->branch_id,
                        'counter_id' => $salesReturn->counter_id,
                        'remarks' => "Returned from Sale Invoice #{$sale->invoice_no_display}",
                        'created_by' => $creatorId,
                        'created_at' => now(),
                    ]);

                    // Fetch original FIFO cost for cost consistency
                    $stockItem->loadMissing('stockInwardItem');
                    $unitCost = $stockItem->stockInwardItem ? $stockItem->stockInwardItem->purchase_price : null;

                    // Log positive stock movement ledger entry
                    $this->inventoryService->recordMovement([
                        'company_id' => $salesReturn->company_id,
                        'branch_id' => $salesReturn->branch_id,
                        'counter_id' => $salesReturn->counter_id,
                        'product_id' => $line['product_id'],
                        'stock_item_id' => $stockItem->id,
                        'movement_type' => StockMovement::TYPE_RETURN,
                        'transaction_type' => InventoryTransactionType::SALES_RETURN->value,
                        'quantity' => 1.00,
                        'unit_cost' => $unitCost,
                        'reference_type' => SalesReturn::class,
                        'reference_id' => $salesReturn->id,
                        'movement_date' => $returnDate,
                        'business_date' => $businessDate,
                        'remarks' => "Customer Return Invoice #{$salesReturn->return_no_display}",
                        'created_by' => $creatorId,
                    ]);
                } else {
                    // Log unallocated quantity-based stock movement ledger entry
                    $this->inventoryService->recordMovement([
                        'company_id' => $salesReturn->company_id,
                        'branch_id' => $salesReturn->branch_id,
                        'counter_id' => $salesReturn->counter_id,
                        'product_id' => $line['product_id'],
                        'stock_item_id' => null,
                        'movement_type' => StockMovement::TYPE_RETURN,
                        'transaction_type' => InventoryTransactionType::SALES_RETURN->value,
                        'quantity' => $line['returned_quantity'],
                        'unit_cost' => $line['rate'],
                        'reference_type' => SalesReturn::class,
                        'reference_id' => $salesReturn->id,
                        'movement_date' => $returnDate,
                        'business_date' => $businessDate,
                        'remarks' => "Customer Return Invoice #{$salesReturn->return_no_display}",
                        'created_by' => $creatorId,
                    ]);
                }
            }

            // 5. Accounting Adjustment
            if ($sale->sale_type == Sale::TYPE_CREDIT) {
                // Apply credit offset directly to receivable balance
                $receivable = CustomerReceivable::where('sales_id', $sale->id)
                    ->lockForUpdate()
                    ->first();

                if ($receivable) {
                    $receivable->paid_amount = round($receivable->paid_amount + $roundedGrandTotal, 2);
                    $this->receivableService->updateBalance($receivable);
                }
            } elseif ($sale->sale_type == Sale::TYPE_CASH) {
                // Cash refund payment registration
                $paymentModeId = $data['payment_mode_id'] ?? 1; // Fallback to Cash
                $referenceNo = $data['reference_no'] ?? null;
                $remarks = $data['remarks'] ?? null;

                $this->refundService->recordRefund(
                    $salesReturn,
                    $paymentModeId,
                    $roundedGrandTotal,
                    $referenceNo,
                    $remarks,
                    $creatorId
                );
            }

            return $salesReturn;
        });
    }

    /**
     * Generate next sequential number for returns.
     */
    protected function generateNextReturnNo(): int
    {
        $maxNo = SalesReturn::lockForUpdate()->max('return_no');
        return ($maxNo ? (int) $maxNo : 0) + 1;
    }

    /**
     * Format return display number using branch setting templates.
     */
    protected function formatReturnNoDisplay(int $returnNo, ?int $branchId = null): string
    {
        $prefix = SettingService::get('sales_return_prefix', 'SR');
        $paddedNo = str_pad((string) $returnNo, 5, '0', STR_PAD_LEFT);

        if ($branchId) {
            $branchCode = \App\Models\Branch::where('id', $branchId)->value('branch_code');
            if ($branchCode) {
                return "{$prefix}/{$branchCode}/{$paddedNo}";
            }
        }

        return "{$prefix}-{$paddedNo}";
    }
}
