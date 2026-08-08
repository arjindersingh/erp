import Alpine from 'alpinejs';

window.Alpine = Alpine;

const themeStorageKey = 'erp-theme';
const systemTheme = () => window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

window.applyErpTheme = (theme) => {
    const resolvedTheme = theme === 'system' ? systemTheme() : theme;

    document.documentElement.dataset.theme = resolvedTheme;
    document.documentElement.dataset.themePreference = theme;
};

const savedTheme = localStorage.getItem(themeStorageKey) || 'system';
window.applyErpTheme(savedTheme);

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if ((localStorage.getItem(themeStorageKey) || 'system') === 'system') window.applyErpTheme('system');
});

window.themeMenu = () => ({
    open: false,
    init() {
        this.theme = localStorage.getItem(themeStorageKey) || 'system';
    },
    setTheme(theme) {
        window.applyErpTheme(theme);
        localStorage.setItem(themeStorageKey, theme);
        this.theme = theme;
        this.open = false;
    },
});

Alpine.start();

const revealElements = document.querySelectorAll('.scroll-reveal');

if ('IntersectionObserver' in window && ! window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (! entry.isIntersecting) return;

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12 });

    revealElements.forEach((element) => observer.observe(element));
} else {
    revealElements.forEach((element) => element.classList.add('is-visible'));
}
