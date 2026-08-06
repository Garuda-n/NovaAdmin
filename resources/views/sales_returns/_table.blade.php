<div class="overflow-x-auto">
    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
        <thead class="bg-slate-50 dark:bg-[#0f1422] text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
            <tr>
                <th class="px-6 py-4">Return No</th>
                <th class="px-6 py-4">Return Date</th>
                <th class="px-6 py-4">Customer</th>
                <th class="px-6 py-4">Original Invoice</th>
                <th class="px-6 py-4 text-right">Grand Total (₹)</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @forelse ($salesReturns as $return)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                        <a href="{{ route('sales-returns.show', $return->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                            {{ $return->return_no_display }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                        {{ $return->return_date ? $return->return_date->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                        {{ $return->customer->customer_name ?? 'Walk-in' }}
                        <span class="block text-xs text-slate-400 font-mono">{{ $return->customer->mobile ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if ($return->sale)
                            <a href="{{ route('sales.show', $return->sale->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                                {{ $return->sale->invoice_no_display }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right font-extrabold text-slate-900 dark:text-white">
                        ₹{{ number_format($return->grand_total, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                            Completed
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('sales-returns.show', $return->id) }}" 
                               class="inline-flex items-center p-1.5 text-xs font-medium rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 transition" 
                               title="View Details">
                                <x-heroicon-o-eye class="w-3.5 h-3.5" />
                            </a>
                            <button onclick="window.print();"
                                    class="inline-flex items-center p-1.5 text-xs font-medium rounded-lg text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 transition" 
                                    title="Print Return receipt">
                                <x-heroicon-o-printer class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        No sales returns found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($salesReturns->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
        {{ $salesReturns->links() }}
    </div>
@endif
