<script>
    function setThemeMode(mode) {
        if (mode === 'dark') {
            document.documentElement.classList.remove('light');
            document.documentElement.classList.add('dark');
            // Add data-theme attribute
            document.documentElement.setAttribute('data-theme', 'dark');
            Cookies.set('theme-mode', 'dark', {
                expires: 365
            });
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            // Remove data-theme attribute
            document.documentElement.setAttribute('data-theme', 'light');
            Cookies.set('theme-mode', 'light', {
                expires: 365
            });
        }
    }

    function toggleThemeMode() {
        const currentMode = localStorage.getItem('theme-mode');
        const newMode = currentMode === 'dark' ? 'light' : 'dark';

        localStorage.setItem('theme-mode', newMode);
        setThemeMode(newMode);
    }

    function initThemeMode() {
        const savedMode = localStorage.getItem('theme-mode');
        if (savedMode) {
            setThemeMode(savedMode);
        } else {
            const userPrefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const initialMode = userPrefersDarkMode ? 'dark' : 'light';
            setThemeMode(initialMode);
            localStorage.setItem('theme-mode', initialMode);
        }
    }

    initThemeMode();
</script>
