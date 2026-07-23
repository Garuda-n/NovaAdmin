<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Header Section -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                            Quotation #{{ $quotation->quotation_no ?? $quotation->id }}
                        </h1>
                        @if($quotation->status == 2)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                Converted
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Created
                            </span>
                        @endif
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Read-only quotation summary and item breakdown.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    @if($quotation->status != 2)
                        @can('quotation.edit')
                        <a href="{{ route('quotations.edit', $quotation) }}"
                           class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition">
                            Edit Quotation
                        </a>
                        @endcan
                    @endif

                    @can('quotation.print')
                    <a href="{{ route('quotations.pdf', $quotation) }}"
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition">
                        PDF
                    </a>
                    @endcan

                    <a href="{{ route('quotations.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg transition">
                        ← Back to List
                    </a>
                </div>
            </div>

            <!-- Card 1: Header Information (Read Only) -->
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Header Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-sm">
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Branch</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $quotation->branch->name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Counter</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $quotation->counter->counter_name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Business Date</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ $quotation->business_date ? \Carbon\Carbon::parse($quotation->business_date)->format('Y-m-d') : '-' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Quotation Number</span>
                        <span class="font-medium text-slate-900 dark:text-white">#{{ $quotation->quotation_no ?? $quotation->id }}</span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Customer</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $quotation->customer->customer_name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Customer Type</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 mt-0.5">
                            {{ $quotation->customer_type ?? 'B2C' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Created By</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $quotation->creator->name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Created At</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $quotation->created_at ? $quotation->created_at->format('Y-m-d H:i') : '-' }}</span>
                    </div>

                    @if($quotation->remarks)
                        <div class="lg:col-span-4">
                            <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Remarks</span>
                            <p class="font-medium text-slate-800 dark:text-slate-200 mt-1 bg-slate-50 dark:bg-slate-900/50 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                {{ $quotation->remarks }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card 2: Product Details Table (Read Only) -->
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 border border-slate-200 dark:border-slate-700 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                    Product Details
                </h3>

                <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                    <table class="w-full text-xs text-left text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-3 py-3 w-10 text-center">#</th>
                                <th class="px-3 py-3">Product</th>
                                <th class="px-3 py-3">UOM</th>
                                <th class="px-3 py-3 text-right">Qty</th>
                                <th class="px-3 py-3 text-right">Rate</th>
                                <th class="px-3 py-3 text-right">Tax %</th>
                                <th class="px-3 py-3 text-right">Tax Amount</th>
                                <th class="px-3 py-3 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($quotation->details as $index => $item)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                    <td class="px-3 py-2 text-center font-medium text-slate-500">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-slate-900 dark:text-white">
                                        {{ $item->product_name ?? $item->product->name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $item->uom_name ?? $item->uom->name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        {{ number_format($item->qty, 3) }}
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        ₹ {{ number_format($item->rate, 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        {{ number_format($item->tax_percent, 2) }}%
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        ₹ {{ number_format($item->tax_amount, 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right font-semibold text-slate-900 dark:text-white">
                                        ₹ {{ number_format($item->line_total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">
                                        No product details attached to this quotation.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card 3: Summary Section (Right Aligned) -->
            <div class="flex justify-end">
                <div class="w-full sm:w-80 bg-white dark:bg-slate-800 shadow-sm rounded-xl p-5 border border-slate-200 dark:border-slate-700 space-y-3">
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 pb-2">
                        Summary
                    </h4>

                    <div class="flex justify-between items-center text-xs text-slate-600 dark:text-slate-400">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-900 dark:text-white">₹ {{ number_format($quotation->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs text-slate-600 dark:text-slate-400">
                        <span>Tax Amount</span>
                        <span class="font-semibold text-slate-900 dark:text-white">₹ {{ number_format($quotation->tax_amount, 2) }}</span>
                    </div>

                    <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center text-sm font-bold text-slate-900 dark:text-white">
                        <span>Grand Total</span>
                        <span class="text-indigo-600 dark:text-indigo-400 text-base">₹ {{ number_format($quotation->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
