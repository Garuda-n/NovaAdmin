<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice - {{ $sale->invoice_no_display }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .header-table, .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .details-table th {
            background-color: #f8fafc;
            text-transform: uppercase;
            font-size: 11px;
            color: #475569;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }
        @media print {
            .no-print { display: none; }
            .invoice-box { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right; max-width: 800px; margin-left: auto; margin-right: auto;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Print / Save PDF
        </button>
    </div>

    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <div class="title">{{ $sale->salesInvoiceSnapshot->company_name ?? $sale->company->company_name }}</div>
                    <div>{{ $sale->salesInvoiceSnapshot->company_address ?? '' }}</div>
                    <div>GSTIN: {{ $sale->salesInvoiceSnapshot->company_gst_number ?? 'N/A' }}</div>
                    <div>Branch: {{ $sale->salesInvoiceSnapshot->branch_name ?? $sale->branch->branch_name }}</div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <h2 style="margin: 0; color: #4f46e5;">TAX INVOICE</h2>
                    <div style="margin-top: 6px;"><strong>Invoice No:</strong> {{ $sale->invoice_no_display }}</div>
                    <div><strong>Date:</strong> {{ $sale->invoice_date ? $sale->invoice_date->format('d/m/Y') : '' }}</div>
                    <div><strong>Sale Type:</strong> {{ $sale->isCashSale() ? 'Cash Sale' : 'Credit Sale' }}</div>
                    @if($sale->due_date)
                        <div><strong>Due Date:</strong> {{ $sale->due_date->format('d/m/Y') }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

        <table class="header-table">
            <tr>
                <td style="width: 100%;">
                    <strong>BILLED TO:</strong><br>
                    <span style="font-size: 15px; font-weight: bold;">{{ $sale->salesInvoiceSnapshot->customer_name ?? $sale->customer->customer_name }}</span><br>
                    Mobile: {{ $sale->salesInvoiceSnapshot->customer_mobile ?? $sale->customer->mobile }}<br>
                    GSTIN: {{ $sale->salesInvoiceSnapshot->customer_gst_number ?? 'Unregistered' }}<br>
                    Address: {{ $sale->salesInvoiceSnapshot->customer_address ?? 'N/A' }}
                </td>
            </tr>
        </table>

        <table class="details-table mt-6">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Item Description</th>
                    <th class="text-right" style="width: 10%;">Qty</th>
                    <th class="text-right" style="width: 12%;">Rate (₹)</th>
                    <th class="text-right" style="width: 10%;">GST %</th>
                    <th class="text-right" style="width: 18%;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $detail->product_name }}</strong><br>
                            <small style="color: #666;">Code: {{ $detail->product_code }}</small>
                        </td>
                        <td class="text-right">{{ number_format($detail->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->rate, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->tax_percentage, 2) }}%</td>
                        <td class="text-right font-bold">{{ number_format($detail->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="header-table mt-4">
            <tr>
                <td style="width: 50%;">
                    @if($sale->remarks)
                        <strong>Notes / Remarks:</strong><br>
                        {{ $sale->remarks }}
                    @endif
                </td>
                <td style="width: 50%;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-right font-bold">₹{{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tax Amount:</td>
                            <td class="text-right">₹{{ number_format($sale->tax_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Discount:</td>
                            <td class="text-right">-₹{{ number_format($sale->invoice_discount + $sale->item_discount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Round Off:</td>
                            <td class="text-right">₹{{ number_format($sale->round_off, 2) }}</td>
                        </tr>
                        <tr style="border-top: 2px solid #333; font-size: 15px;">
                            <td><strong>Grand Total:</strong></td>
                            <td class="text-right font-bold" style="color: #4f46e5;">₹{{ number_format($sale->grand_total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="margin-top: 50px; text-align: right;">
            <div style="border-top: 1px solid #ccc; display: inline-block; width: 200px; text-align: center; padding-top: 5px;">
                Authorized Signatory
            </div>
        </div>
    </div>
</body>
</html>
