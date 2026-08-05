<div class="space-y-5">
    <!-- Customer Info & Summary Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50 dark:bg-slate-700/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $customer->customer_name }}</h3>
                <span class="px-2 py-0.5 rounded text-xs font-extrabold {{ $customer->customer_type === 'B2B' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-slate-200 text-slate-800 dark:bg-slate-600 dark:text-slate-200' }}">
                    {{ $customer->customer_type }}
                </span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-3">
                @if($customer->mobile)
                    <span>📱 {{ $customer->mobile }}</span>
                @endif
                @if($customer->email)
                    <span>✉️ {{ $customer->email }}</span>
                @endif
                @if($customer->gst_number)
                    <span class="font-mono text-slate-600 dark:text-slate-300">GST: {{ $customer->gst_number }}</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs shrink-0">
            <div class="text-right">
                <span class="block text-[10px] text-slate-500 uppercase font-bold">Total Sales</span>
                <span class="text-base font-black text-emerald-600 dark:text-emerald-400">₹{{ number_format($totalRevenue, 2) }}</span>
            </div>
            <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
            <div class="text-right">
                <span class="block text-[10px] text-slate-500 uppercase font-bold">Completed Bills</span>
                <span class="text-base font-black text-indigo-600 dark:text-indigo-400">{{ $completedCount }}</span>
            </div>
        </div>
    </div>

    <!-- Customer Invoices List Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto max-h-[60vh]">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-xs">
                <thead class="bg-slate-100 dark:bg-slate-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Invoice No</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Date</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Branch / Counter</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-left">Payment Mode</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Tax</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Discount</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-right">Grand Total</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Status</th>
                        <th class="px-3.5 py-3 font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                            <td class="px-3.5 py-3 font-extrabold text-indigo-600 dark:text-indigo-400">
                                <a href="{{ route('sales.show', $sale->id) }}" target="_blank" class="hover:underline">
                                    #{{ $sale->invoice_no_display }}
                                </a>
                            </td>
                            <td class="px-3.5 py-3 text-slate-600 dark:text-slate-300 font-medium whitespace-nowrap">
                                {{ $sale->invoice_date ? $sale->invoice_date->format('d M Y') : '—' }}
                            </td>
                            <td class="px-3.5 py-3 text-slate-600 dark:text-slate-400">
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $sale->branch->name ?? '—' }}</span>
                                @if($sale->counter)
                                    <span class="text-slate-400 text-[10px]"> ({{ $sale->counter->counter_name }})</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3">
                                @if($sale->payments && $sale->payments->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($sale->payments as $payment)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-800">
                                                {{ $payment->paymentMode->mode_name ?? 'Cash' }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 text-right text-purple-700 dark:text-purple-300 font-semibold">
                                ₹{{ number_format($sale->tax_amount, 2) }}
                            </td>
                            <td class="px-3.5 py-3 text-right text-amber-600 dark:text-amber-400 font-semibold">
                                ₹{{ number_format(($sale->item_discount ?? 0) + ($sale->invoice_discount ?? 0), 2) }}
                            </td>
                            <td class="px-3.5 py-3 text-right text-slate-900 dark:text-white font-black text-sm">
                                ₹{{ number_format($sale->grand_total, 2) }}
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                @if($sale->status === \App\Models\Sale::STATUS_COMPLETED)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('sales.show', $sale->id) }}" target="_blank" class="p-1 rounded text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400" title="View Details">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="p-1 rounded text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400" title="Print Invoice">
                                        <x-heroicon-o-printer class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-400">
                                No sales transactions found for this customer.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
