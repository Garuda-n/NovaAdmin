/**
 * NovaAdmin - Global Searchable Select Initializer (Select2)
 */
(function ($) {
    if (!$) {
        console.warn('jQuery is not loaded. Searchable select initialization skipped.');
        return;
    }
    'use strict';

    window.initSearchableSelects = function (container) {
        if (typeof $.fn.select2 === 'undefined') return;

        const $context = container ? $(container) : $(document);

        $context.find('select').each(function () {
            const $select = $(this);

            // Skip if already initialized, explicitly excluded, or inside specific excluded components
            if (
                $select.data('select2') ||
                $select.hasClass('no-searchable') ||
                $select.attr('data-no-searchable') !== undefined ||
                $select.hasClass('swal2-select')
            ) {
                return;
            }

            // Always enable search box for all select dropdowns
            const minimumResultsForSearch = 0;

            // Attach to document.body globally so window viewport height is used for position calculation
            const $dropdownParent = $(document.body);

            $select.select2({
                width: '100%',
                dropdownParent: $dropdownParent,
                minimumResultsForSearch: minimumResultsForSearch,
                language: {
                    noResults: function () {
                        return 'No matching options found';
                    }
                }
            });

            // Dispatch native change event ONLY on explicit user selection/clearing to prevent infinite loops
            $select.on('select2:select select2:clear', function () {
                const event = document.createEvent('HTMLEvents');
                event.initEvent('change', true, true);
                this.dispatchEvent(event);
            });
        });
    };

    $(document).ready(function () {
        window.initSearchableSelects();

        // Observe DOM mutations to auto-initialize new select elements added dynamically
        const observer = new MutationObserver(function (mutations) {
            let shouldInit = false;
            mutations.forEach(function (mutation) {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    for (let i = 0; i < mutation.addedNodes.length; i++) {
                        const node = mutation.addedNodes[i];
                        if (node.nodeType === 1) {
                            if (node.tagName === 'SELECT' || node.querySelector('select')) {
                                shouldInit = true;
                                break;
                            }
                        }
                    }
                }
            });
            if (shouldInit) {
                window.initSearchableSelects();
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });
    });
})(typeof jQuery !== 'undefined' ? jQuery : (typeof $ !== 'undefined' ? $ : null));
