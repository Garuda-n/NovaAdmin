/**
 * NovaAdmin - Common Toast Auto-Dismiss & Dynamic Toast JS
 */
window.showToast = function (message, type = 'success') {
    const existing = document.getElementById('toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'toast';
    toast.className = `fixed top-5 right-5 px-4 py-3 rounded-lg shadow-lg z-[9999] text-white animate-slide-in ${
        type === 'success' ? 'bg-green-500 dark:bg-emerald-600' : 'bg-red-500 dark:bg-rose-600'
    }`;
    toast.innerText = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
};

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        const toast = document.getElementById('toast');
        if (toast) {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 500);
        }
    }, 2500);
});
