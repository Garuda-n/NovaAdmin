<div class="space-y-6" id="stock-inward-print-area">

    <!-- Header & Action Bar -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-700/60">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-6 h-6 text-indigo-500" />
                    Bulk Stock Inward Details
                </h2>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-indigo-100 text-indigo-800 border border-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300 dark:border-indigo-500/30">
                    {{ $stockInward->invoice_no }}
                </span>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">
                Invoice Date: <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $stockInward->invoice_date ? $stockInward->invoice_date->format('d M Y') : '—' }}</span>
                | Supplier: <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $stockInward->supplier->supplier_name ?? '—' }}</span>
                | Created: <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $stockInward->created_at->format('d M Y, h:i A') }}</span>
            </p>
        </div>

        <div class="flex items-center gap-3 print:hidden">
            <button onclick="window.print()" type="button"
               class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition shadow flex items-center gap-1">
                <x-heroicon-o-printer class="w-4 h-4" /> Print
            </button>
            @if($stockInward->hasAllocatedItems())
            <span class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 text-xs font-semibold border border-amber-300 dark:border-amber-700 flex items-center gap-1" title="Bulk Inward cannot be edited because item allocation has already started.">
                <x-heroicon-o-lock-closed class="w-3.5 h-3.5" /> Locked
            </span>
            @else
            @can('stock-inwards.edit')
            <a href="{{ route('stock-inwards.edit', $stockInward) }}"
               class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold transition shadow flex items-center gap-1">
                <x-heroicon-o-pencil-square class="w-4 h-4" /> Edit Invoice
            </a>
            @endcan
            @endif
        </div>
    </div>

    @if($stockInward->hasAllocatedItems())
    <div class="bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 p-3 rounded-r-lg shadow-sm flex items-center gap-3">
        <x-heroicon-o-lock-closed class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" />
        <div>
            <h4 class="font-bold text-amber-900 dark:text-amber-200 text-xs">Status: Allocation Started (🔒 Locked)</h4>
            <p class="text-[11px] text-amber-800 dark:text-amber-300 mt-0.5">Bulk Inward cannot be edited because item allocation has already started.</p>
        </div>
    </div>
    @endif

    <!-- Header Details Card -->
    <div class="bg-slate-50 border border-slate-200 dark:bg-[#1c2538] dark:border-[#27334d] rounded-xl p-5 space-y-4">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 pb-2 border-b border-slate-200 dark:border-slate-700/60">
            Invoice Header Information
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-500 dark:text-slate-400 block mb-1">Invoice No</span>
                <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-sm">{{ $stockInward->invoice_no }}</span>
            </div>

            <div>
                <span class="text-slate-500 dark:text-slate-400 block mb-1">Invoice Date</span>
                <span class="text-slate-900 dark:text-slate-200 font-semibold text-sm">{{ $stockInward->invoice_date ? $stockInward->invoice_date->format('d M Y') : '—' }}</span>
            </div>

            <div>
                <span class="text-slate-500 dark:text-slate-400 block mb-1">Supplier</span>
                <span class="text-slate-900 dark:text-slate-200 font-semibold text-sm">{{ $stockInward->supplier->supplier_name ?? '—' }}</span>
            </div>

            <div>
                <span class="text-slate-500 dark:text-slate-400 block mb-1">Company</span>
                <span class="text-slate-900 dark:text-slate-200 font-semibold text-sm">{{ $stockInward->company->name ?? '—' }}</span>
            </div>

            <div>
                <span class="text-slate-500 dark:text-slate-400 block mb-1">Branch</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $stockInward->branch->name ?? '—' }} ({{ $stockInward->branch->branch_code ?? '' }})</span>
            </div>

            <div>
                <span class="text-slate-500 dark:text-slate-400 block mb-1">Counter</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $stockInward->counter->counter_name ?? 'N/A' }}</span>
            </div>

            <div>
                <span class="text-slate-500 dark:text-slate-400 block mb-1">Created By</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $stockInward->creator->name ?? 'System' }}</span>
            </div>

            <div>
                <span class="text-slate-500 dark:text-slate-400 block mb-1">Created At</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $stockInward->created_at->format('d M Y, h:i A') }}</span>
            </div>

            @if($stockInward->remarks)
            <div class="col-span-2 md:col-span-4 bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700 mt-1">
                <span class="text-slate-500 dark:text-slate-400 block text-[11px] font-medium uppercase mb-0.5">Invoice Remarks</span>
                <span class="text-slate-800 dark:text-slate-200 text-xs">{{ $stockInward->remarks }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Items Table Card -->
    <div class="bg-slate-50 border border-slate-200 dark:bg-[#1c2538] dark:border-[#27334d] rounded-xl p-5 space-y-4">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 pb-2 border-b border-slate-200 dark:border-slate-700/60 flex items-center gap-2">
            <x-heroicon-o-cube-transparent class="w-4 h-4 text-indigo-500" />
            Itemized Inventory List ({{ $stockInward->items->count() }} {{ Str::plural('item', $stockInward->items->count()) }})
        </h3>

        <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">#</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Category</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Product</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Sub Product</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Received Qty</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Allocated Qty</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Pending Qty</th>
                        <th class="px-3 py-2.5 text-center text-xs font-semibold uppercase text-slate-600 dark:text-slate-300 print:hidden">Allocation Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-900/40">
                    @foreach($stockInward->items as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="px-3 py-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-3 py-2 text-xs text-slate-700 dark:text-slate-300 font-medium">
                                {{ $item->product->category->name ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ $item->product->code ?? '' }} - {{ $item->product->name ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-slate-700 dark:text-slate-300">
                                @if($item->subProduct)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700">
                                        {{ $item->subProduct->code }} ({{ $item->subProduct->name }})
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-xs text-right font-mono font-bold text-slate-800 dark:text-slate-200">
                                {{ number_format($item->qty, 0) }}
                            </td>
                            <td id="item-allocated-qty-{{ $item->id }}" class="px-3 py-2 text-xs text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($item->allocated_qty ?? 0, 0) }}
                            </td>
                            <td id="item-pending-qty-{{ $item->id }}" class="px-3 py-2 text-xs text-right font-mono font-bold text-amber-600 dark:text-amber-400">
                                {{ number_format($item->pending_qty ?? $item->qty, 0) }}
                            </td>
                            <td id="item-action-cell-{{ $item->id }}" class="px-3 py-2 text-center text-xs print:hidden">
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
                <tfoot class="bg-slate-100 dark:bg-slate-800 font-semibold text-xs text-slate-800 dark:text-slate-200">
                    <tr>
                        <td colspan="4" class="px-3 py-2.5 text-right font-bold">Total Summary:</td>
                        <td class="px-3 py-2.5 text-right font-mono text-indigo-600 dark:text-indigo-400 font-bold">
                            {{ number_format($stockInward->items->sum('qty'), 0) }}
                        </td>
                        <td class="px-3 py-2.5 text-right font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                            {{ number_format($stockInward->items->sum('allocated_qty'), 0) }}
                        </td>
                        <td class="px-3 py-2.5 text-right font-mono text-amber-600 dark:text-amber-400 font-bold">
                            {{ number_format($stockInward->items->sum('pending_qty'), 0) }}
                        </td>
                        <td class="print:hidden"></td>
                    </tr>
                </tfoot>
            </table>
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
