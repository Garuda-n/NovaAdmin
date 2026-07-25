<?php

namespace App\Services\Sales;

use App\Models\CustomerReceivable;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class ReceivableService
{
    /**
     * Create customer receivable entry for a Credit Sale invoice.
     *
     * @param Sale $sale
     * @return CustomerReceivable
     */
    public function createReceivable(Sale $sale): CustomerReceivable
    {
        if (!$sale->isCreditSale()) {
            throw new InvalidArgumentException("Customer receivable can only be created for Credit Sales.");
        }

        $userId = Auth::id() ?? $sale->created_by;

        return CustomerReceivable::create([
            'sales_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'invoice_date' => $sale->invoice_date,
            'due_date' => $sale->due_date ?? $sale->invoice_date,
            'original_amount' => $sale->grand_total,
            'paid_amount' => 0.00,
            'balance_amount' => $sale->grand_total,
            'status' => CustomerReceivable::STATUS_PENDING,
            'remarks' => $sale->remarks,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    /**
     * Allocate payment amount towards a receivable.
     *
     * @param CustomerReceivable $receivable
     * @param float $amount
     * @return CustomerReceivable
     */
    public function allocateAmount(CustomerReceivable $receivable, float $amount): CustomerReceivable
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Allocation amount must be greater than zero.");
        }

        $newPaidAmount = round($receivable->paid_amount + $amount, 2);
        $receivable->paid_amount = min($receivable->original_amount, $newPaidAmount);

        return $this->updateBalance($receivable);
    }

    /**
     * Recalculate balance amount and update status.
     *
     * @param CustomerReceivable $receivable
     * @return CustomerReceivable
     */
    public function updateBalance(CustomerReceivable $receivable): CustomerReceivable
    {
        $balance = round(max(0, $receivable->original_amount - $receivable->paid_amount), 2);
        $receivable->balance_amount = $balance;

        if ($receivable->status !== CustomerReceivable::STATUS_CANCELLED) {
            if ($balance <= 0) {
                $receivable->status = CustomerReceivable::STATUS_PAID;
            } elseif ($receivable->paid_amount > 0) {
                $receivable->status = CustomerReceivable::STATUS_PARTIALLY_PAID;
            } else {
                $receivable->status = CustomerReceivable::STATUS_PENDING;
            }
        }

        if (Auth::check()) {
            $receivable->updated_by = Auth::id();
        }

        $receivable->save();

        return $receivable;
    }

    /**
     * Mark receivable as fully paid.
     *
     * @param CustomerReceivable $receivable
     * @return CustomerReceivable
     */
    public function markAsPaid(CustomerReceivable $receivable): CustomerReceivable
    {
        $receivable->paid_amount = $receivable->original_amount;
        $receivable->balance_amount = 0.00;
        $receivable->status = CustomerReceivable::STATUS_PAID;

        if (Auth::check()) {
            $receivable->updated_by = Auth::id();
        }

        $receivable->save();

        return $receivable;
    }

    /**
     * Cancel a receivable entry.
     *
     * @param CustomerReceivable $receivable
     * @return CustomerReceivable
     */
    public function cancelReceivable(CustomerReceivable $receivable): CustomerReceivable
    {
        $receivable->status = CustomerReceivable::STATUS_CANCELLED;

        if (Auth::check()) {
            $receivable->updated_by = Auth::id();
        }

        $receivable->save();

        return $receivable;
    }
}
