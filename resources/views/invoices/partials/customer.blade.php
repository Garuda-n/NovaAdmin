<!-- Invoice Customer Partial -->
<div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 20px; background-color: #f8fafc;">
    <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 6px; border-b: 1px solid #cbd5e1; padding-bottom: 4px;">
        Billed To (Customer Snapshot)
    </div>
    <table style="width: 100%; border-collapse: collapse; font-size: 12px; color: #334155;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div style="font-size: 14px; font-weight: bold; color: #0f172a;">{{ $customerName }}</div>
                <div>Mobile: {{ $customerMobile }} {{ $customerEmail ? '| Email: '.$customerEmail : '' }}</div>
                <div>Address: {{ $customerAddress }}</div>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <div><strong>Customer GSTIN:</strong> {{ $customerGst }}</div>
                <div><strong>State / Tax Type:</strong> {{ $gstType == 1 ? 'Intra-State (CGST + SGST)' : 'Inter-State (IGST)' }}</div>
            </td>
        </tr>
    </table>
</div>
