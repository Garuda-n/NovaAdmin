@php
    $summary = $reportData['summary'] ?? [];
    $paginator = $reportData['paginator'] ?? null;
    $items = $reportData['items'] ?? [];
    $hasSearched = $reportData['has_searched'] ?? false;
@endphp

<!-- Summary KPI Cards Header -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <!-- Total Customers Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Customers</p>
            <h3 class="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($summary['total_customers'] ?? 0) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                <span class="text-blue-600 font-bold">{{ number_format($summary['b2b_count'] ?? 0) }} B2B</span>
                <span>•</span>
                <span class="text-emerald-600 font-bold">{{ number_format($summary['b2c_count'] ?? 0) }} B2C</span>
            </p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-users class="w-5 h-5" />
        </div>
    </div>

    <!-- Total Revenue Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Customer Sales</p>
            <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1">₹{{ number_format($summary['total_revenue'] ?? 0, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Completed Transactions</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-currency-rupee class="w-5 h-5" />
        </div>
    </div>

    <!-- Total Invoices Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Orders / Bills</p>
            <h3 class="text-xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ number_format($summary['total_invoices'] ?? 0) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Invoices Generated</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-document-text class="w-5 h-5" />
        </div>
    </div>

    <!-- Total Discounts Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Discounts Received</p>
            <h3 class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1">₹{{ number_format($summary['total_discount'] ?? 0, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Customer Concessions</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-tag class="w-5 h-5" />
        </div>
    </div>

    <!-- Avg Spend Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Avg Spend / Customer</p>
            <h3 class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1">₹{{ number_format($summary['avg_customer_spend'] ?? 0, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Customer Lifetime Value</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-chart-bar class="w-5 h-5" />
        </div>
    </div>
</div>

<!-- Detailed Customer Performance Table -->
<div class="bg-white dark:bg-slate-800 shadow rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-xs">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Customer Name</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Type & GST</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Location</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Invoices</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Tax Paid</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Discounts</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Total Revenue</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Last Purchase</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Status</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($items as $cust)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-3.5 py-3">
                            <div class="font-extrabold text-slate-900 dark:text-white">
                                {{ $cust->customer_name }}
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $cust->mobile ?? 'No Mobile' }} {{ $cust->email ? '• '.$cust->email : '' }}
                            </div>
                        </td>
                        <td class="px-3.5 py-3">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold {{ $cust->customer_type === 'B2B' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
                                {{ $cust->customer_type }}
                            </span>
                            @if($cust->gst_number)
                                <div class="text-[10px] font-mono text-slate-500 dark:text-slate-400 mt-1">
                                    GST: {{ $cust->gst_number }}
                                </div>
                            @endif
                        </td>
                        <td class="px-3.5 py-3 text-slate-600 dark:text-slate-400">
                            {{ $cust->city_name ?? '' }}{{ $cust->city_name && $cust->state_name ? ', ' : '' }}{{ $cust->state_name ?? '—' }}
                        </td>
                        <td class="px-3.5 py-3 text-center font-bold text-indigo-600 dark:text-indigo-400">
                            {{ number_format($cust->completed_invoices) }}
                        </td>
                        <td class="px-3.5 py-3 text-right text-purple-700 dark:text-purple-300 font-semibold">
                            ₹{{ number_format($cust->total_tax, 2) }}
                        </td>
                        <td class="px-3.5 py-3 text-right text-amber-600 dark:text-amber-400 font-semibold">
                            ₹{{ number_format($cust->total_discount, 2) }}
                        </td>
                        <td class="px-3.5 py-3 text-right text-emerald-600 dark:text-emerald-400 font-black text-sm">
                            ₹{{ number_format($cust->total_revenue, 2) }}
                        </td>
                        <td class="px-3.5 py-3 text-center text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
                            {{ $cust->last_purchase_date ? \Carbon\Carbon::parse($cust->last_purchase_date)->format('d M Y') : 'Never' }}
                        </td>
                        <td class="px-3.5 py-3 text-center">
                            @if($cust->status)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                            <button type="button" onclick="openCustomerSalesModal({{ $cust->customer_id }})" class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 dark:hover:bg-indigo-900 text-[11px] font-bold transition">
                                <x-heroicon-o-document-text class="w-3.5 h-3.5" />
                                View Sales
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-12 text-slate-400">
                            @if($hasSearched)
                                <p class="text-sm font-semibold">No customer records matched your filter criteria.</p>
                                <p class="text-xs text-slate-400 mt-1">Try adjusting the search text, location, or customer type filters.</p>
                            @else
                                <p class="text-sm font-semibold">Apply filters to generate the Customer Analysis Report.</p>
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
