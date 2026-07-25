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
    <div id="cash_payment_container" class="pt-4 border-t border-slate-200 dark:border-slate-800 grid grid-cols-1 md:grid-cols-3 gap-4 {{ old('sale_type', '1') == '1' ? '' : 'hidden' }}">
        <div>
            <label for="payment_mode_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Payment Mode</label>
            <select name="payment_mode_id" id="payment_mode_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                @foreach ($paymentModes as $mode)
                    <option value="{{ $mode->id }}" {{ old('payment_mode_id') == $mode->id || $mode->is_default ? 'selected' : '' }}>
                        {{ $mode->mode_name }} ({{ $mode->mode_code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="paid_amount" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Paid Amount (₹)</label>
            <input type="number" step="0.01" name="paid_amount" id="paid_amount" value="{{ old('paid_amount', $totals['grand_total']) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="reference_no" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reference / Txn No</label>
            <input type="text" name="reference_no" id="reference_no" placeholder="UPI ID / Card Ref / Cheque No" value="{{ old('reference_no') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>

    <div class="pt-2">
        <label for="remarks" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Remarks / Internal Notes</label>
        <textarea name="remarks" id="remarks" rows="2" placeholder="Optional sales invoice notes..." class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('remarks') }}</textarea>
    </div>
</div>
