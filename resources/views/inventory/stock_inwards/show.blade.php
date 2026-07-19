<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Bulk Stock Inward Details — {{ $stockInward->invoice_no }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('stock-inwards.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition">
                    ← Back to List
                </a>
                @can('stock-inwards.edit')
                <a href="{{ route('stock-inwards.edit', $stockInward) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-lg transition flex items-center gap-1">
                    <x-heroicon-o-pencil-square class="w-4 h-4" /> Edit
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Header Info Card -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 border border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-slate-700 pb-3 flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5 text-indigo-500" />
                    Invoice Header Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-sm">
                    <div>
                        <span class="block text-xs uppercase text-gray-500 dark:text-gray-400 font-medium">Invoice No</span>
                        <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-base">{{ $stockInward->invoice_no }}</span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase text-gray-500 dark:text-gray-400 font-medium">Invoice Date</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $stockInward->invoice_date ? $stockInward->invoice_date->format('d M Y') : '—' }}</span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase text-gray-500 dark:text-gray-400 font-medium">Supplier</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $stockInward->supplier->supplier_name ?? '—' }}</span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase text-gray-500 dark:text-gray-400 font-medium">Company</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $stockInward->company->name ?? '—' }}</span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase text-gray-500 dark:text-gray-400 font-medium">Branch</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $stockInward->branch->name ?? '—' }} ({{ $stockInward->branch->branch_code ?? '' }})</span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase text-gray-500 dark:text-gray-400 font-medium">Counter</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $stockInward->counter->counter_name ?? 'N/A' }}</span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase text-gray-500 dark:text-gray-400 font-medium">Created By</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $stockInward->creator->name ?? 'System' }}</span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase text-gray-500 dark:text-gray-400 font-medium">Created At</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $stockInward->created_at->format('d M Y, h:i A') }}</span>
                    </div>

                    @if($stockInward->remarks)
                    <div class="md:col-span-4 bg-gray-50 dark:bg-slate-700/50 p-3 rounded-lg border border-gray-200 dark:border-slate-600">
                        <span class="block text-xs uppercase text-gray-500 dark:text-gray-400 font-medium mb-1">Remarks</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $stockInward->remarks }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 border border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-slate-700 pb-3 flex items-center gap-2">
                    <x-heroicon-o-cube-transparent class="w-5 h-5 text-indigo-500" />
                    Itemized Inventory List ({{ $stockInward->items->count() }} items)
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-100 dark:bg-slate-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Product Code & Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Sub Product</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Quantity</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Weight</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Purchase Price</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Selling Price</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">MRP</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            @foreach($stockInward->items as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $item->product->code ?? '' }} - {{ $item->product->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        @if($item->subProduct)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                                                {{ $item->subProduct->code }} ({{ $item->subProduct->name }})
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ number_format($item->qty, 3) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-700 dark:text-gray-300">
                                        {{ $item->weight !== null ? number_format($item->weight, 3) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-700 dark:text-gray-300">
                                        {{ $item->purchase_price !== null ? '₹' . number_format($item->purchase_price, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-700 dark:text-gray-300">
                                        {{ $item->selling_price !== null ? '₹' . number_format($item->selling_price, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-700 dark:text-gray-300">
                                        {{ $item->mrp !== null ? '₹' . number_format($item->mrp, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $item->remarks ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 dark:bg-slate-700/80 font-semibold text-gray-800 dark:text-gray-200">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right">Total Summary:</td>
                                <td class="px-4 py-3 text-right font-mono text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($stockInward->items->sum('qty'), 3) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono">
                                    {{ number_format($stockInward->items->sum('weight'), 3) }}
                                </td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
