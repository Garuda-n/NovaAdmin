<div class="overflow-x-auto">
    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
        <thead class="bg-slate-50 dark:bg-[#0f1422] text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
            <tr>
                <th class="px-6 py-4">Customer</th>
                <th class="px-6 py-4">Invoice No</th>
                <th class="px-6 py-4">Invoice Date</th>
                <th class="px-6 py-4 text-right">Original Amount (₹)</th>
                <th class="px-6 py-4 text-right">Paid Amount (₹)</th>
                <th class="px-6 py-4 text-right">Balance Amount (₹)</th>
                <th class="px-6 py-4">Due Date</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @forelse ($receivables as $receivable)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                        {{ $receivable->customer->customer_name ?? 'Customer' }}
                        <span class="block text-xs text-slate-400 font-mono">{{ $receivable->customer->mobile ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                        @if($receivable->sale)
                            <a href="{{ route('sales.show', $receivable->sale->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $receivable->sale->invoice_no_display }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                        {{ $receivable->invoice_date ? $receivable->invoice_date->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right font-medium text-slate-900 dark:text-white">
                        ₹{{ number_format($receivable->original_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                        ₹{{ number_format($receivable->paid_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 text-right font-extrabold text-rose-600 dark:text-rose-400">
                        ₹{{ number_format($receivable->balance_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 font-medium {{ $receivable->isOverdue() ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-600 dark:text-slate-300' }}">
                        {{ $receivable->due_date ? $receivable->due_date->format('d M Y') : '-' }}
                        @if($receivable->isOverdue())
                            <span class="block text-[10px] text-rose-500 uppercase font-semibold">Overdue</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if ($receivable->isPaid())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                Paid
                            </span>
                        @elseif ($receivable->isPartiallyPaid())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                Partially Paid
                            </span>
                        @elseif ($receivable->isPending())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300">
                                Pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                Cancelled
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if($receivable->sale)
                                <a href="{{ route('sales.show', $receivable->sale->id) }}" class="p-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-medium transition">
                                    View Invoice
                                </a>
                                @if(!$receivable->isPaid() && $receivable->sale->isCompleted())
                                    @can('receivable.allocate')
                                        <a href="{{ route('sales.show', $receivable->sale->id) }}?collect=1" class="p-1.5 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-lg text-xs font-semibold transition">
                                            Collect
                                        </a>
                                    @endcan
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        No customer receivables found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($receivables->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
        {{ $receivables->links() }}
    </div>
@endif
