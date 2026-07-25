<!-- GST Summary Partial -->
<div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; margin-bottom: 20px; background-color: #fafafa;">
    <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #475569; margin-bottom: 6px;">
        GST Tax Rate Breakdown
    </div>
    <table style="width: 100%; border-collapse: collapse; font-size: 11px; text-align: right;">
        <thead>
            <tr style="background-color: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #475569;">
                <th style="padding: 4px; text-align: left;">GST Rate</th>
                <th style="padding: 4px;">Taxable Amount (₹)</th>
                @if($gstType == 1)
                    <th style="padding: 4px;">CGST (₹)</th>
                    <th style="padding: 4px;">SGST (₹)</th>
                @else
                    <th style="padding: 4px;">IGST (₹)</th>
                @endif
                <th style="padding: 4px;">Total Tax (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($taxSummary as $rate => $row)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 4px; text-align: left; font-weight: 600;">{{ number_format($row['tax_rate'], 2) }}%</td>
                    <td style="padding: 4px;">{{ number_format($row['taxable_amount'], 2) }}</td>
                    @if($gstType == 1)
                        <td style="padding: 4px;">{{ number_format($row['cgst_amount'], 2) }}</td>
                        <td style="padding: 4px;">{{ number_format($row['sgst_amount'], 2) }}</td>
                    @else
                        <td style="padding: 4px;">{{ number_format($row['igst_amount'], 2) }}</td>
                    @endif
                    <td style="padding: 4px; font-weight: bold;">{{ number_format($row['tax_amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
