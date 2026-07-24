<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation #{{ $quotation->quotation_no ?? $quotation->id }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background-color: #f1f5f9;
            line-height: 1.5;
        }

        /* Top Action / Preview Bar */
        .preview-bar {
            background-color: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .preview-title {
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .preview-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease-in-out;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-secondary {
            background-color: #334155;
            color: #f8fafc;
        }

        .btn-secondary:hover {
            background-color: #475569;
        }

        /* Printable Document Container (A4 Style) */
        .page-container {
            max-width: 210mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 16mm 18mm;
            border-radius: 4px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        /* Print Media Styles */
        @media print {
            body {
                background-color: #ffffff;
            }

            .preview-bar {
                display: none !important;
            }

            .page-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
                max-width: 100%;
            }

            @page {
                size: A4;
                margin: 12mm 15mm;
            }
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .company-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .doc-title {
            font-size: 24px;
            font-weight: 800;
            color: #4f46e5;
            text-align: right;
            text-transform: uppercase;
        }

        .meta-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 20px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .value {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }

        /* Line Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }

        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Totals Section */
        .totals-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .totals-table {
            width: 260px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 10px;
            font-size: 12px;
        }

        .grand-total-row {
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
        }

        .footer-note {
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            margin-top: 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Top Sticky Preview Bar -->
    <div class="preview-bar">
        <div class="preview-title">
            <span>📄 Quotation Print / PDF View — #{{ $quotation->quotation_no ?? $quotation->id }}</span>
        </div>
        <div class="preview-actions">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Print / Save as PDF
            </button>

            <button onclick="window.close()" class="btn btn-secondary">
                ← Back to List
            </button>
        </div>
    </div>

    <!-- Page Printable Container -->
    <div class="page-container">

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="vertical-align: top;">
                    <div class="company-title">{{ config('app.name', 'NovaAdmin') }}</div>
                    <div style="color: #64748b; font-size: 11px; margin-top: 4px;">
                        {{ $quotation->branch->name ?? 'Main Branch' }}
                    </div>
                </td>
                <td style="vertical-align: top;" class="text-right">
                    <div class="doc-title">QUOTATION</div>
                    <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 4px;">
                        #{{ $quotation->quotation_no ?? $quotation->id }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Metadata Section -->
        <div class="meta-card grid-2">
            <div>
                <div style="margin-bottom: 8px;">
                    <div class="label">Customer Name</div>
                    <div class="value">{{ $quotation->customer->customer_name ?? 'N/A' }}</div>
                </div>
                <div style="margin-bottom: 8px;">
                    <div class="label">Mobile</div>
                    <div class="value">{{ $quotation->customer->mobile ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="label">Customer Type / GST</div>
                    <div class="value">{{ $quotation->customer_type }} {{ $quotation->customer?->gst_number ? '('.$quotation->customer->gst_number.')' : '' }}</div>
                </div>
            </div>

            <div>
                <div style="margin-bottom: 8px;">
                    <div class="label">Business Date</div>
                    <div class="value">{{ $quotation->business_date ? $quotation->business_date->format('d M Y') : date('d M Y') }}</div>
                </div>
                <div style="margin-bottom: 8px;">
                    <div class="label">Branch / Counter</div>
                    <div class="value">{{ $quotation->branch->name ?? '-' }} / {{ $quotation->counter->counter_name ?? '-' }}</div>
                </div>
                <div>
                    <div class="label">Status</div>
                    <div class="value" style="color: {{ $quotation->status == 2 ? '#2563eb' : ($quotation->isExpired() ? '#dc2626' : '#16a34a') }};">
                        {{ $quotation->display_status }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>Product Description</th>
                    <th style="width: 80px;">UOM</th>
                    <th class="text-right" style="width: 80px;">Qty</th>
                    <th class="text-right" style="width: 100px;">Rate</th>
                    <th class="text-right" style="width: 70px;">Tax %</th>
                    <th class="text-right" style="width: 90px;">Tax Amt</th>
                    <th class="text-right" style="width: 110px;">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $detail->product_name }}</td>
                        <td>{{ $detail->uom_name }}</td>
                        <td class="text-right">{{ number_format($detail->qty, 3) }}</td>
                        <td class="text-right">₹{{ number_format($detail->rate, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->tax_percent, 2) }}%</td>
                        <td class="text-right">₹{{ number_format($detail->tax_amount, 2) }}</td>
                        <td class="text-right" style="font-weight: 600;">₹{{ number_format($detail->line_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="8">Item Code: {{ $detail->product->code ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Summary -->
        <div class="totals-wrapper">
            <table class="totals-table">
                <tr>
                    <td style="color: #64748b;">Subtotal:</td>
                    <td class="text-right" style="font-weight: 600;">₹{{ number_format($quotation->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b;">Tax Amount:</td>
                    <td class="text-right" style="font-weight: 600;">₹{{ number_format($quotation->tax_amount, 2) }}</td>
                </tr>
                <tr class="grand-total-row">
                    <td style="padding: 10px;">Grand Total:</td>
                    <td class="text-right" style="padding: 10px;">₹{{ number_format($quotation->grand_total, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($quotation->remarks)
            <div style="background: #f8fafc; padding: 12px; border-radius: 6px; border-left: 3px solid #4f46e5; margin-bottom: 24px;">
                <div class="label">Remarks / Terms</div>
                <div style="font-size: 11px; margin-top: 4px;">{{ $quotation->remarks }}</div>
            </div>
        @endif

        <!-- Footer Signatures -->
        <div class="footer-note">
            <div>
                Prepared By: <strong>{{ $quotation->creator->name ?? 'System' }}</strong>
            </div>
            <div style="text-align: right;">
                <div style="border-top: 1px solid #cbd5e1; padding-top: 4px; width: 160px; text-align: center;">
                    Authorized Signature
                </div>
            </div>
        </div>

    </div>

</body>
</html>
