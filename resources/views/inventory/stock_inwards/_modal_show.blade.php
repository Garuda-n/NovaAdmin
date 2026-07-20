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
            @can('stock-inwards.edit')
            <a href="{{ route('stock-inwards.edit', $stockInward) }}"
               class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold transition shadow flex items-center gap-1">
                <x-heroicon-o-pencil-square class="w-4 h-4" /> Edit Invoice
            </a>
            @endcan
        </div>
    </div>

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
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Product</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Sub Product</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Qty</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Weight</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Purchase Price</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Selling Price</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">MRP</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-900/40">
                    @foreach($stockInward->items as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="px-3 py-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                {{ $index + 1 }}
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
                            <td class="px-3 py-2 text-xs text-right font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ number_format($item->qty, 3) }}
                            </td>
                            <td class="px-3 py-2 text-xs text-right font-mono text-slate-700 dark:text-slate-300">
                                {{ $item->weight !== null ? number_format($item->weight, 3) : '—' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-right font-mono text-slate-700 dark:text-slate-300">
                                {{ $item->purchase_price !== null ? '₹' . number_format($item->purchase_price, 2) : '—' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-right font-mono text-slate-700 dark:text-slate-300">
                                {{ $item->selling_price !== null ? '₹' . number_format($item->selling_price, 2) : '—' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-right font-mono text-slate-700 dark:text-slate-300">
                                {{ $item->mrp !== null ? '₹' . number_format($item->mrp, 2) : '—' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-slate-600 dark:text-slate-400">
                                {{ $item->remarks ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-100 dark:bg-slate-800 font-semibold text-xs text-slate-800 dark:text-slate-200">
                    <tr>
                        <td colspan="3" class="px-3 py-2.5 text-right font-bold">Total Summary:</td>
                        <td class="px-3 py-2.5 text-right font-mono text-indigo-600 dark:text-indigo-400 font-bold">
                            {{ number_format($stockInward->items->sum('qty'), 3) }}
                        </td>
                        <td class="px-3 py-2.5 text-right font-mono font-bold">
                            {{ number_format($stockInward->items->sum('weight'), 3) }}
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
</div>

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
