<!-- Customer & Quotation Context Partial -->
<div class="bg-white dark:bg-[#182035] rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 4 0 11-8 0 4 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Quotation & Customer Details
        </h2>
        <span class="inline-flex items-center px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-semibold rounded-full border border-indigo-200 dark:border-indigo-800">
            Quotation #{{ $quotation->quotation_no }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Customer Name</label>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $customer->customer_name }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $customer->mobile }} {{ $customer->email ? '• '.$customer->email : '' }}</p>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Customer GST / Type</label>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">
                {{ $customer->gst_number ?? $customer->gstin ?? 'Unregistered / B2C' }}
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Type: {{ $customer->customer_type ?? 'B2C' }}</p>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Branch & Counter</label>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $branch->branch_name ?? 'Branch' }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Counter: {{ $counter->counter_name ?? 'POS Counter' }}</p>
        </div>
    </div>

    <div class="pt-4 border-t border-slate-200 dark:border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="gst_type" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">GST Tax Type</label>
            <select name="gst_type" id="gst_type" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="1" {{ old('gst_type', $gstType ?? 1) == 1 ? 'selected' : '' }}>1 - Intra-State (CGST + SGST)</option>
                <option value="2" {{ old('gst_type', $gstType ?? 1) == 2 ? 'selected' : '' }}>2 - Inter-State (IGST)</option>
            </select>
        </div>

        <div>
            <label for="invoice_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Invoice Date</label>
            <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>
</div>
