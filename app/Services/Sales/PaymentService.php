<?php

namespace App\Services\Sales;

use App\Models\CustomerReceivable;
use App\Models\PaymentAllocation;
use App\Models\Sale;
use App\Models\SalesPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected ReceivableService $receivableService;

    /**
     * PaymentService constructor.
     *
     * @param ReceivableService $receivableService
     */
    public function __construct(ReceivableService $receivableService)
    {
        $this->receivableService = $receivableService;
    }

    /**
     * Create a payment record against a sales invoice.
     *
     * @param Sale $sale
     * @param array $paymentData
     * @return SalesPayment
     */
    public function createPayment(Sale $sale, array $paymentData): SalesPayment
    {
        return DB::transaction(function () use ($sale, $paymentData) {
            $userId = Auth::id() ?? $sale->created_by;

            $payment = SalesPayment::create([
                'sales_id' => $sale->id,
                'payment_mode_id' => $paymentData['payment_mode_id'],
                'payment_date' => $paymentData['payment_date'] ?? now()->toDateString(),
                'amount' => $paymentData['amount'],
                'reference_no' => $paymentData['reference_no'] ?? null,
                'remarks' => $paymentData['remarks'] ?? null,
                'status' => SalesPayment::STATUS_COMPLETED,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Auto-allocate if allocations are requested or for credit sale receivables
            $allocationType = $paymentData['allocation_type'] ?? PaymentAllocation::TYPE_MANUAL;
            $allocationDetails = $paymentData['allocations'] ?? [];

            if (!empty($allocationDetails) || $allocationType === PaymentAllocation::TYPE_FIFO) {
                $this->allocatePayment($payment, $allocationDetails, $allocationType);
            } elseif ($sale->customerReceivable) {
                // Default allocation to invoice's own receivable
                $this->allocatePayment($payment, [
                    [
                        'customer_receivable_id' => $sale->customerReceivable->id,
                        'amount' => $payment->amount,
                    ]
                ], PaymentAllocation::TYPE_MANUAL);
            }

            return $payment;
        });
    }

    /**
     * Allocate payment amount against outstanding customer receivables.
     *
     * @param SalesPayment $payment
     * @param array $allocationDetails
     * @param int $type
     * @return array
     */
    public function allocatePayment(
        SalesPayment $payment,
        array $allocationDetails = [],
        int $type = PaymentAllocation::TYPE_MANUAL
    ): array {
        return DB::transaction(function () use ($payment, $allocationDetails, $type) {
            if ($payment->isCancelled()) {
                throw new \InvalidArgumentException("Cannot allocate cancelled payment.");
            }

            $userId = Auth::id() ?? $payment->created_by;
            $allocationsCreated = [];
            $unallocatedAmount = $payment->amount;

            if ($type === PaymentAllocation::TYPE_FIFO) {
                // Fetch pending/partially paid receivables for customer ordered by due_date ASC
                $customerReceivables = CustomerReceivable::where('customer_id', $payment->sale->customer_id)
                    ->whereIn('status', [CustomerReceivable::STATUS_PENDING, CustomerReceivable::STATUS_PARTIALLY_PAID])
                    ->orderBy('due_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($customerReceivables as $receivable) {
                    if ($unallocatedAmount <= 0) {
                        break;
                    }

                    $applyAmount = min($unallocatedAmount, $receivable->balance_amount);
                    if ($applyAmount <= 0) {
                        continue;
                    }

                    $allocation = PaymentAllocation::create([
                        'sales_payment_id' => $payment->id,
                        'customer_receivable_id' => $receivable->id,
                        'allocated_amount' => $applyAmount,
                        'allocation_date' => $payment->payment_date,
                        'allocation_type' => PaymentAllocation::TYPE_FIFO,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);

                    $this->receivableService->allocateAmount($receivable, $applyAmount);
                    $allocationsCreated[] = $allocation;
                    $unallocatedAmount -= $applyAmount;
                }
            } else { // TYPE_MANUAL or TYPE_ADJUSTMENT
                foreach ($allocationDetails as $detail) {
                    $receivableId = $detail['customer_receivable_id'];
                    $amount = (float) $detail['amount'];

                    if ($amount <= 0) {
                        continue;
                    }

                    $receivable = CustomerReceivable::findOrFail($receivableId);

                    $allocation = PaymentAllocation::create([
                        'sales_payment_id' => $payment->id,
                        'customer_receivable_id' => $receivable->id,
                        'allocated_amount' => $amount,
                        'allocation_date' => $payment->payment_date,
                        'allocation_type' => $type,
                        'remarks' => $detail['remarks'] ?? null,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);

                    $this->receivableService->allocateAmount($receivable, $amount);
                    $allocationsCreated[] = $allocation;
                }
            }

            return $allocationsCreated;
        });
    }

    /**
     * Cancel a payment record and rollback all associated allocations.
     *
     * @param SalesPayment $payment
     * @param int|null $cancelledBy
     * @param string|null $reason
     * @return SalesPayment
     */
    public function cancelPayment(SalesPayment $payment, ?int $cancelledBy = null, ?string $reason = null): SalesPayment
    {
        return DB::transaction(function () use ($payment, $cancelledBy, $reason) {
            if ($payment->isCancelled()) {
                throw new \InvalidArgumentException("Payment is already cancelled.");
            }

            $cancellerId = $cancelledBy ?? Auth::id() ?? $payment->created_by;

            // Rollback payment allocations
            foreach ($payment->paymentAllocations as $allocation) {
                $receivable = $allocation->customerReceivable;
                if ($receivable) {
                    $receivable->paid_amount = max(0, round($receivable->paid_amount - $allocation->allocated_amount, 2));
                    $this->receivableService->updateBalance($receivable);
                }
            }

            $payment->update([
                'status' => SalesPayment::STATUS_CANCELLED,
                'cancelled_by' => $cancellerId,
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
                'updated_by' => $cancellerId,
            ]);

            return $payment;
        });
    }
}
