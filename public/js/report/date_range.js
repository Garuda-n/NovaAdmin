/**
 * NovaAdmin — Report Date Range Handler (Alpine.js)
 *
 * Usage in x-data: { ...reportDateRange({ preset, from_date, to_date }), ...yourOtherData }
 */
function reportDateRange(config) {
    'use strict';

    /**
     * Calculate from/to dates for a given preset.
     */
    function resolveDates(preset) {
        const today = new Date();
        let from, to;

        switch (preset) {
            case 'today':
                from = to = formatDate(today);
                break;

            case 'yesterday': {
                const d = new Date(today);
                d.setDate(d.getDate() - 1);
                from = to = formatDate(d);
                break;
            }

            case 'last_7_days': {
                const d = new Date(today);
                d.setDate(d.getDate() - 6);
                from = formatDate(d);
                to = formatDate(today);
                break;
            }

            case 'last_30_days': {
                const d = new Date(today);
                d.setDate(d.getDate() - 29);
                from = formatDate(d);
                to = formatDate(today);
                break;
            }

            case 'this_month':
                from = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                to = formatDate(today);
                break;

            case 'last_month': {
                const first = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const last  = new Date(today.getFullYear(), today.getMonth(), 0);
                from = formatDate(first);
                to = formatDate(last);
                break;
            }

            case 'this_year':
                from = formatDate(new Date(today.getFullYear(), 0, 1));
                to = formatDate(today);
                break;

            case 'last_year':
                from = formatDate(new Date(today.getFullYear() - 1, 0, 1));
                to = formatDate(new Date(today.getFullYear() - 1, 11, 31));
                break;

            case 'custom':
                // Do nothing — user controls the dates manually
                return null;

            default:
                from = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                to = formatDate(today);
        }

        return { from_date: from, to_date: to };
    }

    /**
     * Format a Date object to YYYY-MM-DD string.
     */
    function formatDate(d) {
        const yyyy = d.getFullYear();
        const mm   = String(d.getMonth() + 1).padStart(2, '0');
        const dd   = String(d.getDate()).padStart(2, '0');
        return yyyy + '-' + mm + '-' + dd;
    }

    // Resolve initial dates from preset if not explicitly provided
    const initial = resolveDates(config.preset || 'this_month');

    return {
        preset:    config.preset    || 'this_month',
        from_date: config.from_date || (initial ? initial.from_date : ''),
        to_date:   config.to_date   || (initial ? initial.to_date   : ''),

        /**
         * Called when the preset dropdown changes.
         */
        onPresetChange() {
            const dates = resolveDates(this.preset);
            if (dates) {
                this.from_date = dates.from_date;
                this.to_date   = dates.to_date;
            }
        },
    };
}
