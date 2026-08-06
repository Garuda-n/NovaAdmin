<!-- Invoice Payment Partial -->
<div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; margin-bottom: 20px; font-size: 11px; background-color: #f8fafc;">
    <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #475569; margin-bottom: 6px;">
        Payment & Settlement Summary
    </div>

    @if ($sale->isCashSale())
        <table style="width: 100%; border-collapse: collapse; color: #1e293b;">
            <tr>
                <td colspan="4" style="padding-bottom: 6px;"><strong>Payment Type:</strong> Instant Cash / Settlement</td>
            </tr>
            <tr style="background-color: #e2e8f0;">
                <td style="padding: 4px 6px; font-weight: bold; border: 1px solid #cbd5e1;">#</td>
                <td style="padding: 4px 6px; font-weight: bold; border: 1px solid #cbd5e1;">Payment Mode</td>
                <td style="padding: 4px 6px; font-weight: bold; border: 1px solid #cbd5e1;">Reference / Txn ID</td>
                <td style="padding: 4px 6px; font-weight: bold; border: 1px solid #cbd5e1; text-align: right;">Amount (₹)</td>
            </tr>
            @php $paymentTotal = 0; @endphp
            @foreach ($sale->salesPayments as $idx => $payment)
                @if (!$payment->isCancelled())
                    @php $paymentTotal += (float) $payment->amount; @endphp
                    <tr>
                        <td style="padding: 4px 6px; border: 1px solid #cbd5e1;">{{ $idx + 1 }}</td>
                        <td style="padding: 4px 6px; border: 1px solid #cbd5e1;">{{ $payment->paymentMode->mode_name ?? 'Cash' }}</td>
                        <td style="padding: 4px 6px; border: 1px solid #cbd5e1; color: #64748b;">{{ $payment->reference_no ?? '—' }}</td>
                        <td style="padding: 4px 6px; border: 1px solid #cbd5e1; text-align: right;">₹{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr style="background-color: #f1f5f9;">
                <td colspan="3" style="padding: 4px 6px; border: 1px solid #cbd5e1; font-weight: bold; text-align: right;">Total Paid</td>
                <td style="padding: 4px 6px; border: 1px solid #cbd5e1; font-weight: bold; text-align: right;">₹{{ number_format($paymentTotal, 2) }}</td>
            </tr>
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
