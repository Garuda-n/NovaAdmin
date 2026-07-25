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
