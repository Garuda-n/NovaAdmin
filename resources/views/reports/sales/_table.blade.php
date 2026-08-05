@php
    $summary = $reportData['summary'] ?? [];
    $paginator = $reportData['paginator'] ?? null;
    $items = $reportData['items'] ?? [];
    $hasSearched = $reportData['has_searched'] ?? false;
@endphp

<!-- Summary KPI Cards Header -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <!-- Gross Sales Revenue Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gross Sales Revenue</p>
            <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1">₹{{ number_format($summary['gross_revenue'] ?? 0, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Completed Sales</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-currency-rupee class="w-5 h-5" />
        </div>
    </div>

    <!-- Total Invoices Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Sales Bills</p>
            <h3 class="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($summary['total_invoices'] ?? 0) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                <span class="text-emerald-600 font-bold">{{ number_format($summary['completed_invoices'] ?? 0) }} Done</span>
                <span>•</span>
                <span class="text-rose-500 font-bold">{{ number_format($summary['cancelled_invoices'] ?? 0) }} Can.</span>
            </p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-document-text class="w-5 h-5" />
        </div>
    </div>

    <!-- Total Tax Amount Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tax Collected</p>
            <h3 class="text-xl font-black text-purple-600 dark:text-purple-400 mt-1">₹{{ number_format($summary['total_tax'] ?? 0, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">GST (CGST/SGST/IGST)</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-calculator class="w-5 h-5" />
        </div>
    </div>

    <!-- Total Discount Given Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Discounts Offered</p>
            <h3 class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1">₹{{ number_format($summary['total_discount'] ?? 0, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Item & Invoice Concessions</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-tag class="w-5 h-5" />
        </div>
    </div>

    <!-- Avg Ticket Size Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Avg Ticket Size</p>
            <h3 class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1">₹{{ number_format($summary['avg_ticket_size'] ?? 0, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Average Bill Amount</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-ticket class="w-5 h-5" />
        </div>
    </div>
</div>

<!-- Detailed Sales Transactions Table -->
<div class="bg-white dark:bg-slate-800 shadow rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-xs">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Invoice No</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Date</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Customer</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Branch / Counter</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Payment Mode</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Tax</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Discount</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Grand Total</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Status</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($items as $sale)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-3.5 py-3 font-extrabold text-indigo-600 dark:text-indigo-400">
                            <a href="{{ route('sales.show', $sale->id) }}" class="hover:underline flex items-center gap-1">
                                #{{ $sale->invoice_no_display }}
                            </a>
                        </td>
                        <td class="px-3.5 py-3 text-slate-600 dark:text-slate-300 whitespace-nowrap font-medium">
                            {{ $sale->invoice_date ? $sale->invoice_date->format('d M Y') : '—' }}
                        </td>
                        <td class="px-3.5 py-3 text-slate-800 dark:text-slate-100 font-bold">
                            {{ $sale->customer->customer_name ?? 'Walk-in Customer' }}
                            @if(isset($sale->customer_type))
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-extrabold {{ $sale->customer_type === 'B2B' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
                                    {{ $sale->customer_type }}
                                </span>
                            @endif
                        </td>
                        <td class="px-3.5 py-3 text-slate-600 dark:text-slate-400">
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $sale->branch->name ?? '—' }}</span>
                            @if($sale->counter)
                                <span class="text-slate-400 text-[10px]"> ({{ $sale->counter->counter_name }})</span>
                            @endif
                        </td>
                        <td class="px-3.5 py-3">
                            @if($sale->payments && $sale->payments->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($sale->payments as $payment)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800">
                                            {{ $payment->paymentMode->mode_name ?? 'Cash' }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-3.5 py-3 text-right text-purple-700 dark:text-purple-300 font-semibold">
                            ₹{{ number_format($sale->tax_amount, 2) }}
                        </td>
                        <td class="px-3.5 py-3 text-right text-amber-600 dark:text-amber-400 font-semibold">
                            ₹{{ number_format(($sale->item_discount ?? 0) + ($sale->invoice_discount ?? 0), 2) }}
                        </td>
                        <td class="px-3.5 py-3 text-right text-slate-900 dark:text-white font-black text-sm">
                            ₹{{ number_format($sale->grand_total, 2) }}
                        </td>
                        <td class="px-3.5 py-3 text-center">
                            @if($sale->status === \App\Models\Sale::STATUS_COMPLETED)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Cancelled
                                </span>
                            @endif
                        </td>
                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('sales.show', $sale->id) }}" class="p-1 rounded text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 font-semibold" title="View Details">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </a>
                                <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="p-1 rounded text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 font-semibold" title="Print Invoice">
                                    <x-heroicon-o-printer class="w-4 h-4" />
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-12 text-slate-400">
                            @if($hasSearched)
                                <p class="text-sm font-semibold">No sales transactions matched your filter criteria.</p>
                                <p class="text-xs text-slate-400 mt-1">Try adjusting the date range, branch, or customer filters.</p>
                            @else
                                <p class="text-sm font-semibold">Apply date range or branch filters to generate the Sales Report.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($paginator && $paginator->hasPages())
        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-700">
            {{ $paginator->links() }}
        </div>
    @endif
</div>
