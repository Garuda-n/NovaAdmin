/**
 * NovaAdmin - Common Toast Auto-Dismiss JS
 */
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        const toast = document.getElementById('toast');
        if (toast) {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 500);
        }
    }, 2500);
});
