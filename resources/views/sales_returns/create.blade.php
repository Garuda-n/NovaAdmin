<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Page Header -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Create Sales Return
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Select a customer, retrieve their completed invoices, and choose items to return.
                    </p>
                </div>

                <a href="{{ route('sales-returns.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg transition">
                    ← Back to List
                </a>
            </div>

            <!-- Validation Error Alert -->
            @if ($errors->any())
                <div class="p-4 bg-rose-50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-400 rounded-xl border border-rose-200 dark:border-rose-800/50 text-sm space-y-1">
                    <p class="font-semibold text-rose-900 dark:text-rose-300">Please correct the following errors:</p>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Form -->
            <form action="{{ route('sales-returns.store') }}" method="POST" id="sales_return_form" class="space-y-6">
                @csrf

                <!-- Step 1: Customer & Invoice Selection -->
                <div class="bg-white dark:bg-[#182035] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6 space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center">1</span>
                        Customer & Invoice Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Return Date -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Return Date <span class="text-red-500">*</span></label>
                            <input type="date" name="return_date" id="return_date" value="{{ old('return_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <!-- Customer Dropdown -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Select Customer <span class="text-red-500">*</span></label>
                            <select id="customer_id" name="customer_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">-- Choose Customer --</option>
                                @foreach (\App\Models\Customer::active()->orderBy('customer_name')->get() as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->customer_name }} ({{ $c->mobile }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Invoice Dropdown -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Select Sales Invoice <span class="text-red-500">*</span></label>
                            <select id="sales_id" name="sales_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500" disabled required>
                                <option value="">-- Select Customer First --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Line Items to Return -->
                <div id="invoice_items_card" class="bg-white dark:bg-[#182035] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6 space-y-4 hidden">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center">2</span>
                        Select Items to Return
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300" id="items_table">
                            <thead class="bg-slate-50 dark:bg-[#0f1422] text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-4 py-3 text-center w-12">Select</th>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3 text-right">Sold Qty</th>
                                    <th class="px-4 py-3 text-right">Prev. Returned</th>
                                    <th class="px-4 py-3 text-right">Available Qty</th>
                                    <th class="px-4 py-3 text-right">Rate (₹)</th>
                                    <th class="px-4 py-3 text-right w-36">Return Qty</th>
                                    <th class="px-4 py-3 text-right">Taxable (₹)</th>
                                    <th class="px-4 py-3 text-right">Tax (₹)</th>
                                    <th class="px-4 py-3 text-right">Line Total (₹)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800" id="items_tbody">
                                <!-- Populated dynamically by javascript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Step 3: Refund & Summary Blocks -->
                <div id="refund_summary_blocks" class="grid grid-cols-1 lg:grid-cols-3 gap-6 hidden">
                    <!-- Refund Payout Details -->
                    <div class="lg:col-span-2 bg-white dark:bg-[#182035] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center">3</span>
                            Settlement & Refund Details
                        </h2>

                        <!-- Receivable Offset Banner for Credit Sales -->
                        <div id="receivable_offset_info" class="p-4 bg-amber-50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300 rounded-xl border border-amber-200 dark:border-amber-800/50 text-sm flex items-center gap-3 hidden">
                            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="font-medium">
                                <strong>Receivable Offset:</strong> This return is linked to a Credit Sale. The total refund of <span class="font-bold font-mono text-base" id="receivable_refund_total">₹0.00</span> will be deducted directly from the customer's outstanding invoice balances.
                            </p>
                        </div>

                        <!-- Cash Refund Input Block -->
                        <div id="cash_refund_block" class="grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Payment Mode <span class="text-red-500">*</span></label>
                                <select id="payment_mode_id" name="payment_mode_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select Mode --</option>
                                @foreach (\App\Models\PaymentMode::where('status', 1)->get() as $mode)
                                    <option value="{{ $mode->id }}">{{ $mode->mode_name }}</option>
                                @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Reference No</label>
                                <input type="text" name="reference_no" id="reference_no" placeholder="TXN ID, Receipt Code..." class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Return Remarks / Reason</label>
                                <textarea name="remarks" id="remarks" rows="3" placeholder="Add reasons for this return transaction..." class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Return Summary -->
                    <div class="bg-white dark:bg-[#182035] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                            Return Summary
                        </h2>

                        <div class="space-y-3 font-mono text-sm">
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span>Subtotal</span>
                                <span id="summary_subtotal">₹0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span>Item Discount</span>
                                <span id="summary_item_discount">₹0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span>Invoice Discount (Pro-rata)</span>
                                <span id="summary_invoice_discount">₹0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800/80 pt-2">
                                <span>CGST</span>
                                <span id="summary_cgst">₹0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span>SGST</span>
                                <span id="summary_sgst">₹0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span>IGST</span>
                                <span id="summary_igst">₹0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span>Tax Total</span>
                                <span id="summary_tax">₹0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span>Round Off</span>
                                <span id="summary_round_off">₹0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-900 dark:text-white font-extrabold text-base border-t border-slate-200 dark:border-slate-800 pt-3">
                                <span>Grand Total</span>
                                <span class="text-indigo-600 dark:text-indigo-400" id="summary_grand_total">₹0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end gap-4 bg-white dark:bg-[#182035] p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <a href="{{ route('sales-returns.index') }}" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition">
                        Cancel
                    </a>

                    <button type="submit" id="submit_return_btn" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition cursor-not-allowed opacity-50" disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Complete Return
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const customerSelect = document.getElementById('customer_id');
            const salesSelect = document.getElementById('sales_id');
            const returnDateInput = document.getElementById('return_date');
            
            const invoiceItemsCard = document.getElementById('invoice_items_card');
            const itemsTbody = document.getElementById('items_tbody');
            const refundSummaryBlocks = document.getElementById('refund_summary_blocks');
            
            const receivableOffsetInfo = document.getElementById('receivable_offset_info');
            const receivableRefundTotal = document.getElementById('receivable_refund_total');
            const cashRefundBlock = document.getElementById('cash_refund_block');
            const paymentModeSelect = document.getElementById('payment_mode_id');
            
            const submitBtn = document.getElementById('submit_return_btn');
            const form = document.getElementById('sales_return_form');
            
            let currentInvoiceData = null;

            // Trigger loading customer invoices
            customerSelect.addEventListener('change', function () {
                const customerId = this.value;
                
                // Clear selection states
                salesSelect.innerHTML = '<option value="">-- Choose Sales Invoice --</option>';
                salesSelect.disabled = true;
                invoiceItemsCard.classList.add('hidden');
                refundSummaryBlocks.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                
                if (!customerId) return;

                salesSelect.innerHTML = '<option value="">Loading invoices...</option>';

                fetch(`/sales-returns/get-invoices/${customerId}`)
                    .then(res => res.json())
                    .then(data => {
                        salesSelect.innerHTML = '<option value="">-- Choose Sales Invoice --</option>';
                        if (data.length === 0) {
                            salesSelect.innerHTML = '<option value="">No completed invoices found</option>';
                            return;
                        }
                        
                        data.forEach(invoice => {
                            const date = new Date(invoice.invoice_date).toLocaleDateString('en-GB', {
                                day: '2-digit', month: 'short', year: 'numeric'
                            });
                            salesSelect.innerHTML += `<option value="${invoice.id}">#${invoice.invoice_no_display} - ${date} (₹${parseFloat(invoice.grand_total).toFixed(2)})</option>`;
                        });
                        salesSelect.disabled = false;
                    })
                    .catch(err => {
                        console.error('Error fetching invoices:', err);
                        salesSelect.innerHTML = '<option value="">Failed to load invoices</option>';
                    });
            });

            // Trigger loading invoice detail lines
            salesSelect.addEventListener('change', function () {
                const saleId = this.value;
                
                invoiceItemsCard.classList.add('hidden');
                refundSummaryBlocks.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                itemsTbody.innerHTML = '';
                
                if (!saleId) return;

                fetch(`/sales-returns/get-invoice-details/${saleId}`)
                    .then(res => res.json())
                    .then(data => {
                        currentInvoiceData = data;
                        
                        // Check if all items are fully returned
                        let hasReturnable = false;

                        data.details.forEach((line, index) => {
                            const row = document.createElement('tr');
                            row.className = 'hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition';
                            
                            const isSelectable = line.is_returnable && line.remaining_qty > 0;
                            if (isSelectable) {
                                hasReturnable = true;
                            }

                            const checkboxHtml = isSelectable
                                ? `<input type="checkbox" name="items[${index}][selected]" value="1" data-index="${index}" class="row-checkbox rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500">`
                                : `<span class="text-slate-400 dark:text-slate-600 text-xs">${!line.is_returnable ? 'Non-returnable' : 'Returned'}</span>`;

                            const qtyInputHtml = isSelectable
                                ? `<input type="number" name="items[${index}][returned_quantity]" data-index="${index}" min="0.01" max="${line.remaining_qty}" step="${line.item_type === 1 ? '1' : '0.01'}" value="${line.item_type === 1 ? '1' : line.remaining_qty}" ${line.item_type === 1 ? 'readonly' : ''} class="qty-input w-24 text-right rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50" disabled>`
                                : '-';

                            const hiddenDetailId = isSelectable
                                ? `<input type="hidden" name="items[${index}][sales_detail_id]" value="${line.id}">`
                                : '';

                            row.innerHTML = `
                                <td class="px-4 py-3 text-center">${checkboxHtml}${hiddenDetailId}</td>
                                <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">
                                    ${line.product_name}
                                    <span class="block text-xs text-slate-400 font-mono">${line.product_code}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono">${line.original_qty.toFixed(2)}</td>
                                <td class="px-4 py-3 text-right font-mono text-slate-500">${line.returned_qty.toFixed(2)}</td>
                                <td class="px-4 py-3 text-right font-mono text-indigo-600 dark:text-indigo-400 font-bold">${line.remaining_qty.toFixed(2)}</td>
                                <td class="px-4 py-3 text-right font-mono">₹${line.rate.toFixed(2)}</td>
                                <td class="px-4 py-3 text-right">${qtyInputHtml}</td>
                                <td class="px-4 py-3 text-right font-mono" id="line_taxable_${index}">₹0.00</td>
                                <td class="px-4 py-3 text-right font-mono text-slate-500" id="line_tax_${index}">₹0.00</td>
                                <td class="px-4 py-3 text-right font-mono text-slate-900 dark:text-white font-semibold" id="line_total_${index}">₹0.00</td>
                            `;
                            
                            itemsTbody.appendChild(row);
                        });

                        // Show cards
                        invoiceItemsCard.classList.remove('hidden');
                        refundSummaryBlocks.classList.remove('hidden');

                        // Financial Settlement Setup
                        if (data.sale_type === 2) {
                            // Credit sale
                            receivableOffsetInfo.classList.remove('hidden');
                            cashRefundBlock.classList.add('hidden');
                            paymentModeSelect.removeAttribute('required');
                        } else {
                            // Cash sale
                            receivableOffsetInfo.classList.add('hidden');
                            cashRefundBlock.classList.remove('hidden');
                            paymentModeSelect.setAttribute('required', 'required');
                        }

                        // Attach event listeners
                        attachTableEvents();
                        recalculateTotals();
                    })
                    .catch(err => {
                        console.error('Error fetching details:', err);
                        alert('Failed to load invoice items details.');
                    });
            });

            function attachTableEvents() {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                const qtyInputs = document.querySelectorAll('.qty-input');

                checkboxes.forEach(chk => {
                    chk.addEventListener('change', function () {
                        const index = this.dataset.index;
                        const qtyInput = document.querySelector(`.qty-input[data-index="${index}"]`);
                        
                        if (qtyInput) {
                            qtyInput.disabled = !this.checked;
                        }
                        
                        recalculateTotals();
                    });
                });

                qtyInputs.forEach(input => {
                    input.addEventListener('input', function () {
                        const index = this.dataset.index;
                        const line = currentInvoiceData.details[index];
                        const val = parseFloat(this.value) || 0;

                        // Real-time quantity validation
                        if (val > line.remaining_qty) {
                            this.value = line.remaining_qty;
                            alert(`Returned quantity cannot exceed maximum returnable amount of ${line.remaining_qty}`);
                        } else if (val < 0) {
                            this.value = 0;
                        }

                        recalculateTotals();
                    });
                });
            }

            function recalculateTotals() {
                if (!currentInvoiceData) return;

                let subtotal = 0.00;
                let itemDiscount = 0.00;
                let invoiceDiscount = 0.00;
                let cgst = 0.00;
                let sgst = 0.00;
                let igst = 0.00;
                let taxTotal = 0.00;

                let anyChecked = false;

                const checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(chk => {
                    if (chk.checked) {
                        anyChecked = true;
                        const index = parseInt(chk.dataset.index);
                        const line = currentInvoiceData.details[index];
                        const qtyInput = document.querySelector(`.qty-input[data-index="${index}"]`);
                        const returnedQty = parseFloat(qtyInput.value) || 0;

                        if (returnedQty <= 0) return;

                        // Calculations mapping exact backend Formulas
                        const lineSubtotal = returnedQty * line.rate;

                        // Pro-rata line discount
                        let lineItemDiscount = 0.00;
                        if (line.original_qty > 0) {
                            lineItemDiscount = line.discount_amount * (returnedQty / line.original_qty);
                        }

                        // Pro-rata global invoice discount
                        let lineInvoiceDiscount = 0.00;
                        if (currentInvoiceData.subtotal > 0) {
                            lineInvoiceDiscount = (lineSubtotal / currentInvoiceData.subtotal) * currentInvoiceData.invoice_discount;
                        }

                        const taxableValue = lineSubtotal - lineItemDiscount - lineInvoiceDiscount;

                        // Recompute taxes
                        let lineCgst = 0.00;
                        let lineSgst = 0.00;
                        let lineIgst = 0.00;

                        if (currentInvoiceData.gst_type === 1) {
                            lineCgst = taxableValue * (line.cgst_percentage / 100);
                            lineSgst = taxableValue * (line.sgst_percentage / 100);
                        } else if (currentInvoiceData.gst_type === 2) {
                            lineIgst = taxableValue * (line.igst_percentage / 100);
                        }

                        const lineTax = lineCgst + lineSgst + lineIgst;
                        const lineTotal = taxableValue + lineTax;

                        // Display line items calculations
                        document.getElementById(`line_taxable_${index}`).innerText = '₹' + taxableValue.toFixed(2);
                        document.getElementById(`line_tax_${index}`).innerText = '₹' + lineTax.toFixed(2);
                        document.getElementById(`line_total_${index}`).innerText = '₹' + lineTotal.toFixed(2);

                        // Accumulate header totals
                        subtotal += lineSubtotal;
                        itemDiscount += lineItemDiscount;
                        invoiceDiscount += lineInvoiceDiscount;
                        cgst += lineCgst;
                        sgst += lineSgst;
                        igst += lineIgst;
                        taxTotal += lineTax;
                    } else {
                        const index = chk.dataset.index;
                        document.getElementById(`line_taxable_${index}`).innerText = '₹0.00';
                        document.getElementById(`line_tax_${index}`).innerText = '₹0.00';
                        document.getElementById(`line_total_${index}`).innerText = '₹0.00';
                    }
                });

                // Apply rounding calculation matching backend
                const exactGrandTotal = subtotal - itemDiscount - invoiceDiscount + taxTotal;
                const roundedGrandTotal = Math.round(exactGrandTotal);
                const roundOff = roundedGrandTotal - exactGrandTotal;

                // Update summary block
                document.getElementById('summary_subtotal').innerText = '₹' + subtotal.toFixed(2);
                document.getElementById('summary_item_discount').innerText = '₹' + itemDiscount.toFixed(2);
                document.getElementById('summary_invoice_discount').innerText = '₹' + invoiceDiscount.toFixed(2);
                document.getElementById('summary_cgst').innerText = '₹' + cgst.toFixed(2);
                document.getElementById('summary_sgst').innerText = '₹' + sgst.toFixed(2);
                document.getElementById('summary_igst').innerText = '₹' + igst.toFixed(2);
                document.getElementById('summary_tax').innerText = '₹' + taxTotal.toFixed(2);
                document.getElementById('summary_round_off').innerText = '₹' + roundOff.toFixed(2);
                document.getElementById('summary_grand_total').innerText = '₹' + roundedGrandTotal.toFixed(2);

                if (currentInvoiceData && currentInvoiceData.sale_type === 2) {
                    receivableRefundTotal.innerText = '₹' + roundedGrandTotal.toFixed(2);
                }

                // Enable/disable submit button
                if (anyChecked) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            // Prevent duplicate form submissions
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mr-3 text-white inline" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
            });
        });
    </script>
    @endpush
</x-app-layout>
