/**
 * NovaAdmin - Global Searchable Select Initializer (Select2)
 */
(function ($) {
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

            // Force dropdown menu to open downwards below the selection box
            $select.on('select2:open', function () {
                setTimeout(function () {
                    const $dropdown = $('.select2-dropdown');
                    if ($dropdown.hasClass('select2-dropdown--above')) {
                        $dropdown.removeClass('select2-dropdown--above').addClass('select2-dropdown--below');
                        const $container = $select.next('.select2-container');
                        if ($container.length) {
                            const containerOffset = $container.offset();
                            $dropdown.css('top', (containerOffset.top + $container.outerHeight()) + 'px');
                        }
                    }
                }, 0);
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
})(jQuery);
