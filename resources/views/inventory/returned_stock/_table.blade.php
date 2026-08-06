<div class="overflow-x-auto">
    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
        <thead class="bg-slate-50 dark:bg-[#0f1422] text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
            <tr>
                <th class="px-6 py-4">Returned Serial Code</th>
                <th class="px-6 py-4">Product Name & Code</th>
                <th class="px-6 py-4">Branch / Counter</th>
                <th class="px-6 py-4">Return Document</th>
                <th class="px-6 py-4">Return Date</th>
                @if (request('current_status', 'pending') === 'recreated')
                    <th class="px-6 py-4">Recreated Serial Code</th>
                @endif
                <th class="px-6 py-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @forelse ($stockItems as $item)
                @php
                    // Retrieve corresponding detail line linked to this stock item return
                    $returnDetail = $item->salesReturnAsOriginal->first();
                @endphp
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white font-mono">
                        {{ $item->item_code }}
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                        {{ $item->product->name ?? '-' }}
                        <span class="block text-xs text-slate-400 font-mono">{{ $item->product->code ?? '' }} • {{ $item->product->category->name ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        {{ $item->branch->branch_name ?? '-' }}
                        <span class="block text-xs text-slate-400 font-medium">Counter: {{ $item->counter->counter_name ?? 'POS' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if ($returnDetail && $returnDetail->salesReturn)
                            <a href="{{ route('sales-returns.show', $returnDetail->salesReturn->id) }}" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">
                                {{ $returnDetail->salesReturn->return_no_display }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                        @if ($returnDetail && $returnDetail->salesReturn)
                            {{ $returnDetail->salesReturn->return_date ? $returnDetail->salesReturn->return_date->format('d M Y') : '-' }}
                        @else
                            -
                        @endif
                    </td>
                    @if (request('current_status', 'pending') === 'recreated')
                        <td class="px-6 py-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                            @if ($returnDetail && $returnDetail->recreatedStockItem)
                                {{ $returnDetail->recreatedStockItem->item_code }}
                            @else
                                -
                            @endif
                        </td>
                    @endif
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if (request('current_status', 'pending') === 'pending')
                                @if ($returnDetail)
                                    @can('returned-stock.recreate')
                                        <button type="button" 
                                                id="recreate_btn_{{ $returnDetail->id }}"
                                                onclick="triggerRecreation({{ $returnDetail->id }}, '{{ $item->item_code }}')" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                            <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                                            Recreate Item
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">No Permission</span>
                                    @endcan
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Orphan Item</span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    Recreated & Active
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ request('current_status', 'pending') === 'recreated' ? '7' : '6' }}" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        No returned serialized stock items found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($stockItems->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
        {{ $stockItems->links() }}
    </div>
@endif
