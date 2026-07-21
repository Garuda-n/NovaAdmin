/**
 * NovaAdmin - Item Allocation Filter Module
 */
function itemAllocationFilter() {
    return {
        search: '',
        branch_id: '',
        supplier_id: '',
        category_id: '',
        filterUrl: '/inventory/item-allocation/filter',

        applyFilter() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const data = {
                search: this.search,
                branch_id: this.branch_id,
                supplier_id: this.supplier_id,
                category_id: this.category_id,
                _token: token
            };

            fetch(this.filterUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.html) {
                    document.getElementById('allocation-table-container').innerHTML = result.html;
                }
            })
            .catch(error => {
                console.error('Filter error:', error);
            });
        },

        resetFilter() {
            this.search = '';
            this.branch_id = '';
            this.supplier_id = '';
            this.category_id = '';
            this.applyFilter();
        }
    };
}
