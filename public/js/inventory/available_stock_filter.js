/**
 * NovaAdmin - Available Stock Filter Module
 */
function availableStockFilter(config = {}) {
    return {
        branch_id: config.branch_id || '',
        counter_id: config.counter_id || '',
        category_id: config.category_id || '',
        product_id: config.product_id || '',
        sub_product_id: config.sub_product_id || '',
        size_id: config.size_id || '',
        item_code: config.item_code || '',
        filterUrl: config.filterUrl || '/inventory/available-stock/filter',
        csrfToken: config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        loading: false,

        init() {
            // Intercept pagination clicks inside table container
            document.addEventListener('click', (e) => {
                const paginationLink = e.target.closest('#available-stock-table-container .pagination-wrapper a, #available-stock-table-container nav a');
                if (paginationLink && paginationLink.href) {
                    e.preventDefault();
                    this.fetchData(paginationLink.href);
                }
            });
        },

        applyFilter() {
            this.fetchData(this.filterUrl);
        },

        resetFilter() {
            this.branch_id = '';
            this.counter_id = '';
            this.category_id = '';
            this.product_id = '';
            this.sub_product_id = '';
            this.size_id = '';
            this.item_code = '';
            this.applyFilter();
        },

        fetchData(url) {
            this.loading = true;
            const formData = new FormData();
            formData.append('_token', this.csrfToken);
            if (this.branch_id) formData.append('branch_id', this.branch_id);
            if (this.counter_id) formData.append('counter_id', this.counter_id);
            if (this.category_id) formData.append('category_id', this.category_id);
            if (this.product_id) formData.append('product_id', this.product_id);
            if (this.sub_product_id) formData.append('sub_product_id', this.sub_product_id);
            if (this.size_id) formData.append('size_id', this.size_id);
            if (this.item_code) formData.append('item_code', this.item_code);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result.html) {
                    const tableContainer = document.getElementById('available-stock-table-container');
                    if (tableContainer) {
                        tableContainer.innerHTML = result.html;
                    }
                }
            })
            .catch(error => {
                console.error('Available stock filter error:', error);
            })
            .finally(() => {
                this.loading = false;
            });
        }
    };
}
