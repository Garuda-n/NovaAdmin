/**
 * NovaAdmin - Early Theme Initialization Script (Prevents Dark/Light Mode FOUC)
 */
if (localStorage.getItem('theme') === 'dark') {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}
