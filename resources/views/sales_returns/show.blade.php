<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#182035] p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Sales Return #{{ $salesReturn->return_no_display }}
                        </h1>
                        <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 text-xs font-semibold rounded-full border border-emerald-300 dark:border-emerald-700">
                            Completed
                        </span>
                        @if ($salesReturn->sale && $salesReturn->sale->isCashSale())
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 text-xs font-semibold rounded-full border border-blue-300 dark:border-blue-700">
                                Cash Refund Payout
                            </span>
                        @else
                            <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 text-xs font-semibold rounded-full border border-amber-300 dark:border-amber-700">
                                Credit Offset
                            </span>
                        @endif
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Return Date: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $salesReturn->return_date ? $salesReturn->return_date->format('d M Y') : '-' }}</span>
                        • Business Date: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $salesReturn->business_date ? $salesReturn->business_date->format('d M Y') : '-' }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('sales-returns.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg hover:bg-slate-300 transition">
                        ← Back to List
                    </a>

                    <button onclick="window.print();" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Receipt
                    </button>
                </div>
            </div>

            <!-- Snapshots Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Customer info -->
                <div class="bg-white dark:bg-[#182035] rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Customer</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $salesReturn->customer->customer_name ?? 'Walk-in' }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Mobile: {{ $salesReturn->customer->mobile ?? 'N/A' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">GSTIN: {{ $salesReturn->customer->gst_number ?? 'Unregistered' }}</p>
                </div>

                <!-- Company Seller info -->
                <div class="bg-white dark:bg-[#182035] rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Seller (Company)</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $salesReturn->company->company_name ?? 'NovaAdmin' }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Branch: {{ $salesReturn->branch->branch_name ?? 'Main' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Counter: {{ $salesReturn->counter->counter_name ?? 'POS' }}</p>
                </div>

                <!-- Original Reference Invoice info -->
                <div class="bg-white dark:bg-[#182035] rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Original Reference</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        @if ($salesReturn->sale)
                            <a href="{{ route('sales.show', $salesReturn->sale->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                Invoice #{{ $salesReturn->sale->invoice_no_display }}
                            </a>
                        @else
                            -
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Original Invoice Date: {{ $salesReturn->sale->invoice_date ? $salesReturn->sale->invoice_date->format('d M Y') : '-' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Original Invoice Type: {{ $salesReturn->sale->isCashSale() ? 'Cash' : 'Credit' }}</p>
                </div>
            </div>

            <!-- Line Items table -->
            <div class="bg-white dark:bg-[#182035] rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Returned Items</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-[#0f1422] text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-3">#</th>
                                <th class="px-6 py-3">Product Name & Code</th>
                                <th class="px-6 py-3">Item Type</th>
                                <th class="px-6 py-3">Serial Barcodes (Returned / Recreated)</th>
                                <th class="px-6 py-3 text-right">Returned Qty</th>
                                <th class="px-6 py-3 text-right">Rate (₹)</th>
                                <th class="px-6 py-3 text-right">Tax (%)</th>
                                <th class="px-6 py-3 text-right">Line Total (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach ($salesReturn->salesReturnDetails as $index => $detail)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                                    <td class="px-6 py-4 text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $detail->product->name ?? 'Unknown Product' }}
                                        <span class="block text-xs text-slate-400 font-mono">{{ $detail->product->code ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($detail->item_type === 1)
                                            <span class="text-xs px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/50">Serialized</span>
                                        @else
                                            <span class="text-xs px-2 py-0.5 rounded bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-400 border border-slate-200 dark:border-slate-700/50">Quantity</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs space-y-1">
                                        @if ($detail->originalStockItem)
                                            <div>
                                                <span class="text-slate-400">Orig:</span>
                                                <span class="text-slate-900 dark:text-slate-100 font-bold bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">{{ $detail->originalStockItem->item_code }}</span>
                                            </div>
                                        @endif
                                        @if ($detail->recreatedStockItem)
                                            <div>
                                                <span class="text-emerald-500 font-bold">New:</span>
                                                <span class="text-emerald-700 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/30 px-1.5 py-0.5 rounded">{{ $detail->recreatedStockItem->item_code }}</span>
                                            </div>
                                        @elseif($detail->item_type === 1)
                                            <div class="text-amber-500 text-xs font-semibold">
                                                Pending Warehouse Recreation
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium">{{ number_format($detail->returned_quantity, 2) }}</td>
                                    <td class="px-6 py-4 text-right">₹{{ number_format($detail->rate, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-indigo-600 dark:text-indigo-400 font-medium">{{ number_format($detail->tax_percentage, 2) }}%</td>
                                    <td class="px-6 py-4 text-right font-extrabold text-slate-900 dark:text-white">₹{{ number_format($detail->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Subtotals summary block -->
                <div class="p-6 bg-slate-50 dark:bg-[#0f1422] border-t border-slate-200 dark:border-slate-800 flex justify-end">
                    <div class="w-80 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Subtotal:</span>
                            <span class="font-medium text-slate-900 dark:text-white">₹{{ number_format($salesReturn->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Discount Deductions:</span>
                            <span class="font-medium text-slate-900 dark:text-white">₹{{ number_format($salesReturn->item_discount + $salesReturn->invoice_discount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Total Tax Replaced:</span>
                            <span class="font-medium text-slate-900 dark:text-white">₹{{ number_format($salesReturn->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Round Off:</span>
                            <span class="font-medium text-slate-900 dark:text-white">₹{{ number_format($salesReturn->round_off, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white pt-2 border-t border-slate-300 dark:border-slate-700">
                            <span>Grand Total:</span>
                            <span class="text-indigo-600 dark:text-indigo-400">₹{{ number_format($salesReturn->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refund payouts details card (for cash sale refunds) -->
            @if ($salesReturn->salesPayments->isNotEmpty())
                <div class="bg-white dark:bg-[#182035] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                        Refund Payout Logs
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-[#0f1422] text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-6 py-3">Payout Date</th>
                                    <th class="px-6 py-3">Payment Mode</th>
                                    <th class="px-6 py-3">Reference No</th>
                                    <th class="px-6 py-3 text-right">Amount (₹)</th>
                                    <th class="px-6 py-3">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @foreach ($salesReturn->salesPayments as $p)
                                    <tr>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $p->payment_date ? $p->payment_date->format('d M Y') : '-' }}</td>
                                        <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">{{ $p->paymentMode->name ?? 'Cash' }}</td>
                                        <td class="px-6 py-4 font-mono text-xs">{{ $p->reference_no ?? '-' }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-rose-600 dark:text-rose-400">₹{{ number_format(abs($p->amount), 2) }}</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs">{{ $p->remarks }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
