<?php

namespace App\Services\Sales;

use App\Models\SalesPayment;
use App\Models\SalesReturn;
use Illuminate\Support\Facades\Auth;

class RefundService
{
    /**
     * Record a refund payout log as a negative payment in the sales_payments table.
     *
     * @param SalesReturn $return
     * @param int $paymentModeId
     * @param float $amount
     * @param string|null $referenceNo
     * @param string|null $remarks
     * @param int|null $userId
     * @return SalesPayment
     */
    public function recordRefund(
        SalesReturn $return,
        int $paymentModeId,
        float $amount,
        ?string $referenceNo = null,
        ?string $remarks = null,
        ?int $userId = null
    ): SalesPayment {
        $creatorId = $userId ?? Auth::id() ?? $return->created_by;

        return SalesPayment::create([
            'sales_id' => $return->sales_id,
            'sales_return_id' => $return->id,
            'payment_mode_id' => $paymentModeId,
            'payment_date' => $return->return_date,
            'amount' => -abs($amount), // Force negative cash flow
            'reference_no' => $referenceNo,
            'remarks' => $remarks ?? "Refund payout for Sales Return #{$return->return_no_display}",
            'status' => SalesPayment::STATUS_COMPLETED,
            'created_by' => $creatorId,
            'updated_by' => $creatorId,
        ]);
    }
}
