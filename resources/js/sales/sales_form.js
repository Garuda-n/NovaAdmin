/**
 * Sales Form Interactivity & Dynamic Calculation Engine
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

    function recalculateSummary() {
        const summaryCard = document.getElementById('invoice_summary_card');
        if (!summaryCard) return;

        const subtotal = parseFloat(summaryCard.dataset.subtotal) || 0;
        const itemDiscount = parseFloat(summaryCard.dataset.itemDiscount) || 0;
        const taxAmount = parseFloat(summaryCard.dataset.taxAmount) || 0;

        const invoiceDiscount = Math.max(0, parseFloat(invoiceDiscountInput?.value) || 0);
        const netSubtotal = Math.max(0, subtotal - itemDiscount - invoiceDiscount);
        const unroundedTotal = netSubtotal + taxAmount;

        let roundOff = 0;

        if (isUserModifiedRoundOff) {
            roundOff = parseFloat(roundOffInput.value) || 0;
        } else {
            const roundedTotal = Math.round(unroundedTotal);
            roundOff = parseFloat((roundedTotal - unroundedTotal).toFixed(2));
            if (roundOffInput) {
                roundOffInput.value = roundOff;
            }
        }

        const grandTotal = Math.max(0, unroundedTotal + roundOff);

        const grandTotalSpan = document.getElementById('summary_grand_total');
        if (grandTotalSpan) {
            grandTotalSpan.textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        if (paidAmountInput) {
            paidAmountInput.value = grandTotal.toFixed(2);
        }
    }

    if (invoiceDiscountInput) {
        invoiceDiscountInput.addEventListener('input', recalculateSummary);
        invoiceDiscountInput.addEventListener('change', recalculateSummary);
    }

    recalculateSummary();

    // Form submit guard
    const conversionForm = document.getElementById('sales_conversion_form');
    const submitBtn = document.getElementById('submit_conversion_btn');

    if (conversionForm && submitBtn) {
        conversionForm.addEventListener('submit', function () {
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
