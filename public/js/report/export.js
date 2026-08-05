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

/**
 * Print only the report content (KPI cards + data table) cleanly in a printable document.
 */
function printReportContent(containerId, title) {
    'use strict';
    const targetId = containerId || 'report-table-container';
    const container = document.getElementById(targetId);
    if (!container) {
        window.print();
        return;
    }

    const clone = container.cloneNode(true);

    // Remove buttons, pagination navs, action columns, and icons from print copy
    clone.querySelectorAll('button, .pagination, svg, th:last-child, td:last-child').forEach(el => {
        const text = el.textContent.trim().toLowerCase();
        if (text.includes('action') || el.classList.contains('pagination') || el.tagName === 'BUTTON' || el.tagName === 'svg') {
            el.remove();
        }
    });

    const reportTitle = title || 'Performance & Analytical Report';
    const currentDate = new Date().toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>${reportTitle}</title>
            <style>
                @page { size: A4 landscape; margin: 8mm; }
                * {
                    box-sizing: border-box;
                }
                body {
                    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                    color: #0f172a;
                    background: #ffffff;
                    margin: 0;
                    padding: 10px;
                    font-size: 11px;
                }
                /* Reset outer container borders & shadows */
                div, section, main {
                    border: none !important;
                    box-shadow: none !important;
                    outline: none !important;
                    border-radius: 0 !important;
                    background: transparent !important;
                }
                .print-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 2px solid #0f172a !important;
                    padding-bottom: 8px;
                    margin-bottom: 16px;
                }
                .brand-title {
                    font-size: 18px;
                    font-weight: 900;
                    color: #0f172a;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .sub-title {
                    font-size: 13px;
                    font-weight: 700;
                    color: #4f46e5;
                    margin-top: 2px;
                }
                .timestamp {
                    font-size: 10px;
                    color: #64748b;
                    font-weight: 600;
                }
                /* KPI Grid Styling */
                .grid {
                    display: grid !important;
                    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
                    gap: 10px !important;
                    margin-bottom: 16px !important;
                }
                .grid > div {
                    border: 1px solid #cbd5e1 !important;
                    border-radius: 6px !important;
                    padding: 8px 10px !important;
                    background: #f8fafc !important;
                }
                .uppercase { text-transform: uppercase; }
                .font-bold { font-weight: 700; }
                .font-black, .font-extrabold { font-weight: 900; }
                .text-emerald-600 { color: #059669; }
                .text-indigo-600 { color: #4f46e5; }
                .text-purple-600 { color: #9333ea; }
                .text-amber-600 { color: #d97706; }
                .text-blue-600 { color: #2563eb; }
                
                /* Table Styling - Clean Edge-to-Edge Grid */
                table {
                    width: 100% !important;
                    border-collapse: collapse !important;
                    margin-top: 6px !important;
                    font-size: 10px !important;
                    border: 1px solid #cbd5e1 !important;
                }
                th {
                    background-color: #f1f5f9 !important;
                    color: #0f172a !important;
                    font-weight: 800 !important;
                    text-transform: uppercase !important;
                    padding: 7px 9px !important;
                    border: 1px solid #cbd5e1 !important;
                    text-align: left !important;
                }
                td {
                    padding: 6px 9px !important;
                    border: 1px solid #cbd5e1 !important;
                    color: #334155 !important;
                }
                tr:nth-child(even) {
                    background-color: #f8fafc !important;
                }
                .text-right { text-align: right !important; }
                .text-center { text-align: center !important; }
                .print-footer {
                    margin-top: 16px;
                    padding-top: 8px;
                    border-top: 1px solid #e2e8f0 !important;
                    text-align: center;
                    font-size: 9px;
                    color: #94a3b8;
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <div>
                    <div class="brand-title">NovaAdmin ERP</div>
                    <div class="sub-title">${reportTitle}</div>
                </div>
                <div class="timestamp">
                    Printed: ${currentDate}
                </div>
            </div>
            <div>
                ${clone.innerHTML}
            </div>
            <div class="print-footer">
                NovaAdmin ERP Systems — Confidential Business Report
            </div>
        </body>
        </html>
    `);
    doc.close();

    setTimeout(() => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(() => {
            if (document.body.contains(iframe)) {
                document.body.removeChild(iframe);
            }
        }, 1000);
    }, 250);
}
