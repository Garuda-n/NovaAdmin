<!-- Invoice Payment Partial -->
<div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; margin-bottom: 20px; font-size: 11px; background-color: #f8fafc;">
    <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #475569; margin-bottom: 6px;">
        Payment & Settlement Summary
    </div>

    @if ($sale->isCashSale())
        @php $payment = $sale->salesPayments->first(); @endphp
        <table style="width: 100%; border-collapse: collapse; color: #1e293b;">
            <tr>
                <td style="width: 33%;"><strong>Payment Type:</strong> Instant Cash / Settlement</td>
                <td style="width: 33%;"><strong>Payment Mode:</strong> {{ $payment->paymentMode->mode_name ?? 'Cash' }}</td>
                <td style="width: 34%; text-align: right;"><strong>Paid Amount:</strong> ₹{{ number_format($payment->amount ?? $sale->grand_total, 2) }}</td>
            </tr>
            @if(isset($payment->reference_no) && $payment->reference_no)
                <tr>
                    <td colspan="3" style="padding-top: 4px; color: #64748b;">
                        <strong>Reference / Txn ID:</strong> {{ $payment->reference_no }}
                    </td>
                </tr>
            @endif
        </table>
    @else
        @php $receivable = $sale->customerReceivable; @endphp
        <table style="width: 100%; border-collapse: collapse; color: #1e293b;">
            <tr>
                <td style="width: 25%;"><strong>Sale Terms:</strong> Credit Sale</td>
                <td style="width: 25%;"><strong>Due Date:</strong> {{ $sale->due_date ? $sale->due_date->format('d/m/Y') : 'N/A' }}</td>
                <td style="width: 25%;"><strong>Paid Amount:</strong> ₹{{ number_format($receivable->paid_amount ?? 0, 2) }}</td>
                <td style="width: 25%; text-align: right;"><strong>Outstanding Balance:</strong> <span style="color: #dc2626; font-weight: bold;">₹{{ number_format($receivable->balance_amount ?? $sale->grand_total, 2) }}</span></td>
            </tr>
        </table>
    @endif
</div>
