/**
 * NovaAdmin - Customer Index Filter Alpine Component
 */
function ajaxCustomerFilter(config) {
    return {
        search: config.search || '',
        customerType: config.customerType || '',
        branchId: config.branchId || '',
        filterUrl: config.filterUrl || '/customers/filter',
        csrfToken: config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        loading: false,

        showModal: false,
        modalHtml: '',
        modalLoading: false,

        init() {
            // Intercept pagination clicks inside table container
            document.addEventListener('click', (e) => {
                const paginationLink = e.target.closest('#customer-table-container .pagination-wrapper a, #customer-table-container nav a');
                if (paginationLink && paginationLink.href) {
                    e.preventDefault();
                    this.fetchData(paginationLink.href);
                }
            });
        },

        openCustomerModal(id) {
            this.modalLoading = true;
            this.showModal = true;
            this.modalHtml = '<div class="flex items-center justify-center p-12 text-slate-300 font-semibold gap-3"><svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading Customer Details...</div>';

            fetch(`/customers/${id}`, {
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
                console.error('Error fetching customer details:', err);
                this.modalHtml = '<div class="text-red-400 p-6 text-center">Failed to load customer details.</div>';
            })
            .finally(() => {
                this.modalLoading = false;
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
            if (this.customerType) formData.append('customer_type', this.customerType);
            if (this.branchId) formData.append('branch_id', this.branchId);

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
            .catch(err => console.error('Error filtering customers:', err))
            .finally(() => {
                this.loading = false;
            });
        }
    };
}
