/**
 * NovaAdmin - Role Permissions Group Toggle JS
 */
function toggleGroup(selectAllCheckbox, group) {
    const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
    checkboxes.forEach(cb => {
        if (!cb.disabled) {
            cb.checked = selectAllCheckbox.checked;
        } else {
            cb.checked = false;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Ensure all disabled checkboxes are explicitly unchecked
    document.querySelectorAll('.permission-checkbox:disabled').forEach(cb => {
        cb.checked = false;
    });

    // Update "Select All" state on page load
    document.querySelectorAll('.select-all-group').forEach(selectAll => {
        const group = selectAll.dataset.group;
        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]:not(:disabled)`);
        const allChecked = checkboxes.length > 0 && [...checkboxes].every(cb => cb.checked);
        selectAll.checked = allChecked;
    });

    // Update "Select All" when individual checkboxes change
    document.querySelectorAll('.permission-checkbox:not(:disabled)').forEach(cb => {
        cb.addEventListener('change', function() {
            const group = this.dataset.group;
            const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]:not(:disabled)`);
            const selectAll = document.querySelector(`.select-all-group[data-group="${group}"]`);
            if (selectAll) {
                selectAll.checked = checkboxes.length > 0 && [...checkboxes].every(c => c.checked);
            }
        });
    });
});
