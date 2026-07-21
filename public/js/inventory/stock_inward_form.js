/**
 * NovaAdmin - Stock Inward Form Alpine Component
 */
function stockInwardForm(initialItems, availableProducts) {
    return {
        items: initialItems && initialItems.length > 0 ? initialItems : [
            { product_id: '', sub_product_id: '', qty: 1, weight: '', purchase_price: '', selling_price: '', mrp: '', remarks: '' }
        ],
        products: availableProducts || [],
        getSubProducts(productId) {
            if (!productId) return [];
            const prod = this.products.find(p => p.id == productId);
            if (!prod || !prod.has_sub_product) return [];
            return prod.sub_products || [];
        },
        hasSubProducts(productId) {
            return this.getSubProducts(productId).length > 0;
        },
        onProductChange(row) {
            const subProds = this.getSubProducts(row.product_id);
            if (subProds.length === 0) {
                row.sub_product_id = '';
            } else if (row.sub_product_id) {
                if (!subProds.some(sp => sp.id == row.sub_product_id)) {
                    row.sub_product_id = '';
                }
            }
        },
        addRow() {
            this.items.push({
                product_id: '',
                sub_product_id: '',
                qty: 1,
                weight: '',
                purchase_price: '',
                selling_price: '',
                mrp: '',
                remarks: ''
            });
        },
        removeRow(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        }
    };
}
