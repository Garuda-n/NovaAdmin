<!-- Payment Details & Sale Type Partial -->
<div class="bg-white dark:bg-[#182035] rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Sale Type & Payment Section
        </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Sale Type Selection -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sale Type</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center p-3 rounded-lg border border-slate-300 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                    <input type="radio" name="sale_type" value="1" id="sale_type_cash" class="text-indigo-600 focus:ring-indigo-500" {{ old('sale_type', '1') == '1' ? 'checked' : '' }}>
                    <span class="ml-2 text-sm font-medium text-slate-900 dark:text-white">Cash Sale (Instant Payment)</span>
                </label>

                <label class="flex items-center p-3 rounded-lg border border-slate-300 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                    <input type="radio" name="sale_type" value="2" id="sale_type_credit" class="text-indigo-600 focus:ring-indigo-500" {{ old('sale_type') == '2' ? 'checked' : '' }}>
                    <span class="ml-2 text-sm font-medium text-slate-900 dark:text-white">Credit Sale (Receivable)</span>
                </label>
            </div>
        </div>

        <!-- Due Date (Shown for Credit Sale) -->
        <div id="due_date_container" class="{{ old('sale_type') == '2' ? '' : 'hidden' }}">
            <label for="due_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Receivable Due Date</label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Payment due date for customer receivable tracking.</p>
        </div>
    </div>

    <!-- Cash Payment Options -->
    <div id="cash_payment_container" class="pt-4 border-t border-slate-200 dark:border-slate-800 space-y-4 {{ old('sale_type', '1') == '1' ? '' : 'hidden' }}">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Payment Breakdown</h3>
            <button type="button" id="add_payment_row_btn" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Payment Mode
            </button>
        </div>

        @php
            $oldPayments = old('payments');
            if (!$oldPayments) {
                $oldPayments = [
                    [
                        'payment_mode_id' => old('payment_mode_id', $paymentModes->firstWhere('is_default', true)->id ?? $paymentModes->first()->id ?? ''),
                        'amount' => old('paid_amount', $totals['grand_total']),
                        'reference_no' => old('reference_no', '')
                    ]
                ];
            }
        @endphp

        <div id="payment_rows_container" class="space-y-3">
            @foreach ($oldPayments as $index => $p)
                <div class="payment-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Payment Mode</label>
                        <select name="payments[{{ $index }}][payment_mode_id]" class="payment-mode-select w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($paymentModes as $mode)
                                <option value="{{ $mode->id }}" {{ ($p['payment_mode_id'] ?? '') == $mode->id || (empty($p['payment_mode_id']) && $mode->is_default) ? 'selected' : '' }}>
                                    {{ $mode->mode_name }} ({{ $mode->mode_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Paid Amount (₹)</label>
                        <input type="number" step="0.01" min="0" name="payments[{{ $index }}][amount]" value="{{ $p['amount'] ?? '0.00' }}" class="payment-amount-input w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Reference / Txn No</label>
                        <input type="text" name="payments[{{ $index }}][reference_no]" placeholder="UPI ID / Card Ref / Cheque No" value="{{ $p['reference_no'] ?? '' }}" class="payment-ref-input w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="md:col-span-1 flex justify-end">
                        <button type="button" class="remove-payment-row-btn p-2 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Remove Payment Mode">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between text-xs font-medium px-1 pt-1 text-slate-600 dark:text-slate-400">
            <div>
                Total Paid: <span id="payment_total_display" class="font-bold text-slate-900 dark:text-white">₹0.00</span>
            </div>
            <div>
                Remaining Balance: <span id="payment_balance_display" class="font-bold text-emerald-600 dark:text-emerald-400">₹0.00</span>
            </div>
        </div>
    </div>

    <template id="payment_row_template">
        <div class="payment-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
            <div class="md:col-span-4">
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Payment Mode</label>
                <select name="payments[__INDEX__][payment_mode_id]" class="payment-mode-select w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach ($paymentModes as $mode)
                        <option value="{{ $mode->id }}" {{ $mode->is_default ? 'selected' : '' }}>
                            {{ $mode->mode_name }} ({{ $mode->mode_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4">
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Paid Amount (₹)</label>
                <input type="number" step="0.01" min="0" name="payments[__INDEX__][amount]" value="0.00" class="payment-amount-input w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Reference / Txn No</label>
                <input type="text" name="payments[__INDEX__][reference_no]" placeholder="UPI ID / Card Ref / Cheque No" class="payment-ref-input w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="md:col-span-1 flex justify-end">
                <button type="button" class="remove-payment-row-btn p-2 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Remove Payment Mode">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>

    <div class="pt-2">
        <label for="remarks" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Remarks / Internal Notes</label>
        <textarea name="remarks" id="remarks" rows="2" placeholder="Optional sales invoice notes..." class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('remarks') }}</textarea>
    </div>
</div>
