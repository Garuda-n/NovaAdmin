<!-- Items Preview Table Partial -->
<div class="bg-white dark:bg-[#182035] rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Items Preview (From Quotation #{{ $quotation->quotation_no }})
        </h2>
        <span class="text-xs text-slate-500 dark:text-slate-400">Total Items: {{ count($itemsPreview) }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-[#0f1422] text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Product Name & Code</th>
                    <th class="px-4 py-3 text-right">Qty</th>
                    <th class="px-4 py-3 text-right">Rate (₹)</th>
                    <th class="px-4 py-3 text-right">Tax (%)</th>
                    <th class="px-4 py-3 text-right">Tax Amount (₹)</th>
                    <th class="px-4 py-3 text-right">Line Total (₹)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @foreach ($itemsPreview as $index => $item)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                        <td class="px-4 py-3 text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">
                            {{ $item['product_name'] }}
                            <span class="block text-xs text-slate-400 font-mono">{{ $item['product_code'] }}</span>
                            @if(!empty($item['allocated_item_id']))
                                @php
                                    $allocatedStockItem = \App\Models\StockItem::find($item['allocated_item_id']);
                                @endphp
                                @if($allocatedStockItem)
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-xs font-semibold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                                        Serial: {{ $allocatedStockItem->item_code }}
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($item['quantity'], 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($item['rate'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-indigo-600 dark:text-indigo-400 font-medium">{{ number_format($item['tax_percentage'], 2) }}%</td>
                        <td class="px-4 py-3 text-right">{{ number_format($item['tax_amount'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white">₹{{ number_format($item['line_total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
