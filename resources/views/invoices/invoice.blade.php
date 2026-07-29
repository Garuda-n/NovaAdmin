<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GST Tax Invoice - {{ $sale->invoice_no_display }}</title>
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
        .invoice-container {
            max-width: 210mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 16mm 18mm;
            border-radius: 4px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        /* Sticky Top Action Bar matching Quotation style */
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
            color: #ffffff;
        }
        .btn-primary {
            background-color: #4f46e5;
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
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .preview-bar {
                display: none !important;
            }
            .invoice-container {
                border: none;
                box-shadow: none;
                padding: 0;
                width: 100%;
                max-width: none;
                margin: 0;
            }
            @page {
                size: A4;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="preview-bar">
        <div class="preview-title">
            <span>📄 Sales Invoice Print / PDF View — #{{ $sale->invoice_no_display }}</span>
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

    <div class="invoice-container">
        <!-- Header Partial -->
        @include('invoices.partials.header')

        <!-- Customer Partial -->
        @include('invoices.partials.customer')

        <!-- Items Table Partial -->
        @include('invoices.partials.items')

        <!-- Tax Breakdown Summary Partial -->
        @include('invoices.partials.tax_summary')

        <!-- Payment Partial -->
        @include('invoices.partials.payment')

        <!-- Footer Partial -->
        @include('invoices.partials.footer')
    </div>

</body>
</html>
