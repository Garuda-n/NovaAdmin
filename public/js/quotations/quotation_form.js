/**
 * NovaAdmin - Quotation Form Client-Side Entry Experience & Customer Search (jQuery)
 */
$(document).ready(function () {
    const $itemsBody = $('#quotation-items-body');
    const isConverted = $('#quotation-form-container').data('is-converted') == true;

    if (isConverted) {
        return; // Read-only mode for converted quotations
    }

    // ==========================================
    // 1. CUSTOMER SEARCHABLE SELECTION (WITH ARROW KEY NAVIGATION)
    // ==========================================
    const $customerSearchInput = $('#customer-search-input');
    const $customerResultsList = $('#customer-results-list');
    const $selectedCustomerId = $('#selected-customer-id');
    const $clearCustomerBtn = $('#clear-customer-btn');
    let highlightedIndex = -1;

    // Show clear button if customer is pre-selected
    if ($selectedCustomerId.val()) {
        $clearCustomerBtn.removeClass('hidden');
    }

    /**
     * Highlight an option at a given index among visible options
     */
    function highlightOption(index) {
        const $visibleOptions = $customerResultsList.find('.customer-option:not(.hidden)');
        if ($visibleOptions.length === 0) {
            highlightedIndex = -1;
            return;
        }

        if (index < 0) {
            index = $visibleOptions.length - 1;
        } else if (index >= $visibleOptions.length) {
            index = 0;
        }

        highlightedIndex = index;

        $visibleOptions.removeClass('active-option bg-indigo-600 text-white').addClass('text-slate-800 dark:text-slate-100');

        const $active = $visibleOptions.eq(highlightedIndex);
        $active.addClass('active-option bg-indigo-600 text-white').removeClass('text-slate-800 dark:text-slate-100');

        // Scroll active item into view inside dropdown
        if ($active.length) {
            const containerScroll = $customerResultsList.scrollTop();
            const containerHeight = $customerResultsList.height();
            const optionTop = $active.position().top;
            const optionHeight = $active.outerHeight();

            if (optionTop < 0) {
                $customerResultsList.scrollTop(containerScroll + optionTop);
            } else if (optionTop + optionHeight > containerHeight) {
                $customerResultsList.scrollTop(containerScroll + optionTop + optionHeight - containerHeight);
            }
        }
    }

    // Filter and display customer list on input, focus, or click
    $customerSearchInput.on('input focus click', function (e) {
        e.stopPropagation();
        const query = $(this).val().toLowerCase().trim();
        let matchCount = 0;

        $customerResultsList.find('.customer-option').each(function () {
            const searchContent = ($(this).attr('data-search') || $(this).data('search') || $(this).text() || '').toString().toLowerCase();

            if (!query || searchContent.includes(query)) {
                $(this).removeClass('hidden');
                matchCount++;
            } else {
                $(this).addClass('hidden');
            }
        });

        if (matchCount === 0 && query !== '') {
            $('#no-customer-found').removeClass('hidden');
        } else {
            $('#no-customer-found').addClass('hidden');
        }

        $customerResultsList.removeClass('hidden');

        // Auto highlight the first matching option
        highlightOption(0);
    });

    // Arrow keys & Enter key navigation inside customer search input
    $customerSearchInput.on('keydown', function (e) {
        const $visibleOptions = $customerResultsList.find('.customer-option:not(.hidden)');

        if (e.which === 40) { // Down Arrow
            e.preventDefault();
            if ($customerResultsList.hasClass('hidden')) {
                $customerResultsList.removeClass('hidden');
            }
            highlightOption(highlightedIndex + 1);
        } else if (e.which === 38) { // Up Arrow
            e.preventDefault();
            if ($customerResultsList.hasClass('hidden')) {
                $customerResultsList.removeClass('hidden');
            }
            highlightOption(highlightedIndex - 1);
        } else if (e.which === 13) { // Enter key
            e.preventDefault();
            e.stopPropagation();
            if ($visibleOptions.length > 0) {
                let $target = $visibleOptions.filter('.active-option');
                if (!$target.length) {
                    $target = $visibleOptions.first();
                }
                selectCustomer($target);
            }
            return false;
        } else if (e.which === 27) { // Escape key
            $customerResultsList.addClass('hidden');
            highlightedIndex = -1;
        }
    });

    // Handle mouse hover over customer options
    $(document).on('mouseenter', '.customer-option', function () {
        const $visibleOptions = $customerResultsList.find('.customer-option:not(.hidden)');
        const index = $visibleOptions.index($(this));
        if (index !== -1) {
            highlightOption(index);
        }
    });

    // Handle customer selection from list
    $(document).on('click', '.customer-option', function (e) {
        e.stopPropagation();
        selectCustomer($(this));
    });

    function selectCustomer($option) {
        const id = $option.attr('data-id') || $option.data('id');
        const display = $option.attr('data-display') || $option.data('display') || $option.text().trim();
        const type = $option.attr('data-type') || $option.data('type');

        $selectedCustomerId.val(id);
        $customerSearchInput.val(display);
        $customerResultsList.addClass('hidden');
        $clearCustomerBtn.removeClass('hidden');
        highlightedIndex = -1;

        // Auto select Customer Type radio button if matching
        if (type) {
            $('input[name="customer_type"][value="' + type + '"]').prop('checked', true);
        }
    }

    // Clear customer selection
    $clearCustomerBtn.on('click', function (e) {
        e.stopPropagation();
        $selectedCustomerId.val('');
        $customerSearchInput.val('').focus();
        $clearCustomerBtn.addClass('hidden');
        $customerResultsList.removeClass('hidden');
        highlightOption(0);
    });

    // Hide customer dropdown when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.customer-search-wrapper').length) {
            $customerResultsList.addClass('hidden');
            highlightedIndex = -1;
        }
    });

    // ==========================================
    // 2. PRODUCT TABLE & LIVE CALCULATIONS
    // ==========================================

    /**
     * Helper: Update row numbers (1, 2, 3...)
     */
    function updateRowNumbers() {
        $itemsBody.find('.quotation-row').each(function (index) {
            $(this).find('.row-number').text(index + 1);

            // Re-index input name attributes
            $(this).find('input, select').each(function () {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, 'items[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }

    /**
     * Helper: Enable/Disable remove buttons based on row count
     */
    function updateRemoveButtonsState() {
        const rowCount = $itemsBody.find('.quotation-row').length;
        if (rowCount <= 1) {
            $itemsBody.find('.btn-remove-row').prop('disabled', true).addClass('opacity-30 cursor-not-allowed');
        } else {
            $itemsBody.find('.btn-remove-row').prop('disabled', false).removeClass('opacity-30 cursor-not-allowed');
        }
    }

    /**
     * Load product details (UOM, Tax %) when product is selected
     */
    function loadProductDetails($row) {
        const $select = $row.find('.product-select');
        const $selectedOption = $select.find('option:selected');
        const productId = $select.val();

        if (productId && $selectedOption.length) {
            const uomId = $selectedOption.attr('data-uom-id') || $selectedOption.data('uom-id') || '';
            const uomName = $selectedOption.attr('data-uom-name') || $selectedOption.data('uom-name') || '';
            const rawTax = $selectedOption.attr('data-tax-percent') || $selectedOption.data('tax-percent') || 0;
            const taxPercent = parseFloat(rawTax) || 0.00;
            const productName = $selectedOption.attr('data-name') || $selectedOption.data('name') || '';

            $row.find('.product-name-input').val(productName);
            $row.find('.uom-id-input').val(uomId);
            $row.find('.uom-name-input').val(uomName);
            $row.find('.uom-name-hidden').val(uomName);
            $row.find('.tax-percent-input').val(taxPercent.toFixed(2));
        } else {
            $row.find('.product-name-input').val('');
            $row.find('.uom-id-input').val('');
            $row.find('.uom-name-input').val('');
            $row.find('.uom-name-hidden').val('');
            $row.find('.tax-percent-input').val('0.00');
        }
    }

    /**
     * Live calculation for a single row matching server-side PricingService
     */
    function calculateRow($row) {
        const productId = $row.find('.product-select').val();

        if (!productId) {
            $row.find('.tax-amount-input').val('0.00');
            $row.find('.line-total-input').val('0.00');
            return { subtotal: 0.00, taxAmount: 0.00, grandTotal: 0.00 };
        }

        const $qtyInput = $row.find('.qty-input');
        const $rateInput = $row.find('.rate-input');

        let qty = parseFloat($qtyInput.val());
        let rate = parseFloat($rateInput.val());
        
        const rawTax = $row.find('.tax-percent-input').val();
        let taxPercent = parseFloat(rawTax) || 0.00;

        // Prevent negative values or NaN
        if (isNaN(qty) || qty < 0) {
            qty = 0.00;
        }
        if (isNaN(rate) || rate < 0) {
            rate = 0.00;
        }
        if (isNaN(taxPercent) || taxPercent < 0) {
            taxPercent = 0.00;
        }

        // Calculation matching PricingService
        const subtotal = Math.round((qty * rate) * 100) / 100;
        const taxAmount = Math.round((subtotal * (taxPercent / 100.0)) * 100) / 100;
        const lineTotal = Math.round((subtotal + taxAmount) * 100) / 100;

        $row.find('.tax-amount-input').val(taxAmount.toFixed(2));
        $row.find('.line-total-input').val(lineTotal.toFixed(2));

        return {
            subtotal: subtotal,
            taxAmount: taxAmount,
            grandTotal: lineTotal
        };
    }

    /**
     * Live Summary Calculation
     */
    function calculateSummary() {
        let subtotal = 0.00;
        let taxAmount = 0.00;
        let grandTotal = 0.00;

        $itemsBody.find('.quotation-row').each(function () {
            const rowCalc = calculateRow($(this));
            subtotal += rowCalc.subtotal;
            taxAmount += rowCalc.taxAmount;
            grandTotal += rowCalc.grandTotal;
        });

        $('#summary-subtotal-display').text('₹ ' + subtotal.toFixed(2));
        $('#summary-subtotal-input').val(subtotal.toFixed(2));

        $('#summary-tax-amount-display').text('₹ ' + taxAmount.toFixed(2));
        $('#summary-tax-amount-input').val(taxAmount.toFixed(2));

        $('#summary-grand-total-display').text('₹ ' + grandTotal.toFixed(2));
        $('#summary-grand-total-input').val(grandTotal.toFixed(2));
    }

    /**
     * Add a new empty product row
     */
    function addRow() {
        const $firstRow = $itemsBody.find('.quotation-row').first();
        const $newRow = $firstRow.clone();

        // Reset inputs in new row
        $newRow.find('input').val('');
        $newRow.find('.product-select').val('');
        $newRow.find('.qty-input').val('1');
        $newRow.find('.rate-input').val('0.00');
        $newRow.find('.tax-percent-input').val('0.00');
        $newRow.find('.tax-amount-input').val('0.00');
        $newRow.find('.line-total-input').val('0.00');

        $itemsBody.append($newRow);

        updateRowNumbers();
        updateRemoveButtonsState();
        calculateSummary();

        return $newRow;
    }

    /**
     * Remove a product row
     */
    function removeRow($row) {
        if ($itemsBody.find('.quotation-row').length > 1) {
            $row.remove();
            updateRowNumbers();
            updateRemoveButtonsState();
            calculateSummary();
        }
    }

    // Initialize initial calculations & row states on page load
    $itemsBody.find('.quotation-row').each(function () {
        loadProductDetails($(this));
        calculateRow($(this));
    });
    updateRemoveButtonsState();
    calculateSummary();

    // ==========================================
    // EVENT DELEGATION
    // ==========================================

    // Click: + Add Product Button
    $(document).on('click', '#btn-add-product', function () {
        const $newRow = addRow();
        $newRow.find('.product-select').focus();
    });

    // Click: Remove Row Button
    $(document).on('click', '.btn-remove-row', function () {
        const $row = $(this).closest('.quotation-row');
        removeRow($row);
    });

    // Change: Product Select
    $(document).on('change', '.product-select', function () {
        const $row = $(this).closest('.quotation-row');
        loadProductDetails($row);
        calculateRow($row);
        calculateSummary();

        // Auto focus Qty when Product selected & recalculate immediately
        if ($(this).val()) {
            $row.find('.qty-input').focus().select();
        }
    });

    // Input / Change: Qty & Rate Live Calculation
    $(document).on('input change', '.qty-input, .rate-input', function () {
        const $row = $(this).closest('.quotation-row');

        // Prevent negative values
        let val = parseFloat($(this).val());
        if (val < 0) {
            $(this).val(0);
        }

        calculateRow($row);
        calculateSummary();
    });

    // Keyboard Navigation (Enter Key Focus Movement)
    $(document).on('keydown', '.qty-input', function (e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const $row = $(this).closest('.quotation-row');
            $row.find('.rate-input').focus().select();
        }
    });

    $(document).on('keydown', '.rate-input', function (e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const $row = $(this).closest('.quotation-row');
            const $nextRow = $row.next('.quotation-row');

            if ($nextRow.length) {
                $nextRow.find('.product-select').focus();
            } else {
                const $newRow = addRow();
                $newRow.find('.product-select').focus();
            }
        }
    });
});
