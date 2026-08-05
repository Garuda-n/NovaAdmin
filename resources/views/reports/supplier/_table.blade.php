@php
    $summary = $reportData['summary'] ?? [];
    $paginator = $reportData['paginator'] ?? null;
    $items = $reportData['items'] ?? [];
    $hasSearched = $reportData['has_searched'] ?? false;
@endphp

<!-- Summary KPI Cards Header -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <!-- Total Active Suppliers Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Suppliers</p>
            <h3 class="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($summary['total_suppliers'] ?? 0) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Vendors & Manufacturers</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-building-storefront class="w-5 h-5" />
        </div>
    </div>

    <!-- Total Procurement Spending Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Procurement Spending</p>
            <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1">₹{{ number_format($summary['total_procurement_value'] ?? 0, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Stock Purchase Value</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-currency-rupee class="w-5 h-5" />
        </div>
    </div>

    <!-- Total Inward Bills Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Inward Invoices / Bills</p>
            <h3 class="text-xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ number_format($summary['total_inward_bills'] ?? 0) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Stock Inward Records</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-document-text class="w-5 h-5" />
        </div>
    </div>

    <!-- Total Stock Quantity Procured Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Stock Procured</p>
            <h3 class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($summary['total_quantity_procured'] ?? 0) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Quantity / Items</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-cube class="w-5 h-5" />
        </div>
    </div>

    <!-- Avg Spend per Supplier Card -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Avg Spend / Supplier</p>
            <h3 class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1">₹{{ number_format($summary['avg_spend_per_supplier'] ?? 0, 2) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Average Vendor Purchase</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
            <x-heroicon-o-chart-bar class="w-5 h-5" />
        </div>
    </div>
</div>

<!-- Detailed Supplier Performance Table -->
<div class="bg-white dark:bg-slate-800 shadow rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-xs">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Supplier Name</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Type & Code</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Location / Branch</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Inwards Count</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Items Procured</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Total Purchase Value</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Last Inward</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Status</th>
                    <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($items as $sup)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-3.5 py-3">
                            <div class="font-extrabold text-slate-900 dark:text-white">
                                {{ $sup->supplier_name }}
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $sup->contact_person ? '👤 '.$sup->contact_person.' • ' : '' }} {{ $sup->mobile ?? 'No Mobile' }}
                            </div>
                        </td>
                        <td class="px-3.5 py-3">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950 dark:text-indigo-300 dark:border-indigo-800">
                                {{ $sup->supplier_type ?? 'Supplier' }}
                            </span>
                            @if($sup->supplier_code)
                                <div class="text-[10px] font-mono text-slate-500 dark:text-slate-400 mt-1">
                                    Code: {{ $sup->supplier_code }}
                                </div>
                            @endif
                        </td>
                        <td class="px-3.5 py-3 text-slate-600 dark:text-slate-400">
                            <div>{{ $sup->city_name ?? '' }}{{ $sup->city_name && $sup->state_name ? ', ' : '' }}{{ $sup->state_name ?? '—' }}</div>
                            @if($sup->branch_name)
                                <div class="text-[10px] text-slate-400 mt-0.5">Branch: {{ $sup->branch_name }}</div>
                            @endif
                        </td>
                        <td class="px-3.5 py-3 text-center font-bold text-indigo-600 dark:text-indigo-400">
                            {{ number_format($sup->total_inwards) }}
                        </td>
                        <td class="px-3.5 py-3 text-right text-purple-700 dark:text-purple-300 font-bold">
                            {{ number_format($sup->total_qty) }}
                        </td>
                        <td class="px-3.5 py-3 text-right text-emerald-600 dark:text-emerald-400 font-black text-sm">
                            ₹{{ number_format($sup->total_purchase_value, 2) }}
                        </td>
                        <td class="px-3.5 py-3 text-center text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
                            {{ $sup->last_inward_date ? \Carbon\Carbon::parse($sup->last_inward_date)->format('d M Y') : 'Never' }}
                        </td>
                        <td class="px-3.5 py-3 text-center">
                            @if($sup->status)
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
                            <button type="button" onclick="openSupplierInwardsModal({{ $sup->supplier_id }})" class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 dark:hover:bg-indigo-900 text-[11px] font-bold transition">
                                <x-heroicon-o-cube class="w-3.5 h-3.5" />
                                View Inwards
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-12 text-slate-400">
                            @if($hasSearched)
                                <p class="text-sm font-semibold">No supplier records matched your filter criteria.</p>
                                <p class="text-xs text-slate-400 mt-1">Try adjusting the search text, location, or supplier type filters.</p>
                            @else
                                <p class="text-sm font-semibold">Apply filters to generate the Supplier Analysis Report.</p>
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
