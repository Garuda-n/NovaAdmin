<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Stock Inward — {{ $stockInward->invoice_no }}</title>
    <!-- Fonts -->
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
            font-size: 13px;
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

        /* Printable Document Container */
        .page-container {
            max-width: 210mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 16mm 18mm;
            border-radius: 4px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        /* Watermark Placeholder (Future Ready) */
        .watermark-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72px;
            font-weight: 800;
            color: rgba(226, 232, 240, 0.35);
            text-transform: uppercase;
            letter-spacing: 6px;
            pointer-events: none;
            user-select: none;
            z-index: 0;
            display: none; /* Can be toggled on by client config */
        }

        .doc-content {
            position: relative;
            z-index: 1;
        }

        /* Document Header */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .company-info h1 {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .company-info p {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .doc-title-block {
            text-align: right;
        }

        .doc-title {
            font-size: 20px;
            font-weight: 800;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .logo-placeholder {
            max-height: 48px;
            max-width: 180px;
            margin-bottom: 6px;
            object-fit: contain;
        }

        /* Information Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }

        /* Tables */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }

        .items-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            color: #1e293b;
        }

        .items-table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }

        /* Summary Box */
        .summary-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .summary-table {
            width: 320px;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            overflow: hidden;
        }

        .summary-table td {
            padding: 8px 12px;
            font-size: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
            background-color: #f8fafc;
            font-weight: 700;
        }

        .summary-label {
            font-weight: 600;
            color: #475569;
        }

        .summary-value {
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }

        /* Signatures Section */
        .signatures-section {
            margin-top: 36px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            border-top: 1px dashed #94a3b8;
            padding-top: 8px;
        }

        .signature-title {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
        }

        /* Future Features placeholders (QR / Barcode / Seal / Terms) */
        .future-extras {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #64748b;
        }

        /* Footer */
        .doc-footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #64748b;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }

            body {
                background: #ffffff;
                color: #000000;
                font-size: 12px;
            }

            .no-print, .preview-bar {
                display: none !important;
            }

            .page-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .info-grid {
                background-color: #ffffff !important;
                border: 1px solid #64748b;
            }

            .items-table th {
                background-color: #f1f5f9 !important;
                color: #000000 !important;
                border: 1px solid #475569 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .items-table td {
                border: 1px solid #94a3b8 !important;
            }

            .summary-table {
                border: 1px solid #64748b !important;
            }

            .summary-table td {
                border-bottom: 1px solid #94a3b8 !important;
            }

            .signatures-section {
                page-break-inside: avoid;
            }

            .items-table tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Preview Action Bar (Screen view only, hidden when printing) -->
    <div class="preview-bar no-print">
        <div class="preview-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Print Preview — Bulk Stock Inward #{{ $stockInward->invoice_no }}
        </div>
        <div class="preview-actions">
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Document
            </button>
            <button type="button" onclick="window.close()" class="btn btn-secondary">
                ← Back to List
            </button>
        </div>
    </div>

    <!-- Printable A4 Document Page -->
    <div class="page-container">

        <!-- Watermark Container (Future Ready) -->
        <div class="watermark-container">
            BULK INWARD
        </div>

        <div class="doc-content">
            <!-- Header Section -->
            <div class="doc-header">
                <div class="company-info">
                    {{-- Future Ready Logo Slot --}}
                    @if(isset($stockInward->company->logo) && $stockInward->company->logo)
                        <img src="{{ asset($stockInward->company->logo) }}" alt="Company Logo" class="logo-placeholder">
                    @endif
                    <h1>{{ $stockInward->company->name ?? 'Company Name' }}</h1>
                    <p>Branch: <strong>{{ $stockInward->branch->name ?? 'Main Branch' }}</strong> {{ $stockInward->counter ? '('.$stockInward->counter->counter_name.')' : '' }}</p>
                </div>
                <div class="doc-title-block">
                    <div class="doc-title">Bulk Stock Inward</div>
                    <p style="font-size: 11px; color: #64748b; margin-top: 4px;">Ref No: <strong>#{{ $stockInward->id }}</strong></p>
                </div>
            </div>

            <!-- Invoice Metadata Grid -->
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Invoice Number</span>
                    <span class="info-value">{{ $stockInward->invoice_no }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Invoice Date</span>
                    <span class="info-value">{{ $stockInward->invoice_date ? $stockInward->invoice_date->format('d M Y') : '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Supplier</span>
                    <span class="info-value">{{ $stockInward->supplier->supplier_name ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Created By / Date</span>
                    <span class="info-value" style="font-size: 11px;">
                        {{ $stockInward->creator->name ?? 'System' }}
                        <br>
                        <span style="color: #64748b; font-weight: normal;">{{ $stockInward->created_at ? $stockInward->created_at->format('d M Y, h:i A') : '—' }}</span>
                    </span>
                </div>
            </div>

            @if($stockInward->remarks)
                <div style="margin-bottom: 16px; padding: 8px 12px; background: #fffbe0; border-left: 3px solid #f59e0b; font-size: 12px;">
                    <strong>Remarks:</strong> {{ $stockInward->remarks }}
                </div>
            @endif

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">S.No</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Sub Product</th>
                        <th class="text-right" style="width: 110px;">Received Qty</th>
                        <th class="text-right" style="width: 110px;">Allocated Qty</th>
                        <th class="text-right" style="width: 110px;">Pending Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockInward->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->product->category->name ?? '—' }}</td>
                            <td>
                                <strong>{{ $item->product->name ?? '—' }}</strong>
                                @if($item->remarks)
                                    <div style="font-size: 10px; color: #64748b;">{{ $item->remarks }}</div>
                                @endif
                            </td>
                            <td>{{ $item->subProduct->name ?? '—' }}</td>
                            <td class="text-right" style="font-weight: 600;">{{ number_format((float)$item->qty, 2) }}</td>
                            <td class="text-right">{{ number_format((float)($item->allocated_qty ?? 0), 0) }}</td>
                            <td class="text-right" style="font-weight: 600; color: {{ ($item->pending_qty ?? 0) > 0 ? '#b45309' : '#166534' }};">
                                {{ number_format((float)($item->pending_qty ?? 0), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 16px; color: #64748b;">No items found in this inward transaction.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Summary Table Section -->
            <div class="summary-wrapper">
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Total Received Quantity:</td>
                        <td class="summary-value">{{ number_format((float)($summary['total_received_qty'] ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Total Allocated Quantity:</td>
                        <td class="summary-value">{{ number_format((float)($summary['total_allocated_qty'] ?? 0), 0) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label" style="color: #0f172a;">Total Pending Quantity:</td>
                        <td class="summary-value" style="font-size: 14px; color: #4f46e5;">{{ number_format((float)($summary['total_pending_qty'] ?? 0), 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Future Ready Section (QR Code / Barcode / Terms Placeholder) -->
            <div class="future-extras">
                <div>
                    <span>Ref Document: #{{ $stockInward->invoice_no }}</span>
                </div>
                <div>
                    <span>Official Stamp / Seal</span>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="signatures-section">
                <div class="signature-box">
                    <div style="height: 35px;"></div>
                    <div class="signature-title">Prepared By</div>
                </div>
                <div class="signature-box">
                    <div style="height: 35px;"></div>
                    <div class="signature-title">Verified By</div>
                </div>
                <div class="signature-box">
                    <div style="height: 35px;"></div>
                    <div class="signature-title">Authorized Signatory</div>
                </div>
                <div class="signature-box">
                    <div style="height: 35px;"></div>
                    <div class="signature-title">Supplier Signature</div>
                </div>
            </div>

            <!-- Document Footer -->
            <div class="doc-footer">
                <div>Printed By: <strong>{{ auth()->user()->name ?? 'System' }}</strong></div>
                <div>Printed On: <strong>{{ now()->format('d M Y, h:i A') }}</strong></div>
                <div>Page 1 of 1</div>
            </div>
        </div>

    </div>

</body>
</html>
