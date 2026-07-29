(function () {
    const $ = window.jQuery || window.$;
    if (!$) return;

    $(function () {
        let debounceTimer;

    // Open Search Modal when clicking the row input
    $(document).on('click', '.inventory-search-input', function () {
        const $input = $(this);
        if ($input.is(':disabled')) return;
        
        const $container = $input.closest('.inventory-search-container');
        const $modal = $container.find('.inventory-search-modal');
        const $modalInput = $modal.find('.modal-search-input');

        // Open modal
        $modal.removeClass('hidden').addClass('flex');
        
        // Focus and select the search input inside modal
        $modalInput.focus().select();
        
        // If there's already search text, trigger search immediately
        if ($modalInput.val().trim().length >= 2) {
            $modalInput.trigger('input');
        }
    });

    // Handle AJAX search inside the modal
    $(document).on('input', '.modal-search-input', function () {
        const $modalInput = $(this);
        const $container = $modalInput.closest('.inventory-search-container');
        const $results = $container.find('.modal-search-results');
        const query = $modalInput.val().trim();

        clearTimeout(debounceTimer);

        if (query.length < 2) {
            $results.html('<div class="p-4 text-center text-xs text-slate-500 dark:text-slate-400">Type at least 2 characters to search inventory stock...</div>');
            return;
        }

        debounceTimer = setTimeout(() => {
            const branchId = $('[name="branch_id"]').val() || '';
            
            $.ajax({
                url: '/api/inventory/search',
                method: 'GET',
                data: {
                    search: query,
                    branch_id: branchId
                },
                success: function (response) {
                    if (response.status && response.data) {
                        renderModalResults($results, response.data);
                    }
                },
                error: function () {
                    $results.html('<div class="p-4 text-red-500 text-xs text-center font-semibold">Error searching inventory.</div>');
                }
            });
        }, 250);
    });

    // Render search results inside the modal
    function renderModalResults($results, items) {
        $results.empty();

        if (items.length === 0) {
            $results.html('<div class="p-4 text-center text-xs text-slate-500 dark:text-slate-400">No available inventory found.</div>');
            return;
        }

        items.forEach((item, index) => {
            let displayHtml = '';
            if (item.tracking_type === 2) { // Individual
                displayHtml = `
                    <div class="inventory-option px-4 py-3 border-b border-slate-100 dark:border-slate-800 last:border-0 hover:bg-indigo-600 hover:text-white cursor-pointer transition text-xs" data-index="${index}">
                        <div class="font-bold text-indigo-600 dark:text-indigo-400 flex justify-between items-center option-title">
                            <span>${item.item_code}</span>
                            <span class="text-[10px] bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 px-1.5 py-0.5 rounded">Individual</span>
                        </div>
                        <div class="font-semibold text-slate-800 dark:text-slate-200 mt-1 option-name">${item.product_name}</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1.5 flex justify-between option-meta">
                            <span>Available: 1 | UOM: ${item.uom_name}</span>
                            <span class="font-bold text-slate-800 dark:text-slate-100 option-price">₹ ${item.rate.toFixed(2)}</span>
                        </div>
                    </div>
                `;
            } else { // Bulk
                displayHtml = `
                    <div class="inventory-option px-4 py-3 border-b border-slate-100 dark:border-slate-800 last:border-0 hover:bg-indigo-600 hover:text-white cursor-pointer transition text-xs" data-index="${index}">
                        <div class="font-bold text-emerald-600 dark:text-emerald-400 flex justify-between items-center option-title">
                            <span>${item.product_name}</span>
                            <span class="text-[10px] bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 px-1.5 py-0.5 rounded">Bulk</span>
                        </div>
                        <div class="font-semibold text-slate-500 dark:text-slate-400 mt-1 option-name">${item.product_code || ''}</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1.5 flex justify-between option-meta">
                            <span>Available: ${item.available_qty} ${item.uom_name}</span>
                            <span class="font-bold text-slate-800 dark:text-slate-100 option-price">₹ ${item.rate.toFixed(2)}</span>
                        </div>
                    </div>
                `;
            }
            
            const $option = $(displayHtml);
            $option.data('item', item);
            $results.append($option);
        });
    }

    // Keyboard Navigation inside Modal Search Input
    $(document).on('keydown', '.modal-search-input', function (e) {
        const $modalInput = $(this);
        const $container = $modalInput.closest('.inventory-search-container');
        const $modal = $container.find('.inventory-search-modal');
        const $results = $container.find('.modal-search-results');
        
        const $options = $results.find('.inventory-option');
        let $active = $results.find('.inventory-option.bg-indigo-600');
        let activeIndex = $options.index($active);

        if (e.which === 40) { // Arrow Down
            e.preventDefault();
            if (activeIndex === -1 || activeIndex === $options.length - 1) {
                activeIndex = 0;
            } else {
                activeIndex++;
            }
            $options.removeClass('bg-indigo-600 text-white').find('*').removeClass('text-white text-indigo-200 text-emerald-200');
            $active = $options.eq(activeIndex).addClass('bg-indigo-600 text-white');
            $active.find('*').addClass('text-white');
        } else if (e.which === 38) { // Arrow Up
            e.preventDefault();
            if (activeIndex === -1 || activeIndex === 0) {
                activeIndex = $options.length - 1;
            } else {
                activeIndex--;
            }
            $options.removeClass('bg-indigo-600 text-white').find('*').removeClass('text-white text-indigo-200 text-emerald-200');
            $active = $options.eq(activeIndex).addClass('bg-indigo-600 text-white');
            $active.find('*').addClass('text-white');
        } else if (e.which === 13) { // Enter
            e.preventDefault();
            if ($active.length) {
                selectItem($container, $active.data('item'));
                $modal.removeClass('flex').addClass('hidden');
            }
        } else if (e.which === 27) { // Escape
            e.preventDefault();
            $modal.removeClass('flex').addClass('hidden');
        }
    });

    // Close Modal actions
    $(document).on('click', '.close-search-modal', function () {
        const $modal = $(this).closest('.inventory-search-modal');
        $modal.removeClass('flex').addClass('hidden');
    });

    // Close modal when clicking backdrop overlay
    $(document).on('click', '.inventory-search-modal', function (e) {
        if ($(e.target).hasClass('inventory-search-modal')) {
            $(this).removeClass('flex').addClass('hidden');
        }
    });

    // Close modals on Escape key globally
    $(document).on('keydown', function (e) {
        if (e.which === 27) { // Escape key
            $('.inventory-search-modal').removeClass('flex').addClass('hidden');
        }
    });

    // Click option in search results
    $(document).on('click', '.inventory-option', function () {
        const $option = $(this);
        const $container = $option.closest('.inventory-search-container');
        const $modal = $container.find('.inventory-search-modal');
        
        selectItem($container, $option.data('item'));
        $modal.removeClass('flex').addClass('hidden');
    });

    // Clear selection
    $(document).on('click', '.inventory-search-clear', function (e) {
        e.stopPropagation(); // Avoid opening modal on clear click
        const $container = $(this).closest('.inventory-search-container');
        clearSelection($container);
    });

    // Helper: Select inventory item
    function selectItem($container, item) {
        const $row = $container.closest('tr, .quotation-row');
        const displayText = item.item_code ? item.item_code : item.product_name;

        $container.find('.inventory-search-input').val(displayText);
        $container.find('.product-id-input').val(item.product_id);
        $container.find('.stock-item-id-input').val(item.stock_item_id || '');
        $container.find('.tracking-type-input').val(item.tracking_type);
        $container.find('.available-qty-input').val(item.available_qty);
        
        // Set modal value as well so it's ready if they open search again
        $container.find('.modal-search-input').val(displayText);
        
        $row.find('.product-name-input').val(item.product_name);
        $row.find('.uom-id-input').val(item.uom_id);
        $row.find('.uom-name-input').val(item.uom_name);
        $row.find('.uom-name-hidden').val(item.uom_name);
        $row.find('.tax-percent-input').val(item.tax_percent.toFixed(2));
        $row.find('.rate-input').val(item.rate.toFixed(2));
        
        $container.find('.inventory-search-clear').removeClass('hidden');

        let infoText = '';
        if (item.tracking_type === 2) { // Individual
            infoText = `Item: ${item.item_code} | Available: 1`;
            $row.find('.qty-input').val(1).prop('readonly', true).addClass('bg-slate-100 dark:bg-slate-900 cursor-not-allowed');
        } else { // Bulk
            infoText = `Available: ${item.available_qty}`;
            $row.find('.qty-input').prop('readonly', false).removeClass('bg-slate-100 dark:bg-slate-900 cursor-not-allowed');
        }
        $container.find('.inventory-item-info').text(infoText);
        $container.find('.qty-warning-container').addClass('hidden');

        // Trigger calculations
        $row.find('.rate-input').trigger('change');
        $row.find('.qty-input').trigger('change');
    }

    // Helper: Clear selection
    function clearSelection($container) {
        const $row = $container.closest('tr, .quotation-row');

        $container.find('.inventory-search-input').val('');
        $container.find('.modal-search-input').val('');
        $container.find('.product-id-input').val('');
        $container.find('.stock-item-id-input').val('');
        $container.find('.tracking-type-input').val('');
        $container.find('.available-qty-input').val('');
        $container.find('.inventory-item-info').empty();
        $container.find('.qty-warning-container').addClass('hidden');
        $container.find('.inventory-search-clear').addClass('hidden');

        $row.find('.product-name-input').val('');
        $row.find('.uom-id-input').val('');
        $row.find('.uom-name-input').val('');
        $row.find('.uom-name-hidden').val('');
        $row.find('.tax-percent-input').val('0.00');
        $row.find('.rate-input').val('0.00');
        $row.find('.qty-input').val('1').prop('readonly', false).removeClass('bg-slate-100 dark:bg-slate-900 cursor-not-allowed');

        // Trigger calculations
        $row.find('.rate-input').trigger('change');
        $row.find('.qty-input').trigger('change');
    }

    // Quantity warnings and enforcement
    $(document).on('input change', '.qty-input', function () {
        const $row = $(this).closest('tr, .quotation-row');
        const trackingType = parseInt($row.find('.tracking-type-input').val());
        const availableQty = parseFloat($row.find('.available-qty-input').val()) || 0;
        const $warning = $row.find('.qty-warning-container');

        if (trackingType === 2) { // Individual
            if (parseFloat($(this).val()) !== 1) {
                $(this).val(1);
            }
            $row.find('.qty-input').prop('readonly', true).addClass('bg-slate-100 dark:bg-slate-900 cursor-not-allowed');
            $warning.addClass('hidden');
        } else if (trackingType === 1) { // Bulk
            const qty = parseFloat($(this).val()) || 0;
            if (qty > availableQty) {
                $warning.text(`⚠️ Exceeds available stock (${availableQty})`).removeClass('hidden');
            } else {
                $warning.addClass('hidden');
            }
        }
    });

    // Run warnings check on page load
    setTimeout(() => {
        $('.qty-input').trigger('change');
    }, 100);
    });
})();
