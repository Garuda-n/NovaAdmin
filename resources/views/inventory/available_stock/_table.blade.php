<div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-slate-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-xs">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">S.No</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Item Code</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Category</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Product</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Sub Product</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Size</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Branch</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Counter</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Allocated Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($items as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400 font-medium">
                            {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-4 py-3 text-indigo-600 dark:text-indigo-400 font-semibold font-mono">
                            {{ $item->item_code }}
                        </td>
                        <td class="px-4 py-3 text-slate-800 dark:text-slate-200 font-medium">
                            {{ $item->product->category->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-800 dark:text-slate-200 font-semibold">
                            {{ $item->product->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                            {{ $item->subProduct->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                            {{ $item->size->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                            {{ $item->branch->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                            {{ $item->counter->counter_name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                            {{ $item->allocated_at ? $item->allocated_at->format('d M Y, h:i A') : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-12 text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <x-heroicon-o-cube-transparent class="w-12 h-12 text-slate-300 dark:text-slate-600" />
                                <span class="text-base font-semibold text-slate-600 dark:text-slate-300">No available stock found.</span>
                                <span class="text-xs text-slate-400">Try adjusting your search or filter parameters.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700 pagination-wrapper">
            {{ $items->links() }}
        </div>
    @endif
</div>
