/**
 * Sales Form Interactivity. Line/document totals are computed server-side by
 * the shared Calculation Engine (sales.calculate preview endpoint) — this
 * file only reflects the server's numbers in the DOM, it never computes
 * subtotal/tax/grand-total itself.
 */
document.addEventListener('DOMContentLoaded', function () {
    const saleTypeCash = document.getElementById('sale_type_cash');
    const saleTypeCredit = document.getElementById('sale_type_credit');
    const dueDateContainer = document.getElementById('due_date_container');
    const cashPaymentContainer = document.getElementById('cash_payment_container');

    const gstTypeSelect = document.getElementById('gst_type');
    const invoiceDiscountInput = document.getElementById('invoice_discount');
    const roundOffInput = document.getElementById('round_off');
    const paidAmountInput = document.getElementById('paid_amount');

    const conversionForm = document.getElementById('sales_conversion_form');
    const summaryCard = document.getElementById('invoice_summary_card');
    const calculateUrl = conversionForm ? conversionForm.dataset.calculateUrl : null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function toggleSaleType() {
        if (!saleTypeCash || !saleTypeCredit) return;

        if (saleTypeCredit.checked) {
            dueDateContainer?.classList.remove('hidden');
            cashPaymentContainer?.classList.add('hidden');
        } else {
            dueDateContainer?.classList.add('hidden');
            cashPaymentContainer?.classList.remove('hidden');
        }
    }

    if (saleTypeCash && saleTypeCredit) {
        saleTypeCash.addEventListener('change', toggleSaleType);
        saleTypeCredit.addEventListener('change', toggleSaleType);
        toggleSaleType();
    }

    let isUserModifiedRoundOff = false;
    let isUserModifiedPaymentAmounts = false;
    // Seeded from the server-rendered totals so the page doesn't need a
    // network round-trip just to show the numbers it already rendered.
    let lastGrandTotal = parseFloat(summaryCard?.dataset.grandTotal) || 0;
    let calculateDebounceTimer = null;
    let pendingCalculation = null;

    function getGrandTotal() {
        return lastGrandTotal;
    }

    // Multi-Payment Mode Management
    const paymentRowsContainer = document.getElementById('payment_rows_container');
    const addPaymentRowBtn = document.getElementById('add_payment_row_btn');
    const paymentRowTemplate = document.getElementById('payment_row_template');
    const paymentTotalDisplay = document.getElementById('payment_total_display');
    const paymentBalanceDisplay = document.getElementById('payment_balance_display');

    function updatePaymentTotals() {
        if (!paymentRowsContainer) return;

        const rows = paymentRowsContainer.querySelectorAll('.payment-row');
        let totalPaid = 0;

        rows.forEach(row => {
            const amountInput = row.querySelector('.payment-amount-input');
            const amt = parseFloat(amountInput?.value) || 0;
            totalPaid += amt;

            // Enable/disable remove button based on row count
            const removeBtn = row.querySelector('.remove-payment-row-btn');
            if (removeBtn) {
                removeBtn.disabled = (rows.length <= 1);
                if (rows.length <= 1) {
                    removeBtn.classList.add('opacity-40', 'cursor-not-allowed');
                } else {
                    removeBtn.classList.remove('opacity-40', 'cursor-not-allowed');
                }
            }
        });

        const grandTotal = getGrandTotal();
        const balance = grandTotal - totalPaid;

        if (paymentTotalDisplay) {
            paymentTotalDisplay.textContent = '₹' + totalPaid.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        if (paymentBalanceDisplay) {
            paymentBalanceDisplay.textContent = '₹' + Math.max(0, balance).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (balance > 0.005) {
                paymentBalanceDisplay.className = 'font-bold text-amber-600 dark:text-amber-400';
            } else {
                paymentBalanceDisplay.className = 'font-bold text-emerald-600 dark:text-emerald-400';
            }
        }
    }

    function reindexPaymentRows() {
        if (!paymentRowsContainer) return;
        const rows = paymentRowsContainer.querySelectorAll('.payment-row');
        rows.forEach((row, idx) => {
            const modeSelect = row.querySelector('.payment-mode-select');
            const amountInput = row.querySelector('.payment-amount-input');
            const refInput = row.querySelector('.payment-ref-input');

            if (modeSelect) modeSelect.name = `payments[${idx}][payment_mode_id]`;
            if (amountInput) amountInput.name = `payments[${idx}][amount]`;
            if (refInput) refInput.name = `payments[${idx}][reference_no]`;
        });
    }

    if (addPaymentRowBtn && paymentRowTemplate && paymentRowsContainer) {
        addPaymentRowBtn.addEventListener('click', function () {
            const rowsCount = paymentRowsContainer.querySelectorAll('.payment-row').length;
            const templateHtml = paymentRowTemplate.innerHTML.replace(/__INDEX__/g, rowsCount);

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = templateHtml.trim();
            const newRow = tempDiv.firstElementChild;

            // Calculate remaining unpaid amount for default row value
            const grandTotal = getGrandTotal();
            let currentPaid = 0;
            paymentRowsContainer.querySelectorAll('.payment-amount-input').forEach(input => {
                currentPaid += parseFloat(input.value) || 0;
            });
            const remaining = Math.max(0, grandTotal - currentPaid);

            const newAmountInput = newRow.querySelector('.payment-amount-input');
            if (newAmountInput) {
                newAmountInput.value = remaining.toFixed(2);
            }

            paymentRowsContainer.appendChild(newRow);
            reindexPaymentRows();
            updatePaymentTotals();
        });
    }

    if (paymentRowsContainer) {
        paymentRowsContainer.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.remove-payment-row-btn');
            if (removeBtn) {
                const rows = paymentRowsContainer.querySelectorAll('.payment-row');
                if (rows.length > 1) {
                    const row = removeBtn.closest('.payment-row');
                    if (row) {
                        row.remove();
                        reindexPaymentRows();
                        updatePaymentTotals();
                    }
                }
            }
        });

        paymentRowsContainer.addEventListener('input', function (e) {
            if (e.target.classList.contains('payment-amount-input')) {
                isUserModifiedPaymentAmounts = true;
                updatePaymentTotals();
            }
        });
    }

    /**
     * Apply a totals payload returned by the sales.calculate preview endpoint
     * to the DOM. Never recomputes anything itself.
     */
    function applyTotals(totals) {
        lastGrandTotal = parseFloat(totals.grand_total) || 0;

        if (!isUserModifiedRoundOff && roundOffInput) {
            roundOffInput.value = totals.round_off;
        }

        const fmt = (v) => '₹' + (parseFloat(v) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const taxAmountEl = document.getElementById('summary_tax_amount');
        if (taxAmountEl) taxAmountEl.textContent = fmt(totals.tax_amount);

        const cgstEl = document.getElementById('summary_cgst');
        if (cgstEl) cgstEl.textContent = fmt(totals.cgst_amount);

        const sgstEl = document.getElementById('summary_sgst');
        if (sgstEl) sgstEl.textContent = fmt(totals.sgst_amount);

        const igstEl = document.getElementById('summary_igst');
        if (igstEl) igstEl.textContent = fmt(totals.igst_amount);

        const grandTotalSpan = document.getElementById('summary_grand_total');
        if (grandTotalSpan) grandTotalSpan.textContent = fmt(lastGrandTotal);

        if (paidAmountInput) {
            paidAmountInput.value = lastGrandTotal.toFixed(2);
        }

        if (paymentRowsContainer && !isUserModifiedPaymentAmounts) {
            const rows = paymentRowsContainer.querySelectorAll('.payment-row');
            if (rows.length === 1) {
                const firstAmountInput = rows[0].querySelector('.payment-amount-input');
                if (firstAmountInput) {
                    firstAmountInput.value = lastGrandTotal.toFixed(2);
                }
            }
        }

        updatePaymentTotals();
    }

    function recalculateSummary() {
        if (!calculateUrl) {
            updatePaymentTotals();
            return;
        }

        clearTimeout(calculateDebounceTimer);
        calculateDebounceTimer = setTimeout(function () {
            const payload = {
                gst_type: gstTypeSelect ? gstTypeSelect.value : undefined,
                invoice_discount: invoiceDiscountInput ? (parseFloat(invoiceDiscountInput.value) || 0) : 0,
            };
            if (isUserModifiedRoundOff && roundOffInput) {
                payload.round_off = roundOffInput.value;
            }

            pendingCalculation = fetch(calculateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify(payload),
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data && data.success) {
                        applyTotals(data.totals);
                    }
                })
                .catch(function () { /* Preview failure leaves prior totals in place; save still recalculates server-side. */ })
                .finally(function () { pendingCalculation = null; });
        }, 250);
    }

    if (roundOffInput) {
        roundOffInput.addEventListener('input', function () {
            isUserModifiedRoundOff = true;
            recalculateSummary();
        });
        roundOffInput.addEventListener('change', function () {
            isUserModifiedRoundOff = true;
            recalculateSummary();
        });
    }

    if (invoiceDiscountInput) {
        invoiceDiscountInput.addEventListener('input', recalculateSummary);
        invoiceDiscountInput.addEventListener('change', recalculateSummary);
    }

    if (gstTypeSelect) {
        gstTypeSelect.addEventListener('change', recalculateSummary);
    }

    // Reflect the already-correct, server-rendered totals in the payment
    // section on load (no network call needed for the initial numbers).
    updatePaymentTotals();

    // Form submit guard & payment amount validation
    const submitBtn = document.getElementById('submit_conversion_btn');

    if (conversionForm && submitBtn) {
        conversionForm.addEventListener('submit', function (e) {
            if (pendingCalculation) {
                e.preventDefault();
                pendingCalculation.then(function () {
                    conversionForm.requestSubmit ? conversionForm.requestSubmit(submitBtn) : conversionForm.submit();
                });
                return;
            }

            if (saleTypeCash && saleTypeCash.checked) {
                let totalPaid = 0;
                if (paymentRowsContainer) {
                    paymentRowsContainer.querySelectorAll('.payment-amount-input').forEach(input => {
                        totalPaid += parseFloat(input.value) || 0;
                    });
                } else if (paidAmountInput) {
                    totalPaid = parseFloat(paidAmountInput.value) || 0;
                }

                const grandTotal = getGrandTotal();
                if (Math.round(totalPaid * 100) < Math.round(grandTotal * 100)) {
                    e.preventDefault();
                    const shortfall = (grandTotal - totalPaid).toFixed(2);
                    alert(`For Cash Sale (Instant Payment), total payments (₹${totalPaid.toFixed(2)}) must equal the Grand Total (₹${grandTotal.toFixed(2)}).\n\nPlease add the remaining balance of ₹${shortfall} or switch to Credit Sale for partial payments.`);
                    return false;
                }
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing Conversion...
            `;
        });
    }
});
