/**
 * NovaAdmin — Report Filters Handler (Alpine.js)
 *
 * Usage in x-data: reportFilters({ filterUrl, csrfToken, containerId, ...extraFields })
 * Merges with reportDateRange() for date handling.
 */
function reportFilters(config) {
    'use strict';

    // Merge date range handling
    const dateRange = reportDateRange({
        preset:    config.preset    || 'this_month',
        from_date: config.from_date || '',
        to_date:   config.to_date   || '',
    });

    // Collect extra filter field names (excluding framework keys)
    const reservedKeys = ['filterUrl', 'csrfToken', 'containerId', 'preset', 'from_date', 'to_date'];
    const extraFields  = {};
    for (const key in config) {
        if (!reservedKeys.includes(key)) {
            extraFields[key] = config[key] || '';
        }
    }

    return {
        ...dateRange,
        ...extraFields,
        filterUrl:   config.filterUrl   || '',
        csrfToken:   config.csrfToken   || '',
        containerId: config.containerId || 'report-table-container',
        loading: false,

        /**
         * Build form data from current filter state.
         */
        buildFormData() {
            const formData = new FormData();
            formData.append('_token', this.csrfToken);
            formData.append('preset', this.preset);
            formData.append('from_date', this.from_date);
            formData.append('to_date', this.to_date);

            for (const key in extraFields) {
                if (this[key] !== undefined && this[key] !== '') {
                    formData.append(key, this[key]);
                }
            }

            return formData;
        },

        /**
         * Apply filters via AJAX and replace table container HTML.
         */
        async applyFilter() {
            this.loading = true;
            try {
                const response = await fetch(this.filterUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: this.buildFormData(),
                });

                if (!response.ok) throw new Error('Filter request failed');

                const data = await response.json();
                const container = document.getElementById(this.containerId);
                if (container && data.html) {
                    container.innerHTML = data.html;
                }
            } catch (error) {
                console.error('Report filter error:', error);
            } finally {
                this.loading = false;
            }
        },

        /**
         * Reset all filter fields to defaults and re-fetch.
         */
        resetFilter() {
            this.preset = 'this_month';
            this.onPresetChange();

            for (const key in extraFields) {
                this[key] = '';
            }

            this.applyFilter();
        },
    };
}
