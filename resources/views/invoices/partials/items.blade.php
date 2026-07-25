<!-- Invoice Items Table Partial -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px;">
    <thead>
        <tr style="background-color: #f1f5f9; color: #475569; text-transform: uppercase; border: 1px solid #cbd5e1;">
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 4%;">S.No</th>
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 14%;">Product Code</th>
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 28%; text-align: left;">Product Name</th>
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 8%; text-align: center;">UOM</th>
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 8%; text-align: right;">Qty</th>
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 10%; text-align: right;">Rate (₹)</th>
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 8%; text-align: right;">Disc (₹)</th>
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 6%; text-align: right;">Tax %</th>
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 10%; text-align: right;">Tax Amt (₹)</th>
            <th style="padding: 8px; border: 1px solid #cbd5e1; width: 12%; text-align: right;">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sale->details as $index => $detail)
            <tr style="border: 1px solid #e2e8f0; color: #1e293b;">
                <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: center;">{{ $index + 1 }}</td>
                <td style="padding: 6px; border: 1px solid #e2e8f0; font-family: monospace;">{{ $detail->product_code }}</td>
                <td style="padding: 6px; border: 1px solid #e2e8f0; font-weight: 600;">{{ $detail->product_name }}</td>
                <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: center;">{{ $detail->uom->uom_name ?? 'PCS' }}</td>
                <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: right; font-weight: 600;">{{ number_format($detail->quantity, 2) }}</td>
                <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: right;">{{ number_format($detail->rate, 2) }}</td>
                <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: right;">{{ number_format($detail->discount_amount, 2) }}</td>
                <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: right;">{{ number_format($detail->tax_percentage, 2) }}%</td>
                <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: right;">{{ number_format($detail->tax_amount, 2) }}</td>
                <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: right; font-weight: bold;">{{ number_format($detail->line_total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
