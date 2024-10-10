import { defineStore, acceptHMRUpdate } from 'pinia';

function getDarkMode() {
    let mode = localStorage.getItem('darkMode') === 'true';
    let element = document.documentElement;
    if (mode !== element.classList.contains('dark')) {
        if (mode) {
            element.classList.add('dark');
        } else {
            element.classList.remove('dark');
        }
    }
    return mode;
}

export const useSystemConfigStore = defineStore('darkMode', {
    state: () => {
        return {
            darkMode: getDarkMode(),
            listSocial: [
                {
                    icon: 'pi pi-facebook text-xl',
                    url: 'https://www.facebook.com/ngotuananh2101',
                },
                {
                    icon: 'pi pi-github text-xl',
                    url: 'https://github.com/ngotuananh101',
                },
            ],
            currentScrollY: 0,
        }
    },
    actions: {
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            let element = document.documentElement;
            if (this.darkMode && !element.classList.contains('dark')) {
                element.classList.add('dark');
            } else {
                element.classList.remove('dark');
            }
        }
    },
    getters: {
        getDarkMode: state => state.darkMode
    }
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useSystemConfigStore, import.meta.hot))
}