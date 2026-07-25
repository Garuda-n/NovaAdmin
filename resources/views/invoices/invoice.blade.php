<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GST Tax Invoice - {{ $sale->invoice_no_display }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            background-color: #f8fafc;
        }
        .invoice-container {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .print-btn-bar {
            max-width: 850px;
            margin: 0 auto 15px auto;
            text-align: right;
        }
        .btn-print {
            padding: 8px 18px;
            background-color: #4f46e5;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
        }
        .btn-print:hover {
            background-color: #4338ca;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .print-btn-bar {
                display: none !important;
            }
            .invoice-container {
                border: none;
                box-shadow: none;
                padding: 0;
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <button onclick="window.print()" class="btn-print">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print GST Tax Invoice
        </button>
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
