<div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
            <thead class="bg-gray-100 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Ref No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Invoice No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Company & Branch</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Supplier</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Items</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($stockInwards as $inward)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                        
                        <!-- Ref No -->
                        <td class="px-6 py-4 font-mono font-semibold text-gray-700 dark:text-gray-300 text-sm">
                            {{ $inward->id }}
                        </td>

                        <!-- Invoice No -->
                        <td class="px-6 py-4 font-mono font-semibold text-indigo-600 dark:text-indigo-400 text-sm">
                            <a href="{{ route('stock-inwards.show', $inward) }}"
                               @click.prevent="openInwardModal({{ $inward->id }})"
                               class="hover:underline cursor-pointer">
                                {{ $inward->invoice_no }}
                            </a>
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                            {{ $inward->invoice_date ? $inward->invoice_date->format('d M Y') : '—' }}
                        </td>

                        <!-- Company & Branch -->
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                            <div class="font-medium">{{ $inward->company->name ?? '—' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $inward->branch->name ?? '—' }}{{ $inward->counter ? ' (' . $inward->counter->counter_name . ')' : '' }}</div>
                        </td>

                        <!-- Supplier -->
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                            {{ $inward->supplier->supplier_name ?? '—' }}
                        </td>

                        <!-- Items Count -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300">
                                {{ $inward->items_count }} {{ Str::plural('item', $inward->items_count) }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <!-- View Popup Button -->
                                <button type="button"
                                    @click.prevent="openInwardModal({{ $inward->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition cursor-pointer"
                                    title="Quick View Invoice">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>

                                @can('stock-inwards.edit')
                                <a href="{{ route('stock-inwards.edit', $inward) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition"
                                    title="Edit Invoice">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                                @endcan

                                @can('stock-inwards.delete')
                                <form action="{{ route('stock-inwards.destroy', $inward) }}" method="POST"
                                      onsubmit="return confirm('Delete this bulk stock inward invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white transition"
                                        title="Delete Invoice">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No bulk stock inward records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stockInwards->hasPages())
        <div class="p-4 border-t border-gray-200 dark:border-slate-700">
            {{ $stockInwards->links() }}
        </div>
    @endif
</div>
