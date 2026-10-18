// Dark / Light Theme Manager for Karasu B Cafe
(function () {
    const THEME_KEY = 'bcafe_theme';

    function getTheme() {
        if (typeof localStorage !== 'undefined' && localStorage.getItem(THEME_KEY)) {
            return localStorage.getItem(THEME_KEY);
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    // Apply immediately to prevent flash
    const initialTheme = getTheme();
    applyTheme(initialTheme);

    // Global toggle function with optional View Transition API support
    window.toggleDarkMode = function () {
        const isDark = document.documentElement.classList.contains('dark');
        const nextTheme = isDark ? 'light' : 'dark';
        
        const update = () => {
            if (typeof localStorage !== 'undefined') {
                localStorage.setItem(THEME_KEY, nextTheme);
            }
            applyTheme(nextTheme);
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: nextTheme } }));
        };

        // If browser supports View Transition API and user prefers normal motion
        if (typeof document !== 'undefined' && 'startViewTransition' in document && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.startViewTransition(() => update());
        } else {
            update();
        }

        return nextTheme;
    };

    window.isDarkMode = function () {
        return document.documentElement.classList.contains('dark');
    };

    // Listen for system theme changes if user has not set explicit preference
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem(THEME_KEY)) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });
})();
