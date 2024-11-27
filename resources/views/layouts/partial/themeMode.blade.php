<script>
    function setThemeMode(mode) {
        if (mode === 'dark') {
            document.documentElement.classList.remove('light');
            document.documentElement.classList.add('dark');
            // Add data-theme attribute
            document.documentElement.setAttribute('data-theme', 'dark');
            setCookies('theme-mode', 'dark', 365);
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            // Remove data-theme attribute
            document.documentElement.setAttribute('data-theme', 'light');
            setCookies('theme-mode', 'light', 365);
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

    function setCookies(name, value, days) {
        // get current cookies
        const cookies = document.cookie.split(';').map(cookie => cookie.trim());
        // find the cookie
        const cookie = cookies.find(cookie => cookie.startsWith(name));
        // if cookie exists
        if (cookie) {
            // update the cookie
            document.cookie = `${name}=${value}; max-age=${60 * 60 * 24 * days}; path=/`;
        } else {
            // create the cookie
            document.cookie = `${name}=${value}; max-age=${60 * 60 * 24 * days}; path=/`;
        }
    }

    initThemeMode();
</script>
