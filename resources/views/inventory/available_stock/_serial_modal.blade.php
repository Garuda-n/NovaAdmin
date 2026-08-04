<div class="p-6">
    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-slate-700">
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <x-heroicon-o-barcode class="w-5 h-5 text-indigo-500" />
                Serialized Item Breakdown
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                List of active allocated item codes for this product at the selected location.
            </p>
        </div>
        <button type="button" onclick="closeSerialModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
    </div>

    <div class="overflow-x-auto max-h-96">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-xs">
            <thead class="bg-slate-50 dark:bg-slate-700/50 sticky top-0">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold text-slate-700 dark:text-slate-300">#</th>
                    <th class="px-3 py-2 text-left font-semibold text-slate-700 dark:text-slate-300">Item Code</th>
                    <th class="px-3 py-2 text-left font-semibold text-slate-700 dark:text-slate-300">Sub Product</th>
                    <th class="px-3 py-2 text-left font-semibold text-slate-700 dark:text-slate-300">Size</th>
                    <th class="px-3 py-2 text-left font-semibold text-slate-700 dark:text-slate-300">Branch</th>
                    <th class="px-3 py-2 text-left font-semibold text-slate-700 dark:text-slate-300">Counter</th>
                    <th class="px-3 py-2 text-left font-semibold text-slate-700 dark:text-slate-300">Allocated Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($items as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                        <td class="px-3 py-2 text-slate-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-3 py-2 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $item->item_code }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $item->subProduct->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $item->size->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $item->branch->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $item->counter->counter_name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-500 dark:text-slate-400">{{ $item->allocated_at ? $item->allocated_at->format('d M Y, h:i A') : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-slate-400">No serialized items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-slate-700 flex justify-end">
        <button type="button" onclick="closeSerialModal()" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-xs font-semibold rounded-lg transition">
            Close
        </button>
    </div>
</div>
