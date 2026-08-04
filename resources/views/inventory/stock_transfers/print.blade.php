<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Transfer Manifest #{{ $stockTransfer->transfer_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-title {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .transfer-title {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .card {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 6px;
        }
        .card-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            font-size: 11px;
            text-transform: uppercase;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 180px;
            text-align: center;
            padding-top: 5px;
            font-size: 11px;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Print / Save PDF
        </button>
    </div>

    <div class="header">
        <div>
            <div class="company-title">{{ $stockTransfer->company->name ?? 'NovaAdmin ERP' }}</div>
            <div style="color: #666; margin-top: 4px;">Stock Transfer Manifest / Movement Advice</div>
        </div>
        <div style="text-align: right;">
            <div class="transfer-title">Transfer #{{ $stockTransfer->transfer_no }}</div>
            <div>Date: {{ $stockTransfer->transfer_date ? $stockTransfer->transfer_date->format('d M Y') : now()->format('d M Y') }}</div>
            <div>Status: <strong>{{ $stockTransfer->status->uiLabel() }}</strong></div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="card-title">Dispatch Location (Source)</div>
            <div style="font-size: 15px; font-weight: bold;">{{ $stockTransfer->sourceBranch->name ?? '-' }}</div>
            @if($stockTransfer->sourceCounter)
                <div>Counter: {{ $stockTransfer->sourceCounter->counter_name }}</div>
            @endif
        </div>

        <div class="card">
            <div class="card-title">Receiving Location (Destination)</div>
            <div style="font-size: 15px; font-weight: bold;">{{ $stockTransfer->destinationBranch->name ?? '-' }}</div>
            @if($stockTransfer->destinationCounter)
                <div>Counter: {{ $stockTransfer->destinationCounter->counter_name }}</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Description</th>
                <th>Code</th>
                <th>Serial / Item Code</th>
                <th class="text-right">Transferred Qty</th>
                <th class="text-right">Received Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockTransfer->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $detail->product->name ?? '' }}</strong></td>
                    <td style="font-family: monospace;">{{ $detail->product->code ?? '-' }}</td>
                    <td style="font-family: monospace;">{{ $detail->item_code ?? '-' }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($detail->transferred_qty, 2) }} {{ $detail->product->uom->name ?? '' }}</td>
                    <td class="text-right">{{ $detail->received_qty !== null ? number_format($detail->received_qty, 2) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($stockTransfer->remarks)
        <div style="margin-bottom: 20px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 4px;">
            <strong>Remarks:</strong> {{ $stockTransfer->remarks }}
        </div>
    @endif

    <div class="footer">
        <div class="signature-line">
            Prepared By<br>
            <span style="font-weight: normal; font-size: 10px;">{{ $stockTransfer->creator->name ?? '' }}</span>
        </div>
        <div class="signature-line">
            Dispatched By<br>
            <span style="font-weight: normal; font-size: 10px;">{{ $stockTransfer->dispatcher->name ?? '' }}</span>
        </div>
        <div class="signature-line">
            Received By<br>
            <span style="font-weight: normal; font-size: 10px;">{{ $stockTransfer->receiver->name ?? '' }}</span>
        </div>
    </div>
</body>
</html>
