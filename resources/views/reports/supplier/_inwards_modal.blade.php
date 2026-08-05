<div class="space-y-5">
    <!-- Supplier Info & Procurement Summary Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50 dark:bg-slate-700/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $supplier->supplier_name }}</h3>
                <span class="px-2 py-0.5 rounded text-xs font-extrabold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                    {{ $supplier->supplier_type ?? 'Supplier' }}
                </span>
                @if($supplier->supplier_code)
                    <span class="text-xs font-mono text-slate-400">({{ $supplier->supplier_code }})</span>
                @endif
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-3">
                @if($supplier->contact_person)
                    <span>👤 {{ $supplier->contact_person }}</span>
                @endif
                @if($supplier->mobile)
                    <span>📱 {{ $supplier->mobile }}</span>
                @endif
                @if($supplier->email)
                    <span>✉️ {{ $supplier->email }}</span>
                @endif
                @if($supplier->gst_number)
                    <span class="font-mono text-slate-600 dark:text-slate-300">GST: {{ $supplier->gst_number }}</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs shrink-0">
            <div class="text-right">
                <span class="block text-[10px] text-slate-500 uppercase font-bold">Total Inward Value</span>
                <span class="text-base font-black text-emerald-600 dark:text-emerald-400">₹{{ number_format($totalProcurementValue, 2) }}</span>
            </div>
            <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
            <div class="text-right">
                <span class="block text-[10px] text-slate-500 uppercase font-bold">Inward Bills</span>
                <span class="text-base font-black text-indigo-600 dark:text-indigo-400">{{ number_format($totalInwardsCount) }}</span>
            </div>
            <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
            <div class="text-right">
                <span class="block text-[10px] text-slate-500 uppercase font-bold">Items Procured</span>
                <span class="text-base font-black text-purple-600 dark:text-purple-400">{{ number_format($totalQtyProcured) }}</span>
            </div>
        </div>
    </div>

    <!-- Inward Stock Invoices Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto max-h-[60vh]">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-xs">
                <thead class="bg-slate-100 dark:bg-slate-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Inward Bill No</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Invoice Date</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Branch / Counter</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Items Count</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Total Qty</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Inward Total Value</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Status</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($inwards as $inward)
                        @php
                            $inwardQty = $inward->items->sum('qty');
                            $inwardValue = $inward->items->sum(function($item) {
                                return ($item->qty * ($item->purchase_price ?? 0));
                            });
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-3.5 py-3 font-extrabold text-indigo-600 dark:text-indigo-400">
                                <a href="{{ route('stock-inwards.show', $inward->id) }}" target="_blank" class="hover:underline">
                                    #{{ $inward->invoice_no }}
                                </a>
                            </td>
                            <td class="px-3.5 py-3 text-slate-600 dark:text-slate-300 font-medium whitespace-nowrap">
                                {{ $inward->invoice_date ? \Carbon\Carbon::parse($inward->invoice_date)->format('d M Y') : '—' }}
                            </td>
                            <td class="px-3.5 py-3 text-slate-600 dark:text-slate-400">
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $inward->branch->name ?? '—' }}</span>
                                @if($inward->counter)
                                    <span class="text-slate-400 text-[10px]"> ({{ $inward->counter->counter_name }})</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 text-center font-bold text-slate-700 dark:text-slate-300">
                                {{ $inward->items->count() }}
                            </td>
                            <td class="px-3.5 py-3 text-right text-purple-700 dark:text-purple-300 font-bold">
                                {{ number_format($inwardQty) }}
                            </td>
                            <td class="px-3.5 py-3 text-right text-emerald-600 dark:text-emerald-400 font-black text-sm">
                                ₹{{ number_format($inwardValue, 2) }}
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                @if($inward->status)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        Received
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                        Draft / Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                <a href="{{ route('stock-inwards.show', $inward->id) }}" target="_blank" class="p-1.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold" title="View Inward Stock Details">
                                    <x-heroicon-o-eye class="w-4 h-4 inline" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                No stock inward invoices found for this supplier.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
