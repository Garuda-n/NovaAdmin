@php
    $isEdit = isset($quotation);
    $isConverted = $isEdit && $quotation->status == 2;

    $details = [];
    if ($isEdit && $quotation->details && $quotation->details->count() > 0) {
        $details = $quotation->details;
    }
@endphp

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/quotations/quotation_form.js') }}"></script>

<div id="quotation-form-container" data-is-converted="{{ $isConverted ? 'true' : 'false' }}" class="space-y-6">

    @if($isConverted)
        <div class="p-4 bg-blue-50 border border-blue-200 dark:bg-blue-950/50 dark:border-blue-800 rounded-xl flex items-center gap-3">
            <span class="inline-flex p-2 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-700 dark:text-blue-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
            <div>
                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200">Converted Quotation (Read Only)</h4>
                <p class="text-xs text-blue-700 dark:text-blue-300">This quotation has been converted. All form controls are read-only and editing is disabled.</p>
            </div>
        </div>
    @endif

    <!-- Card 1: Header Section -->
    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 border border-slate-200 dark:border-slate-700">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Quotation Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Branch (Read Only) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                    Branch
                </label>
                <input
                    type="text"
                    value="{{ $branch->name ?? 'Main Branch' }} {{ isset($branch->branch_code) ? '('.$branch->branch_code.')' : '' }}"
                    class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-600 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-400 text-sm cursor-not-allowed"
                    readonly>
                <input type="hidden" name="branch_id" value="{{ $branch->id ?? '' }}">
            </div>

            <!-- Counter (Read Only) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                    Counter
                </label>
                <input
                    type="text"
                    value="{{ $counter->counter_name ?? 'Main Counter' }} {{ isset($counter->counter_code) ? '('.$counter->counter_code.')' : '' }}"
                    class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-600 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-400 text-sm cursor-not-allowed"
                    readonly>
                <input type="hidden" name="counter_id" value="{{ $counter->id ?? '' }}">
            </div>

            <!-- Business Date (Read Only) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                    Business Date
                </label>
                <input
                    type="text"
                    value="{{ $businessDate }}"
                    class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-600 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-400 text-sm cursor-not-allowed"
                    readonly>
                <input type="hidden" name="business_date" value="{{ $businessDate }}">
            </div>

            <!-- Quotation Number (Display / Read Only) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                    Quotation Number
                </label>
                <input
                    type="text"
                    value="{{ $isEdit && $quotation->quotation_no ? '#'.$quotation->quotation_no : 'Auto Generated After Save' }}"
                    class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-600 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-400 text-sm cursor-not-allowed font-medium"
                    readonly>
            </div>

            <!-- Customer (Searchable Selection, Mandatory) -->
            <div class="lg:col-span-2 relative customer-search-wrapper">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                    Customer <span class="text-red-500">*</span>
                </label>

                <input type="hidden" name="customer_id" id="selected-customer-id" value="{{ old('customer_id', $quotation->customer_id ?? '') }}" required>

                @php
                    $selectedCustomer = null;
                    $oldCustId = old('customer_id');
                    if ($oldCustId) {
                        $selectedCustomer = $customers->firstWhere('id', $oldCustId);
                    } elseif (isset($quotation) && $quotation->customer) {
                        $selectedCustomer = $quotation->customer;
                    }
                    $initialDisplay = $selectedCustomer ? strtoupper($selectedCustomer->customer_name) . ($selectedCustomer->mobile ? '-' . $selectedCustomer->mobile : '') : '';
                @endphp

                <div class="flex items-center gap-1.5">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            id="customer-search-input"
                            class="w-full rounded-lg border-gray-300 bg-white text-slate-900 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500 pr-8"
                            placeholder="Type customer name or mobile..."
                            autocomplete="off"
                            value="{{ $initialDisplay }}"
                            {{ $isConverted ? 'disabled' : '' }}>

                        <button type="button" id="clear-customer-btn" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 {{ $selectedCustomer ? '' : 'hidden' }}" {{ $isConverted ? 'disabled' : '' }}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    @if(!$isConverted)
                        <!-- Quick Add Customer Button (+) -->
                        @can('customers.create')
                        <a href="{{ route('customers.create') }}" target="_blank" title="Add New Customer" class="px-2.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition shrink-0 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </a>
                        @endcan

                        <!-- View / Edit Customers Button -->
                        @can('customers.view')
                        <a href="{{ route('customers.index') }}" target="_blank" title="Manage Customers" class="px-2.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg transition shrink-0 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                        @endcan
                    @endif
                </div>

                <!-- Search Results Dropdown List -->
                <div id="customer-results-list" class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg shadow-xl max-h-60 overflow-y-auto hidden">
                    @foreach($customers as $cust)
                        @php
                            $displayText = strtoupper($cust->customer_name) . ($cust->mobile ? '-' . $cust->mobile : '');
                            $searchContent = strtolower($cust->customer_name . ' ' . ($cust->mobile ?? '') . ' ' . ($cust->customer_code ?? '') . ' ' . ($cust->gst_number ?? ''));
                        @endphp
                        <div class="customer-option px-3 py-2 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 text-slate-800 dark:text-slate-100 font-semibold text-sm cursor-pointer border-b border-slate-100 dark:border-slate-700/50 last:border-0 transition"
                             data-id="{{ $cust->id }}"
                             data-display="{{ $displayText }}"
                             data-name="{{ $cust->customer_name }}"
                             data-mobile="{{ $cust->mobile ?? '' }}"
                             data-search="{{ $searchContent }}"
                             data-type="{{ $cust->customer_type }}">
                            {{ $displayText }}
                        </div>
                    @endforeach
                    <div id="no-customer-found" class="p-3 text-center text-xs text-slate-500 dark:text-slate-400 hidden">
                        No matching customers found.
                    </div>
                </div>

                @error('customer_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Customer Type (Radio Buttons, Mandatory) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-2">
                    Customer Type <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-6 pt-1">
                    <label class="inline-flex items-center text-sm font-medium text-slate-800 dark:text-slate-200 cursor-pointer">
                        <input type="radio" name="customer_type" value="B2C"
                               {{ old('customer_type', $quotation->customer_type ?? 'B2C') === 'B2C' ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-indigo-500"
                               required
                               {{ $isConverted ? 'disabled' : '' }}>
                        <span class="ml-2">B2C</span>
                    </label>

                    <label class="inline-flex items-center text-sm font-medium text-slate-800 dark:text-slate-200 cursor-pointer">
                        <input type="radio" name="customer_type" value="B2B"
                               {{ old('customer_type', $quotation->customer_type ?? '') === 'B2B' ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-indigo-500"
                               required
                               {{ $isConverted ? 'disabled' : '' }}>
                        <span class="ml-2">B2B</span>
                    </label>
                </div>
                @error('customer_type')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remarks (Textarea) -->
            <div class="lg:col-span-4">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">
                    Remarks
                </label>
                <textarea
                    name="remarks"
                    rows="2"
                    placeholder="Add optional notes or payment terms..."
                    class="w-full rounded-lg border-gray-300 bg-white text-slate-900 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    {{ $isConverted ? 'disabled' : '' }}>{{ old('remarks', $quotation->remarks ?? '') }}</textarea>
                @error('remarks')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <!-- Card 2: Product Section (Spreadsheet Style Table) -->
    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 border border-slate-200 dark:border-slate-700 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                Product Details
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">Spreadsheet Grid</span>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
            <table id="quotation-items-table" class="w-full text-xs text-left">
                <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-3 py-3 w-10 text-center">#</th>
                        <th class="px-3 py-3 w-64">Product <span class="text-red-500">*</span></th>
                        <th class="px-3 py-3 w-28">UOM</th>
                        <th class="px-3 py-3 w-28 text-right">Qty <span class="text-red-500">*</span></th>
                        <th class="px-3 py-3 w-32 text-right">Rate <span class="text-red-500">*</span></th>
                        <th class="px-3 py-3 w-24 text-right">Tax %</th>
                        <th class="px-3 py-3 w-32 text-right">Tax Amount</th>
                        <th class="px-3 py-3 w-36 text-right">Line Total</th>
                        <th class="px-3 py-3 w-16 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="quotation-items-body" class="divide-y divide-slate-200 dark:divide-slate-700">
                    @if(count($details) > 0)
                        @foreach($details as $index => $item)
                            <tr class="quotation-row hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-3 py-2 text-center font-medium text-slate-500 row-number">{{ $index + 1 }}</td>
                                
                                <!-- Product Searchable Select -->
                                <td class="px-3 py-2">
                                    <select
                                        name="items[{{ $index }}][product_id]"
                                        class="product-select w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white text-xs py-1.5 focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                        {{ $isConverted ? 'disabled' : '' }}>
                                        <option value="">— Select Product —</option>
                                        @foreach($products as $prod)
                                            <option
                                                value="{{ $prod->id }}"
                                                data-uom-id="{{ $prod->uom_id ?? '' }}"
                                                data-uom-name="{{ $prod->uom->name ?? '' }}"
                                                data-tax-percent="{{ isset($prod->tax) ? $prod->tax->percentage : 0 }}"
                                                data-name="{{ $prod->name }}"
                                                {{ $item->product_id == $prod->id ? 'selected' : '' }}>
                                                {{ $prod->name }} {{ $prod->code ? '('.$prod->code.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[{{ $index }}][product_name]" class="product-name-input" value="{{ $item->product_name }}">
                                </td>

                                <!-- UOM (Read Only) -->
                                <td class="px-3 py-2">
                                    <input
                                        type="text"
                                        value="{{ $item->uom_name }}"
                                        placeholder="UOM"
                                        class="uom-name-input w-full rounded border-gray-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs py-1.5 cursor-not-allowed"
                                        readonly>
                                    <input type="hidden" name="items[{{ $index }}][uom_id]" class="uom-id-input" value="{{ $item->uom_id }}">
                                    <input type="hidden" name="items[{{ $index }}][uom_name]" class="uom-name-hidden" value="{{ $item->uom_name }}">
                                </td>

                                <!-- Qty (Editable) -->
                                <td class="px-3 py-2">
                                    <input
                                        type="number"
                                        step="0.001"
                                        min="0.001"
                                        name="items[{{ $index }}][qty]"
                                        value="{{ number_format($item->qty, 3, '.', '') }}"
                                        class="qty-input w-full text-right rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white text-xs py-1.5 focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                        {{ $isConverted ? 'disabled' : '' }}>
                                </td>

                                <!-- Rate (Editable) -->
                                <td class="px-3 py-2">
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0.00"
                                        name="items[{{ $index }}][rate]"
                                        value="{{ number_format($item->rate, 2, '.', '') }}"
                                        class="rate-input w-full text-right rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white text-xs py-1.5 focus:ring-indigo-500 focus:border-indigo-500"
                                        required
                                        {{ $isConverted ? 'disabled' : '' }}>
                                </td>

                                <!-- Tax % (Read Only) -->
                                <td class="px-3 py-2">
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="items[{{ $index }}][tax_percent]"
                                        value="{{ number_format($item->tax_percent, 2, '.', '') }}"
                                        class="tax-percent-input w-full text-right rounded border-gray-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs py-1.5 cursor-not-allowed"
                                        readonly>
                                </td>

                                <!-- Tax Amount (Read Only) -->
                                <td class="px-3 py-2">
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="items[{{ $index }}][tax_amount]"
                                        value="{{ number_format($item->tax_amount, 2, '.', '') }}"
                                        class="tax-amount-input w-full text-right rounded border-gray-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs py-1.5 cursor-not-allowed"
                                        readonly>
                                </td>

                                <!-- Line Total (Read Only) -->
                                <td class="px-3 py-2">
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="items[{{ $index }}][line_total]"
                                        value="{{ number_format($item->line_total, 2, '.', '') }}"
                                        class="line-total-input w-full text-right rounded border-gray-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-white font-semibold text-xs py-1.5 cursor-not-allowed"
                                        readonly>
                                </td>

                                <!-- Action (Delete Row) -->
                                <td class="px-3 py-2 text-center">
                                    <button
                                        type="button"
                                        class="btn-remove-row p-1 rounded text-red-500 hover:bg-red-50 dark:hover:bg-red-950/50 disabled:opacity-30 disabled:cursor-not-allowed transition"
                                        {{ $isConverted ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Default 1 Empty Row -->
                        <tr class="quotation-row hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-3 py-2 text-center font-medium text-slate-500 row-number">1</td>
                            
                            <!-- Product Searchable Select -->
                            <td class="px-3 py-2">
                                <select
                                    name="items[0][product_id]"
                                    class="product-select w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white text-xs py-1.5 focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                    {{ $isConverted ? 'disabled' : '' }}>
                                    <option value="">— Select Product —</option>
                                    @foreach($products as $prod)
                                        <option
                                            value="{{ $prod->id }}"
                                            data-uom-id="{{ $prod->uom_id ?? '' }}"
                                            data-uom-name="{{ $prod->uom->name ?? '' }}"
                                            data-tax-percent="{{ isset($prod->tax) ? $prod->tax->percentage : 0 }}"
                                            data-name="{{ $prod->name }}">
                                            {{ $prod->name }} {{ $prod->code ? '('.$prod->code.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="items[0][product_name]" class="product-name-input">
                            </td>

                            <!-- UOM (Read Only) -->
                            <td class="px-3 py-2">
                                <input
                                    type="text"
                                    placeholder="UOM"
                                    class="uom-name-input w-full rounded border-gray-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs py-1.5 cursor-not-allowed"
                                    readonly>
                                <input type="hidden" name="items[0][uom_id]" class="uom-id-input">
                                <input type="hidden" name="items[0][uom_name]" class="uom-name-hidden">
                            </td>

                            <!-- Qty (Editable) -->
                            <td class="px-3 py-2">
                                <input
                                    type="number"
                                    step="0.001"
                                    min="0.001"
                                    name="items[0][qty]"
                                    value="1"
                                    class="qty-input w-full text-right rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white text-xs py-1.5 focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                    {{ $isConverted ? 'disabled' : '' }}>
                            </td>

                            <!-- Rate (Editable) -->
                            <td class="px-3 py-2">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0.00"
                                    name="items[0][rate]"
                                    value="0.00"
                                    class="rate-input w-full text-right rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white text-xs py-1.5 focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                    {{ $isConverted ? 'disabled' : '' }}>
                            </td>

                            <!-- Tax % (Read Only) -->
                            <td class="px-3 py-2">
                                <input
                                    type="number"
                                    step="0.01"
                                    name="items[0][tax_percent]"
                                    value="0.00"
                                    class="tax-percent-input w-full text-right rounded border-gray-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs py-1.5 cursor-not-allowed"
                                    readonly>
                            </td>

                            <!-- Tax Amount (Read Only) -->
                            <td class="px-3 py-2">
                                <input
                                    type="number"
                                    step="0.01"
                                    name="items[0][tax_amount]"
                                    value="0.00"
                                    class="tax-amount-input w-full text-right rounded border-gray-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs py-1.5 cursor-not-allowed"
                                    readonly>
                            </td>

                            <!-- Line Total (Read Only) -->
                            <td class="px-3 py-2">
                                <input
                                    type="number"
                                    step="0.01"
                                    name="items[0][line_total]"
                                    value="0.00"
                                    class="line-total-input w-full text-right rounded border-gray-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-white font-semibold text-xs py-1.5 cursor-not-allowed"
                                    readonly>
                            </td>

                            <!-- Action (Delete Row) -->
                            <td class="px-3 py-2 text-center">
                                <button
                                    type="button"
                                    class="btn-remove-row p-1 rounded text-red-500 hover:bg-red-50 dark:hover:bg-red-950/50 disabled:opacity-30 disabled:cursor-not-allowed transition"
                                    {{ $isConverted ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Add Product Button -->
        @if(!$isConverted)
            <div>
                <button
                    type="button"
                    id="btn-add-product"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 rounded-lg border border-indigo-200 dark:border-indigo-800 transition">
                    + Add Item
                </button>
            </div>
        @endif
    </div>

    <!-- Card 3: Summary Section (Right Aligned) -->
    <div class="flex justify-end">
        <div class="w-full sm:w-80 bg-white dark:bg-slate-800 shadow-sm rounded-xl p-5 border border-slate-200 dark:border-slate-700 space-y-3">
            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 pb-2">
                Summary
            </h4>

            <div class="flex justify-between items-center text-xs text-slate-600 dark:text-slate-400">
                <span>Subtotal</span>
                <span id="summary-subtotal-display" class="font-semibold text-slate-900 dark:text-white">₹ {{ number_format($quotation->subtotal ?? 0, 2) }}</span>
                <input type="hidden" name="subtotal" id="summary-subtotal-input" value="{{ number_format($quotation->subtotal ?? 0, 2, '.', '') }}">
            </div>

            <div class="flex justify-between items-center text-xs text-slate-600 dark:text-slate-400">
                <span>Tax Amount</span>
                <span id="summary-tax-amount-display" class="font-semibold text-slate-900 dark:text-white">₹ {{ number_format($quotation->tax_amount ?? 0, 2) }}</span>
                <input type="hidden" name="tax_amount" id="summary-tax-amount-input" value="{{ number_format($quotation->tax_amount ?? 0, 2, '.', '') }}">
            </div>

            <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center text-sm font-bold text-slate-900 dark:text-white">
                <span>Grand Total</span>
                <span id="summary-grand-total-display" class="text-indigo-600 dark:text-indigo-400 text-base">₹ {{ number_format($quotation->grand_total ?? 0, 2) }}</span>
                <input type="hidden" name="grand_total" id="summary-grand-total-input" value="{{ number_format($quotation->grand_total ?? 0, 2, '.', '') }}">
            </div>
        </div>
    </div>

    <!-- Card 4: Footer Buttons -->
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
        <a href="{{ route('quotations.index') }}"
           class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg transition">
            Cancel
        </a>

        @if(!$isConverted)
            <button
                type="submit"
                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                {{ $isEdit ? 'Update Quotation' : 'Save Quotation' }}
            </button>
        @endif
    </div>

</div>
