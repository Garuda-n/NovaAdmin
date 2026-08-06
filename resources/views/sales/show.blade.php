<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#182035] p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Sales Invoice #{{ $sale->invoice_no_display }}
                        </h1>
                        @if ($sale->isCompleted())
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 text-xs font-semibold rounded-full border border-emerald-300 dark:border-emerald-700">
                                Completed
                            </span>
                        @else
                            <span class="px-3 py-1 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-300 text-xs font-semibold rounded-full border border-rose-300 dark:border-rose-700">
                                Cancelled
                            </span>
                        @endif
                        @if ($sale->isCashSale())
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 text-xs font-semibold rounded-full border border-blue-300 dark:border-blue-700">
                                Cash Sale
                            </span>
                        @else
                            <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 text-xs font-semibold rounded-full border border-amber-300 dark:border-amber-700">
                                Credit Sale
                            </span>
                        @endif
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Invoice Date: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $sale->invoice_date ? $sale->invoice_date->format('d M Y') : '-' }}</span>
                        @if($sale->isCreditSale() && $sale->due_date)
                            • Due Date: <span class="font-medium text-amber-600 dark:text-amber-400">{{ $sale->due_date->format('d M Y') }}</span>
                        @endif
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg hover:bg-slate-300 transition">
                        ← Back to List
                    </a>

                    @can('sales.print')
                    <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Invoice
                    </a>
                    @endcan

                    @if ($sale->isCompleted())
                        @can('sales.cancel')
                        <button type="button" onclick="document.getElementById('cancel_modal').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg shadow transition">
                            Cancel Invoice
                        </button>
                        @endcan
                    @endif
                </div>
            </div>

            <!-- Billing Snapshot Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Customer Snapshot -->
                <div class="bg-white dark:bg-[#182035] rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Customer Snapshot</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $sale->salesInvoiceSnapshot->customer_name ?? $sale->customer->customer_name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Mobile: {{ $sale->salesInvoiceSnapshot->customer_mobile ?? $sale->customer->mobile }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">GST: {{ $sale->salesInvoiceSnapshot->customer_gst_number ?? 'Unregistered' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Address: {{ $sale->salesInvoiceSnapshot->customer_address ?? 'N/A' }}</p>
                </div>

                <!-- Company (Seller) Snapshot -->
                <div class="bg-white dark:bg-[#182035] rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Seller Snapshot</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $sale->salesInvoiceSnapshot->company_name ?? $sale->company->company_name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">GST: {{ $sale->salesInvoiceSnapshot->company_gst_number ?? 'N/A' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Address: {{ $sale->salesInvoiceSnapshot->company_address ?? 'N/A' }}</p>
                </div>

                <!-- Branch Snapshot -->
                <div class="bg-white dark:bg-[#182035] rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Branch Snapshot</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $sale->salesInvoiceSnapshot->branch_name ?? $sale->branch->branch_name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Counter: {{ $sale->counter->counter_name ?? 'POS' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Branch GST: {{ $sale->salesInvoiceSnapshot->branch_gst_number ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Line Items Table Card -->
            <div class="bg-white dark:bg-[#182035] rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Invoice Line Items</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-[#0f1422] text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-3">#</th>
                                <th class="px-6 py-3">Product Name & Code</th>
                                <th class="px-6 py-3 text-right">Qty</th>
                                <th class="px-6 py-3 text-right">Rate (₹)</th>
                                <th class="px-6 py-3 text-right">Tax (%)</th>
                                <th class="px-6 py-3 text-right">Tax Amount (₹)</th>
                                <th class="px-6 py-3 text-right">Line Total (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach ($sale->details as $index => $detail)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                                    <td class="px-6 py-4 text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $detail->product_name }}
                                        <span class="block text-xs text-slate-400 font-mono">{{ $detail->product_code }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium">{{ number_format($detail->quantity, 2) }}</td>
                                    <td class="px-6 py-4 text-right">₹{{ number_format($detail->rate, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-indigo-600 dark:text-indigo-400 font-medium">{{ number_format($detail->tax_percentage, 2) }}%</td>
                                    <td class="px-6 py-4 text-right">₹{{ number_format($detail->tax_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-extrabold text-slate-900 dark:text-white">₹{{ number_format($detail->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 dark:bg-[#0f1422] border-t border-slate-200 dark:border-slate-800 flex justify-end">
                    <div class="w-80 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Subtotal:</span>
                            <span class="font-medium text-slate-900 dark:text-white">₹{{ number_format($sale->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Total Tax Amount:</span>
                            <span class="font-medium text-slate-900 dark:text-white">₹{{ number_format($sale->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Discount / Round Off:</span>
                            <span class="font-medium text-slate-900 dark:text-white">₹{{ number_format($sale->invoice_discount + $sale->round_off, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white pt-2 border-t border-slate-300 dark:border-slate-700">
                            <span>Grand Total:</span>
                            <span class="text-indigo-600 dark:text-indigo-400">₹{{ number_format($sale->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History / Breakdown Card -->
            @if ($sale->salesPayments->count() > 0)
                <div class="bg-white dark:bg-[#182035] rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Payment Breakdown</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-[#0f1422] text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-3">#</th>
                                    <th class="px-6 py-3">Payment Mode</th>
                                    <th class="px-6 py-3">Reference / Txn No</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3 text-right">Amount Paid (₹)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @foreach ($sale->salesPayments as $idx => $payment)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                                        <td class="px-6 py-4 text-slate-400">{{ $idx + 1 }}</td>
                                        <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                            {{ $payment->paymentMode->mode_name ?? 'N/A' }}
                                            <span class="text-xs text-slate-400 font-normal">({{ $payment->paymentMode->mode_code ?? '' }})</span>
                                        </td>
                                        <td class="px-6 py-4 font-mono text-slate-600 dark:text-slate-400">{{ $payment->reference_no ?: '-' }}</td>
                                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</td>
                                        <td class="px-6 py-4 text-right font-extrabold text-emerald-600 dark:text-emerald-400">₹{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Customer Receivable Status -->
            @if ($sale->customerReceivable)
                <div class="bg-white dark:bg-[#182035] rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Customer Receivable Status</h3>
                        @if(!$sale->customerReceivable->isPaid() && $sale->isCompleted())
                            @can('receivable.allocate')
                                <button type="button" onclick="document.getElementById('collect_payment_modal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                                    Collect Payment
                                </button>
                            @endcan
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-slate-50 dark:bg-[#0f1422] rounded-lg">
                            <span class="text-xs text-slate-400 uppercase font-semibold">Original Invoice Amount</span>
                            <p class="text-lg font-bold text-slate-900 dark:text-white">₹{{ number_format($sale->customerReceivable->original_amount, 2) }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-[#0f1422] rounded-lg">
                            <span class="text-xs text-slate-400 uppercase font-semibold">Paid Amount</span>
                            <p class="text-lg font-bold text-emerald-600">₹{{ number_format($sale->customerReceivable->paid_amount, 2) }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-[#0f1422] rounded-lg">
                            <span class="text-xs text-slate-400 uppercase font-semibold">Balance Amount</span>
                            <p class="text-lg font-bold text-rose-600">₹{{ number_format($sale->customerReceivable->balance_amount, 2) }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-[#0f1422] rounded-lg">
                            <span class="text-xs text-slate-400 uppercase font-semibold">Receivable Status</span>
                            <p class="text-lg font-bold text-indigo-600">
                                @if($sale->customerReceivable->isPaid()) Paid @elseif($sale->customerReceivable->isPartiallyPaid()) Partially Paid @else Pending @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cancellation Modal -->
            <div id="cancel_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden">
                <div class="bg-white dark:bg-[#182035] rounded-xl p-6 max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Cancel Sales Invoice</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Are you sure you want to cancel invoice #{{ $sale->invoice_no_display }}? This will rollback payments and receivables.</p>

                    <form action="{{ route('sales.cancel', $sale->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="cancel_reason" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reason for Cancellation</label>
                            <input type="text" name="cancel_reason" id="cancel_reason" required placeholder="Reason for invoice cancellation" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm">
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="document.getElementById('cancel_modal').classList.add('hidden')" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg">
                                Close
                            </button>
                            <button type="submit" class="px-4 py-2 bg-rose-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-rose-700">
                                Confirm Cancellation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Collect Payment Modal -->
            @if($sale->customerReceivable && !$sale->customerReceivable->isPaid() && $sale->isCompleted())
            <div id="collect_payment_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden">
                <div class="bg-white dark:bg-[#182035] rounded-xl p-6 max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Collect Outstanding Payment</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Record a customer payment to settle the outstanding balance for invoice #{{ $sale->invoice_no_display }}.</p>

                    <form action="{{ route('sales.collect-payment', $sale->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="payment_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm">
                        </div>

                        <div>
                            <label for="payment_mode_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Payment Mode</label>
                            <select name="payment_mode_id" id="payment_mode_id" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm">
                                <option value="">Select Mode</option>
                                @foreach(\App\Models\PaymentMode::where('status', \App\Models\PaymentMode::STATUS_ACTIVE)->orderBy('display_order', 'asc')->get() as $mode)
                                    <option value="{{ $mode->id }}">{{ $mode->mode_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Amount (₹)</label>
                            <input type="number" name="amount" id="amount" required step="0.01" min="0.01" max="{{ $sale->customerReceivable->balance_amount }}" value="{{ $sale->customerReceivable->balance_amount }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm">
                        </div>

                        <div>
                            <label for="reference_no" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reference / Txn ID</label>
                            <input type="text" name="reference_no" id="reference_no" placeholder="Reference or Transaction ID" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm">
                        </div>

                        <div>
                            <label for="remarks" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="2" placeholder="Optional comments" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="document.getElementById('collect_payment_modal').classList.add('hidden')" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg">
                                Close
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-indigo-700">
                                Settle Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @if($sale->customerReceivable && !$sale->customerReceivable->isPaid() && $sale->isCompleted())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('collect') === '1') {
                        const modal = document.getElementById('collect_payment_modal');
                        if (modal) {
                            modal.classList.remove('hidden');
                        }
                    }
                });
            </script>
            @endif

        </div>
    </div>
</x-app-layout>
