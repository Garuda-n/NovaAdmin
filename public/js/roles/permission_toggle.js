/**
 * NovaAdmin - Role Permissions Group Toggle JS
 */
function toggleGroup(selectAllCheckbox, group) {
    const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
    checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
}

document.addEventListener('DOMContentLoaded', function() {
    // Update "Select All" state on page load
    document.querySelectorAll('.select-all-group').forEach(selectAll => {
        const group = selectAll.dataset.group;
        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
        const allChecked = [...checkboxes].every(cb => cb.checked);
        selectAll.checked = allChecked && checkboxes.length > 0;
    });

    // Update "Select All" when individual checkboxes change
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const group = this.dataset.group;
            const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
            const selectAll = document.querySelector(`.select-all-group[data-group="${group}"]`);
            if (selectAll) {
                selectAll.checked = [...checkboxes].every(c => c.checked);
            }
        });
    });
});
