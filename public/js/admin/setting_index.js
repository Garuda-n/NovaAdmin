/**
 * NovaAdmin - Setting Index Filter Alpine Component
 */
function ajaxSettingFilter(config) {
    return {
        search: config.search || '',
        filterUrl: config.filterUrl || '/settings/filter',
        csrfToken: config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        loading: false,

        init() {
            // Intercept pagination links inside table container
            document.addEventListener('click', (e) => {
                const paginationLink = e.target.closest('#setting-table-container .pagination-wrapper a, #setting-table-container nav a');
                if (paginationLink && paginationLink.href) {
                    e.preventDefault();
                    this.fetchData(paginationLink.href);
                }
            });
        },

        applyFilter() {
            this.fetchData(this.filterUrl);
        },

        fetchData(targetUrl) {
            this.loading = true;
            const formData = new FormData();
            formData.append('_token', this.csrfToken);
            if (this.search) formData.append('search', this.search);

            fetch(targetUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.html && this.$refs.tableContainer) {
                    this.$refs.tableContainer.innerHTML = data.html;
                }
            })
            .catch(err => console.error('Error filtering settings:', err))
            .finally(() => {
                this.loading = false;
            });
        }
    };
}
