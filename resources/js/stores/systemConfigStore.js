import { defineStore, acceptHMRUpdate } from 'pinia';
import { useColorMode } from '@vueuse/core';
const colorMode = useColorMode();


function getThemeMode() {
    let mode = localStorage.getItem('theme-mode');
    console.log('load theme mode:', mode);

    if (mode == 'auto'  || mode == null){
        mode = colorMode.system.value;
    }
    return mode;
}

export const useSystemConfigStore = defineStore('system', {
    state: () => {
        return {
            themeMode: getThemeMode(),
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
        }
    },
    actions: {
        switchThemeMode(mode) {
            let listMode = ['dark', 'light', 'auto'];
            if (!listMode.includes(mode)) {
                mode = this.themeMode == 'dark' ? 'light' : 'dark';
            }
            this.themeMode = mode;
            localStorage.setItem('theme-mode', mode);
            colorMode.value = mode;
            document.documentElement.className = this.themeMode;
        },
        init(){
            this.switchThemeMode(this.themeMode);
        }
    },
    getters: {

    }
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useSystemConfigStore, import.meta.hot))
}
