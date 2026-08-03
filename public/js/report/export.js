/**
 * NovaAdmin — Report Export Utilities
 *
 * Provides CSV and PDF export stubs that build query strings
 * from the current filter state and trigger downloads.
 *
 * Usage: mix into your Alpine x-data with spread:
 *   { ...reportExport({ exportBaseUrl: '/reports/inventory' }), ...reportFilters({...}) }
 */
function reportExport(config) {
    'use strict';

    return {
        exportBaseUrl: config.exportBaseUrl || '',

        /**
         * Build query string from current filter state.
         */
        buildExportQuery() {
            const params = new URLSearchParams();

            if (this.preset)    params.append('preset', this.preset);
            if (this.from_date) params.append('from_date', this.from_date);
            if (this.to_date)   params.append('to_date', this.to_date);

            // Include any extra filter fields if they exist
            const reservedKeys = [
                'preset', 'from_date', 'to_date', 'filterUrl', 'csrfToken',
                'containerId', 'loading', 'exportBaseUrl',
            ];

            for (const key in this) {
                if (
                    typeof this[key] === 'string' &&
                    this[key] !== '' &&
                    !reservedKeys.includes(key) &&
                    !key.startsWith('$') &&
                    !key.startsWith('_')
                ) {
                    params.append(key, this[key]);
                }
            }

            return params.toString();
        },

        /**
         * Export report as CSV.
         * Endpoint to be implemented by each report module.
         */
        exportCsv() {
            const query = this.buildExportQuery();
            const url   = this.exportBaseUrl + '/export/csv' + (query ? '?' + query : '');
            window.location.href = url;
        },

        /**
         * Export report as PDF.
         * Endpoint to be implemented by each report module.
         */
        exportPdf() {
            const query = this.buildExportQuery();
            const url   = this.exportBaseUrl + '/export/pdf' + (query ? '?' + query : '');
            window.location.href = url;
        },
    };
}
