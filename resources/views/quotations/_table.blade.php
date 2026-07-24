<div class="overflow-x-auto">
    <table class="w-full text-xs text-left text-slate-600 dark:text-slate-300">
        <thead class="text-xs uppercase bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
            <tr>
                <th scope="col" class="px-3 py-2">Quotation No</th>
                <th scope="col" class="px-3 py-2">Business Date</th>
                <th scope="col" class="px-3 py-2">Branch</th>
                <th scope="col" class="px-3 py-2">Counter</th>
                <th scope="col" class="px-3 py-2">Customer</th>
                <th scope="col" class="px-3 py-2">Customer Type</th>
                <th scope="col" class="px-3 py-2 text-right">Grand Total</th>
                <th scope="col" class="px-3 py-2 text-center">Status</th>
                <th scope="col" class="px-3 py-2">Created By</th>
                <th scope="col" class="px-3 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
            @forelse($quotations as $quotation)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-3 py-2 font-semibold text-slate-900 dark:text-white">
                        #{{ $quotation->quotation_no ?? $quotation->id }}
                    </td>
                    <td class="px-3 py-2">
                        {{ $quotation->business_date ? \Carbon\Carbon::parse($quotation->business_date)->format('Y-m-d') : '-' }}
                    </td>
                    <td class="px-3 py-2">
                        {{ $quotation->branch->name ?? '-' }}
                    </td>
                    <td class="px-3 py-2">
                        {{ $quotation->counter->counter_name ?? '-' }}
                    </td>
                    <td class="px-3 py-2">
                        {{ $quotation->customer->customer_name ?? '-' }}
                    </td>
                    <td class="px-3 py-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $quotation->customer_type === 'B2B' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300' }}">
                            {{ $quotation->customer_type ?? 'B2C' }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-right font-semibold text-slate-900 dark:text-white">
                        {{ number_format($quotation->grand_total, 2) }}
                    </td>
                    <td class="px-3 py-2 text-center">
                        @if($quotation->status == 2)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                Converted
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Created
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-2">
                        {{ $quotation->creator->name ?? '-' }}
                    </td>
                    <td class="px-3 py-2 text-right whitespace-nowrap space-x-1.5">
                        <!-- Edit Button (Hidden when status = Converted) -->
                        @if($quotation->status != 2)
                            @can('quotation.edit')
                            <a href="{{ route('quotations.edit', $quotation) }}"
                               class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-lg text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 border border-amber-200 dark:border-amber-800 transition">
                                <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                            </a>
                            @endcan
                        @endif

                        <!-- PDF Button -->
                        @can('quotation.print')
                        <a href="{{ route('quotations.pdf', $quotation) }}"
                           target="_blank"
                           class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-600 transition">
                            <x-heroicon-o-document-text class="w-3.5 h-3.5" />
                        </a>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                        No quotations found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($quotations->hasPages())
    <div class="p-4 border-t border-slate-200 dark:border-slate-700">
        {{ $quotations->links() }}
    </div>
@endif
