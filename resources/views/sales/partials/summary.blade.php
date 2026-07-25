<!-- Invoice Financial Summary Partial -->
<div class="bg-white dark:bg-[#182035] rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-3">
        Invoice Financial Summary
    </h2>

    <div class="space-y-3">
        <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
            <span>Subtotal</span>
            <span class="font-medium text-slate-900 dark:text-white" id="summary_subtotal">₹{{ number_format($totals['subtotal'], 2) }}</span>
        </div>

        <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
            <span>Item Discount</span>
            <span class="font-medium text-amber-600 dark:text-amber-400" id="summary_item_discount">-₹{{ number_format($totals['item_discount'], 2) }}</span>
        </div>

        <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
            <span>Invoice Discount</span>
            <div class="w-32">
                <input type="number" step="0.01" name="invoice_discount" id="invoice_discount" value="{{ old('invoice_discount', $totals['invoice_discount'] ?? 0) }}" class="w-full py-1 text-right text-sm rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white">
            </div>
        </div>

        <div class="border-t border-slate-200 dark:border-slate-800 pt-3 space-y-2" id="gst_breakup_container">
            @if(($gstType ?? 1) == 1)
                <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
                    <span>CGST Amount</span>
                    <span class="font-medium text-slate-900 dark:text-white" id="summary_cgst">₹{{ number_format($totals['cgst_amount'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
                    <span>SGST Amount</span>
                    <span class="font-medium text-slate-900 dark:text-white" id="summary_sgst">₹{{ number_format($totals['sgst_amount'], 2) }}</span>
                </div>
            @else
                <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
                    <span>IGST Amount</span>
                    <span class="font-medium text-slate-900 dark:text-white" id="summary_igst">₹{{ number_format($totals['igst_amount'], 2) }}</span>
                </div>
            @endif
            <div class="flex items-center justify-between text-sm font-semibold text-slate-900 dark:text-white">
                <span>Total Tax Amount</span>
                <span id="summary_tax_amount">₹{{ number_format($totals['tax_amount'], 2) }}</span>
            </div>
        </div>

        <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
            <span>Round Off</span>
            <div class="w-28">
                <input type="number" step="0.01" name="round_off" id="round_off" value="{{ old('round_off', $totals['round_off'] ?? 0) }}" class="w-full py-1 text-right text-sm rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white">
            </div>
        </div>

        <div class="border-t-2 border-slate-900 dark:border-slate-700 pt-4 flex items-center justify-between">
            <span class="text-base font-bold text-slate-900 dark:text-white">Grand Total</span>
            <span class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400" id="summary_grand_total">₹{{ number_format($totals['grand_total'], 2) }}</span>
        </div>
    </div>
</div>
