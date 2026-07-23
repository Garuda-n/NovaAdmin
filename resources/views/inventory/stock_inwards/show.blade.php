<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Bulk Stock Inward Details — {{ $stockInward->invoice_no }}
            </h2>
            <div class="flex gap-2 print:hidden">
                <button onclick="window.print()" type="button" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition flex items-center gap-1">
                    <x-heroicon-o-printer class="w-4 h-4" /> Print
                </button>
                <a href="{{ route('stock-inwards.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition">
                    ← Back to List
                </a>
                @if($stockInward->hasAllocatedItems())
                    <span class="px-4 py-2 bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 text-sm font-semibold rounded-lg border border-amber-300 dark:border-amber-700 flex items-center gap-1.5" title="Bulk Inward cannot be edited because item allocation has already started.">
                        <x-heroicon-o-lock-closed class="w-4 h-4" /> Locked (Allocation Started)
                    </span>
                @else
                    @can('stock-inwards.edit')
                    <a href="{{ route('stock-inwards.edit', $stockInward) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-lg transition flex items-center gap-1">
                        <x-heroicon-o-pencil-square class="w-4 h-4" /> Edit
                    </a>
                    @endcan
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6" id="stock-inward-print-area">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($stockInward->hasAllocatedItems())
            <!-- Locked Alert Banner -->
            <div class="bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm flex items-center gap-3">
                <x-heroicon-o-lock-closed class="w-6 h-6 text-amber-600 dark:text-amber-400 shrink-0" />
                <div>
                    <h4 class="font-bold text-amber-900 dark:text-amber-200 text-sm">Status: Allocation Started (🔒 Locked)</h4>
                    <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5">Bulk Inward cannot be edited because item allocation has already started.</p>
                </div>
            </div>
            @endif

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
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Product Code & Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Sub Product</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Received Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Allocated Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Pending Qty</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 print:hidden">Allocation Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            @foreach($stockInward->items as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                        {{ $item->product->category->name ?? '—' }}
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
                                    <td class="px-4 py-3 text-sm text-right font-mono font-bold text-slate-800 dark:text-slate-200">
                                        {{ number_format($item->qty, 0) }}
                                    </td>
                                    <td id="item-allocated-qty-{{ $item->id }}" class="px-4 py-3 text-sm text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($item->allocated_qty ?? 0, 0) }}
                                    </td>
                                    <td id="item-pending-qty-{{ $item->id }}" class="px-4 py-3 text-sm text-right font-mono font-bold text-amber-600 dark:text-amber-400">
                                        {{ number_format($item->pending_qty ?? $item->qty, 0) }}
                                    </td>
                                    <td id="item-action-cell-{{ $item->id }}" class="px-4 py-3 text-center text-xs print:hidden">
                                        @if($item->product && $item->product->tracking_type === \App\Models\Product::TRACKING_INDIVIDUAL)
                                            @if(($item->pending_qty ?? $item->qty) > 0)
                                                <button type="button" onclick="openAllocationModal({{ $item->id }})"
                                                    class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow transition inline-flex items-center gap-1">
                                                    <x-heroicon-o-cube-transparent class="w-3.5 h-3.5" /> Allocate
                                                </button>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300">
                                                    Allocation Completed
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Bulk Tracking</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 dark:bg-slate-700/80 font-semibold text-gray-800 dark:text-gray-200">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right">Total Summary:</td>
                                <td class="px-4 py-3 text-right font-mono text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($stockInward->items->sum('qty'), 0) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($stockInward->items->sum('allocated_qty'), 0) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-amber-600 dark:text-amber-400">
                                    {{ number_format($stockInward->items->sum('pending_qty'), 0) }}
                                </td>
                                <td class="print:hidden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Include Allocation Modal Partial -->
    @include('inventory.stock_inwards._allocation_modal')

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #stock-inward-print-area, #stock-inward-print-area * {
        visibility: visible !important;
    }
    #stock-inward-print-area {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        margin: 0 !important;
        padding: 20px !important;
        background: white !important;
        color: black !important;
        z-index: 99999 !important;
    }
    .print\:hidden {
        display: none !important;
    }
}
</style>
</x-app-layout>
