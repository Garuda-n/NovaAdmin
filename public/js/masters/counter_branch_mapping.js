/**
 * NovaAdmin - Counter Branch Mapping Modal Logic
 */
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('branchModal');
    if (!modal) return;

    const overlay = document.getElementById('modalOverlay');
    const title = document.getElementById('counterTitle');
    const form = document.getElementById('branchForm');

    document.querySelectorAll('.assignBranchBtn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const mappedBranches = JSON.parse(this.dataset.branches || '[]');
            if (title) title.textContent = name;
            if (form) form.action = `/counters/${id}/branch-mapping`;
            
            // Reset all checkboxes then check mapped ones
            if (form) {
                form.querySelectorAll('input[name="branch_ids[]"]').forEach(cb => {
                    cb.checked = mappedBranches.includes(parseInt(cb.value));
                });
            }
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('closeModal')?.addEventListener('click', closeModal);
    document.getElementById('cancelModal')?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
