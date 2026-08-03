/**
 * NovaAdmin - Stock Inward Form Alpine Component
 */
function stockInwardForm(initialItems, availableProducts) {
    return {
        items: initialItems && initialItems.length > 0 ? initialItems : [
            { category_id: '', product_id: '', sub_product_id: '', qty: 1, weight: '', purchase_price: '', selling_price: '', mrp: '', remarks: '' }
        ],
        products: availableProducts || [],
        getProductsByCategory(categoryId) {
            if (!categoryId) return [];
            return this.products.filter(p => p.category_id == categoryId);
        },
        getSubProducts(productId) {
            if (!productId) return [];
            const prod = this.products.find(p => p.id == productId);
            if (!prod || !prod.has_sub_product) return [];
            return prod.sub_products || [];
        },
        hasSubProducts(productId) {
            return this.getSubProducts(productId).length > 0;
        },
        onCategoryChange(row, event) {
            row.product_id = '';
            row.sub_product_id = '';
            if (event && event.target && typeof jQuery !== 'undefined') {
                const $tr = jQuery(event.target).closest('tr');
                setTimeout(function () {
                    $tr.find('select').trigger('change.select2');
                }, 50);
            }
        },
        onProductChange(row, event) {
            const subProds = this.getSubProducts(row.product_id);
            if (subProds.length === 0) {
                row.sub_product_id = '';
            } else if (row.sub_product_id) {
                if (!subProds.some(sp => sp.id == row.sub_product_id)) {
                    row.sub_product_id = '';
                }
            }
            if (event && event.target && typeof jQuery !== 'undefined') {
                const $tr = jQuery(event.target).closest('tr');
                setTimeout(function () {
                    $tr.find('select').trigger('change.select2');
                }, 50);
            }
        },
        addRow() {
            this.items.push({
                category_id: '',
                product_id: '',
                sub_product_id: '',
                qty: 1,
                weight: '',
                purchase_price: '',
                selling_price: '',
                mrp: '',
                remarks: ''
            });
            if (typeof window.initSearchableSelects === 'function') {
                setTimeout(function () {
                    window.initSearchableSelects();
                }, 100);
            }
        },
        removeRow(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        initCategoryFromProduct() {
            this.items.forEach(row => {
                if (row.product_id && !row.category_id) {
                    const prod = this.products.find(p => p.id == row.product_id);
                    if (prod) {
                        row.category_id = String(prod.category_id);
                    }
                }
            });
        }
    };
}
