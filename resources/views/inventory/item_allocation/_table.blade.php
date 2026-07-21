<div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
            <thead class="bg-gray-100 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Invoice No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Date & Supplier</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Branch</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Category & Product</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Sub Product</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Received Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Allocated Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Pending Qty</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                        
                        <!-- Invoice No -->
                        <td class="px-4 py-3.5 font-mono font-semibold text-indigo-600 dark:text-indigo-400 text-sm">
                            <a href="{{ route('stock-inwards.show', $item->stockInward) }}" class="hover:underline">
                                {{ $item->stockInward->invoice_no ?? '—' }}
                            </a>
                        </td>

                        <!-- Date & Supplier -->
                        <td class="px-4 py-3.5 text-xs text-gray-800 dark:text-gray-200">
                            <div class="font-semibold">{{ $item->stockInward->supplier->supplier_name ?? '—' }}</div>
                            <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                {{ $item->stockInward->invoice_date ? $item->stockInward->invoice_date->format('d M Y') : '—' }}
                            </div>
                        </td>

                        <!-- Branch -->
                        <td class="px-4 py-3.5 text-xs text-gray-800 dark:text-gray-200">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                {{ $item->stockInward->branch->name ?? '—' }}
                            </span>
                        </td>

                        <!-- Category & Product -->
                        <td class="px-4 py-3.5 text-xs">
                            <div class="font-bold text-slate-900 dark:text-white">
                                {{ $item->product->code ?? '' }} - {{ $item->product->name ?? '—' }}
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                Category: {{ $item->product->category->name ?? '—' }}
                            </div>
                        </td>

                        <!-- Sub Product -->
                        <td class="px-4 py-3.5 text-xs">
                            @if($item->subProduct)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                                    {{ $item->subProduct->code }} ({{ $item->subProduct->name }})
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <!-- Received Qty -->
                        <td class="px-4 py-3.5 text-xs text-right font-mono font-bold text-slate-800 dark:text-slate-200">
                            {{ number_format($item->qty, 0) }}
                        </td>

                        <!-- Allocated Qty -->
                        <td id="item-allocated-qty-{{ $item->id }}" class="px-4 py-3.5 text-xs text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                            {{ number_format($item->allocated_qty ?? 0, 0) }}
                        </td>

                        <!-- Pending Qty -->
                        <td id="item-pending-qty-{{ $item->id }}" class="px-4 py-3.5 text-xs text-right font-mono font-bold text-amber-600 dark:text-amber-400">
                            {{ number_format($item->pending_qty ?? $item->qty, 0) }}
                        </td>

                        <!-- Action -->
                        <td id="item-action-cell-{{ $item->id }}" class="px-4 py-3.5 text-center text-xs">
                            @if(($item->pending_qty ?? $item->qty) > 0)
                                <button type="button" onclick="openAllocationModal({{ $item->id }})"
                                    class="px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow transition inline-flex items-center gap-1">
                                    <x-heroicon-o-cube-transparent class="w-4 h-4" /> Allocate
                                </button>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300">
                                    Allocation Completed
                                </span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No pending individual item allocation lines found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="p-4 border-t border-gray-200 dark:border-slate-700">
            {{ $items->links() }}
        </div>
    @endif
</div>
