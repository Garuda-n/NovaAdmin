@props([
    'reportData' => [],
])

@php
    $items = $reportData['items'] ?? [];
    $summary = $reportData['summary'] ?? [];
    $paginator = $reportData['paginator'] ?? null;
    $hasSearched = $reportData['has_searched'] ?? false;
@endphp

<div class="space-y-6">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-4 border border-gray-200 dark:border-slate-700">
            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total Opening Qty</span>
            <span class="block text-lg font-bold text-gray-800 dark:text-white mt-1">{{ number_format($summary['total_opening'] ?? 0, 2) }}</span>
        </div>
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-4 border border-gray-200 dark:border-slate-700">
            <span class="block text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Total Inward Qty</span>
            <span class="block text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-1">+{{ number_format($summary['total_inward'] ?? 0, 2) }}</span>
        </div>
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-4 border border-gray-200 dark:border-slate-700">
            <span class="block text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase">Total Outward Qty</span>
            <span class="block text-lg font-bold text-rose-600 dark:text-rose-400 mt-1">-{{ number_format($summary['total_outward'] ?? 0, 2) }}</span>
        </div>
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-4 border border-gray-200 dark:border-slate-700">
            <span class="block text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase">Total Closing Qty</span>
            <span class="block text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($summary['total_closing'] ?? 0, 2) }}</span>
        </div>
    </div>

    {{-- Main Stock Register Table --}}
    <div class="bg-white dark:bg-slate-800 shadow rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-xs">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Item Code</th>
                        <th scope="col" class="px-4 py-3 text-left font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-4 py-3 text-right font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Opening Qty</th>
                        <th scope="col" class="px-4 py-3 text-right font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Inward Qty</th>
                        <th scope="col" class="px-4 py-3 text-right font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Outward Qty</th>
                        <th scope="col" class="px-4 py-3 text-right font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Closing Qty</th>
                        <th scope="col" class="px-4 py-3 text-center font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse($items as $row)
                        @php
                            $itemCode = is_object($row) ? ($row->item_code ?? '-') : ($row['item_code'] ?? '-');
                            $productName = is_object($row) ? ($row->product_name ?? '-') : ($row['product_name'] ?? '-');
                            $openingQty = is_object($row) ? ($row->opening_qty ?? 0) : ($row['opening_qty'] ?? 0);
                            $inwardQty = is_object($row) ? ($row->inward_qty ?? 0) : ($row['inward_qty'] ?? 0);
                            $outwardQty = is_object($row) ? ($row->outward_qty ?? 0) : ($row['outward_qty'] ?? 0);
                            $closingQty = is_object($row) ? ($row->closing_qty ?? 0) : ($row['closing_qty'] ?? 0);
                            $uomName = is_object($row) ? ($row->uom_name ?? '-') : ($row['uom_name'] ?? '-');
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                            <td class="px-4 py-3 font-mono text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ $itemCode }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $productName }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ number_format($openingQty, 2) }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-semibold whitespace-nowrap">{{ number_format($inwardQty, 2) }}</td>
                            <td class="px-4 py-3 text-right text-rose-600 dark:text-rose-400 font-semibold whitespace-nowrap">{{ number_format($outwardQty, 2) }}</td>
                            <td class="px-4 py-3 text-right text-indigo-600 dark:text-indigo-400 font-bold whitespace-nowrap">{{ number_format($closingQty, 2) }}</td>
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $uomName }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm font-medium text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    @if($hasSearched)
                                        <x-heroicon-o-archive-box-x-mark class="w-10 h-10 text-gray-300 dark:text-slate-600" />
                                        <span>No stock records found.</span>
                                    @else
                                        <x-heroicon-o-magnifying-glass class="w-10 h-10 text-indigo-400 dark:text-indigo-500" />
                                        <span class="text-gray-600 dark:text-gray-300 font-semibold">Set your filters and click "Search" to view the report data.</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())
            <div class="px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700">
                {{ $paginator->links() }}
            </div>
        @endif
    </div>

</div>
