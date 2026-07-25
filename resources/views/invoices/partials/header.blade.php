<!-- Invoice Header Partial -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <tr>
        <td style="width: 55%; vertical-align: top;">
            <div style="font-size: 22px; font-weight: bold; color: #1e293b; margin-bottom: 4px;">
                {{ $companyName }}
            </div>
            <div style="font-size: 11px; color: #64748b; line-height: 1.4;">
                {{ $companyAddress }}<br>
                <strong>GSTIN:</strong> {{ $companyGst }}<br>
                <strong>Branch:</strong> {{ $branchName }} {{ $branchGst ? '(GST: '.$branchGst.')' : '' }}
            </div>
        </td>
        <td style="width: 45%; text-align: right; vertical-align: top;">
            <div style="font-size: 20px; font-weight: 800; color: #4f46e5; letter-spacing: 0.5px;">
                TAX INVOICE
            </div>
            <div style="font-size: 12px; margin-top: 6px; color: #334155;">
                <strong>Invoice No:</strong> {{ $sale->invoice_no_display }}
            </div>
            <div style="font-size: 12px; color: #334155;">
                <strong>Invoice Date:</strong> {{ $sale->invoice_date ? $sale->invoice_date->format('d/m/Y') : '-' }}
            </div>
            <div style="font-size: 12px; color: #334155;">
                <strong>Sale Type:</strong> {{ $sale->isCashSale() ? 'Cash Sale' : 'Credit Sale' }}
            </div>
            @if($sale->due_date)
                <div style="font-size: 12px; color: #d97706;">
                    <strong>Due Date:</strong> {{ $sale->due_date->format('d/m/Y') }}
                </div>
            @endif
        </td>
    </tr>
</table>

@if($isCancelled)
    <div style="background-color: #fef2f2; border: 2px solid #ef4444; color: #dc2626; text-align: center; font-weight: bold; font-size: 16px; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
        *** CANCELLED INVOICE *** (Cancelled on {{ $sale->cancelled_at ? $sale->cancelled_at->format('d/m/Y H:i') : '' }})
    </div>
@endif
