/**
 * NovaAdmin - Individual Stock Item Allocation Module
 */

let currentAllocPendingQty = 0;
let currentItemGenMode = 'bulk';
let lastSelectedCounterId = null;
let lastSelectedSizeId = null;
let lastSelectedSubProductId = null;

function openAllocationModal(stockInwardItemId) {
    const modal = document.getElementById('allocation-modal');
    if (!modal) return;

    // Capture current selection before fetching if already set
    const currentCounterVal = document.getElementById('alloc-counter-id')?.value;
    if (currentCounterVal) lastSelectedCounterId = currentCounterVal;

    const currentSizeVal = document.getElementById('alloc-size-id')?.value;
    if (currentSizeVal) lastSelectedSizeId = currentSizeVal;

    const currentSubProductVal = document.getElementById('alloc-sub-product-id')?.value;
    if (currentSubProductVal) lastSelectedSubProductId = currentSubProductVal;

    document.getElementById('alloc-stock-inward-item-id').value = stockInwardItemId;
    
    // Fetch pending info
    fetch(`/inventory/stock-inwards/items/${stockInwardItemId}/pending-info`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(response => {
        if (!response.success) {
            showAllocationToast(response.message || 'Failed to load item info.', 'error');
            return;
        }

        const data = response.data;
        currentAllocPendingQty = data.pending_qty;
        currentItemGenMode = data.item_generation_mode;

        // Populate Summary Card
        document.getElementById('alloc-modal-invoice-no').innerText = data.invoice_no;
        document.getElementById('alloc-modal-supplier').innerText = data.supplier_name;
        document.getElementById('alloc-modal-category').innerText = data.category_name;
        document.getElementById('alloc-modal-product').innerText = `${data.product_code} - ${data.product_name}`;
        document.getElementById('alloc-modal-received-qty').innerText = data.received_qty;
        document.getElementById('alloc-modal-allocated-qty').innerText = data.allocated_qty;
        document.getElementById('alloc-modal-pending-qty').innerText = data.pending_qty;

        // Populate Counter Select Options (preserve selected counter)
        let counterOptions = '<option value="">-- Select Counter --</option>';
        data.counters.forEach(c => {
            const isSelected = (String(c.id) === String(lastSelectedCounterId));
            counterOptions += `<option value="${c.id}" ${isSelected ? 'selected' : ''}>${c.name}</option>`;
        });
        document.getElementById('alloc-counter-id').innerHTML = counterOptions;

        // Populate Size Options (preserve selected size)
        let sizeOptions = '<option value="">-- None --</option>';
        data.sizes.forEach(s => {
            const isSelected = (String(s.id) === String(lastSelectedSizeId));
            sizeOptions += `<option value="${s.id}" ${isSelected ? 'selected' : ''}>${s.name}</option>`;
        });
        document.getElementById('alloc-size-id').innerHTML = sizeOptions;

        // Populate Sub Product Options (preserve selected sub product)
        let subOptions = '<option value="">-- None --</option>';
        data.sub_products.forEach(sp => {
            const isSelected = lastSelectedSubProductId 
                ? (String(sp.id) === String(lastSelectedSubProductId))
                : (sp.id === data.sub_product_id);
            subOptions += `<option value="${sp.id}" ${isSelected ? 'selected' : ''}>${sp.code} - ${sp.name}</option>`;
        });
        document.getElementById('alloc-sub-product-id').innerHTML = subOptions;

        // Handle Mode Display
        const bulkContainer = document.getElementById('alloc-bulk-quantity-container');
        const individualNotice = document.getElementById('alloc-individual-mode-notice');
        const completedAlert = document.getElementById('alloc-completed-alert');
        const submitBtn = document.getElementById('alloc-submit-btn');
        const btnText = document.getElementById('alloc-btn-text');

        if (data.is_completed || data.pending_qty <= 0) {
            bulkContainer.classList.add('hidden');
            individualNotice.classList.add('hidden');
            completedAlert.classList.remove('hidden');
            submitBtn.disabled = true;
            btnText.innerText = 'Allocation Completed';
        } else if (data.item_generation_mode === 'individual') {
            bulkContainer.classList.add('hidden');
            individualNotice.classList.remove('hidden');
            completedAlert.classList.add('hidden');
            submitBtn.disabled = false;
            btnText.innerText = 'Generate One Item';
        } else {
            bulkContainer.classList.remove('hidden');
            individualNotice.classList.add('hidden');
            completedAlert.classList.add('hidden');
            submitBtn.disabled = false;
            btnText.innerText = 'Generate Items';
            
            const qtyInput = document.getElementById('alloc-quantity');
            qtyInput.max = data.pending_qty;
            qtyInput.value = data.pending_qty;
        }

        modal.classList.remove('hidden');
    })
    .catch(err => {
        showAllocationToast('Error fetching item allocation details.', 'error');
    });
}

function closeAllocationModal() {
    const modal = document.getElementById('allocation-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function submitAllocationForm(event) {
    event.preventDefault();
    const submitBtn = document.getElementById('alloc-submit-btn');
    const counterId = document.getElementById('alloc-counter-id').value;
    const stockInwardItemId = document.getElementById('alloc-stock-inward-item-id').value;
    const sizeId = document.getElementById('alloc-size-id').value;
    const subProductId = document.getElementById('alloc-sub-product-id').value;
    
    if (!counterId) {
        showAllocationToast('Counter selection is mandatory.', 'error');
        return;
    }

    // Freeze / remember selections for subsequent allocations
    lastSelectedCounterId = counterId;
    if (sizeId) lastSelectedSizeId = sizeId;
    if (subProductId) lastSelectedSubProductId = subProductId;

    let quantity = 1;
    if (currentItemGenMode === 'bulk') {
        const qtyInput = document.getElementById('alloc-quantity');
        quantity = parseInt(qtyInput.value, 10);
        if (isNaN(quantity) || quantity <= 0) {
            showAllocationToast('Quantity must be greater than zero.', 'error');
            return;
        }
        if (quantity > currentAllocPendingQty) {
            showAllocationToast('Quantity cannot exceed Pending Quantity.', 'error');
            return;
        }
    }

    submitBtn.disabled = true;

    fetch('/inventory/stock-inwards/allocate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            stock_inward_item_id: stockInwardItemId,
            counter_id: counterId,
            size_id: sizeId || null,
            sub_product_id: subProductId || null,
            quantity: quantity
        })
    })
    .then(res => res.json().then(data => ({ status: res.status, body: data })))
    .then(res => {
        submitBtn.disabled = false;
        if (res.status === 200 && res.body.success) {
            showAllocationToast(res.body.message, 'success');

            // Refresh modal numbers
            openAllocationModal(stockInwardItemId);

            // Update line item in main page table dynamically
            const allocatedCell = document.getElementById(`item-allocated-qty-${stockInwardItemId}`);
            const pendingCell = document.getElementById(`item-pending-qty-${stockInwardItemId}`);
            const actionCell = document.getElementById(`item-action-cell-${stockInwardItemId}`);

            if (allocatedCell && pendingCell) {
                const prevAlloc = parseInt(allocatedCell.innerText, 10) || 0;
                const prevPending = parseInt(pendingCell.innerText, 10) || 0;
                const newAlloc = prevAlloc + res.body.data.allocated_count;
                const newPending = Math.max(0, prevPending - res.body.data.allocated_count);

                allocatedCell.innerText = newAlloc;
                pendingCell.innerText = newPending;

                if (newPending <= 0 && actionCell) {
                    actionCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300">Allocation Completed</span>';
                }
            }

        } else {
            const errorMsg = res.body.message || 'Pending quantity has changed. Please refresh and try again.';
            showAllocationToast(errorMsg, 'error');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        showAllocationToast('Pending quantity has changed. Please refresh and try again.', 'error');
    });
}

function showAllocationToast(message, type = 'success') {
    const existing = document.getElementById('dynamic-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'dynamic-toast';
    toast.className = `fixed top-5 right-5 px-4 py-3 rounded-lg shadow-xl z-[9999] text-white text-xs font-semibold transition-all duration-300 ${type === 'success' ? 'bg-emerald-600' : 'bg-rose-600'}`;
    toast.innerText = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}
