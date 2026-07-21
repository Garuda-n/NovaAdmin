/**
 * NovaAdmin - Stock Inward Index Alpine Component
 */
function stockInwardIndex() {
    return {
        search: '',
        companyId: '',
        branchId: '',
        supplierId: '',
        filterUrl: '/inventory/stock-inwards/filter',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        loading: false,

        showModal: false,
        modalHtml: '',
        modalLoading: false,

        init() {
            document.addEventListener('click', (e) => {
                const paginationLink = e.target.closest('#stock-inward-table-container .pagination-wrapper a, #stock-inward-table-container nav a');
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
            this.search = '';
            this.companyId = '';
            this.branchId = '';
            this.supplierId = '';
            this.applyFilter();
        },

        fetchData(url) {
            this.loading = true;
            const formData = new FormData();
            formData.append('_token', this.csrfToken);
            if (this.search) formData.append('search', this.search);
            if (this.companyId) formData.append('company_id', this.companyId);
            if (this.branchId) formData.append('branch_id', this.branchId);
            if (this.supplierId) formData.append('supplier_id', this.supplierId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.html) {
                    this.$refs.tableContainer.innerHTML = data.html;
                }
            })
            .catch(err => console.error('Filter error:', err))
            .finally(() => {
                this.loading = false;
            });
        },

        openInwardModal(id) {
            this.modalLoading = true;
            this.showModal = true;
            this.modalHtml = '<div class="flex items-center justify-center p-12 text-slate-300 font-semibold gap-3"><svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading Stock Inward Details...</div>';

            fetch(`/inventory/stock-inwards/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.html) {
                    this.modalHtml = data.html;
                }
            })
            .catch(err => {
                console.error('Error fetching stock inward details:', err);
                this.modalHtml = '<div class="text-red-400 p-6 text-center">Failed to load invoice details.</div>';
            })
            .finally(() => {
                this.modalLoading = false;
            });
        }
    };
}
