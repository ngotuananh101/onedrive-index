import { defineStore, acceptHMRUpdate } from 'pinia';
import { checkLogin } from '../services/apiService';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        _isLoggedIn: false,
    }),
    getters: {
        isLoggedIn: (state) => state._isLoggedIn,
    },
    actions: {
        async login() {
            try {
                let isLogin = await checkLogin();
                this._isLoggedIn = isLogin;
            } catch (error) {
                console.error('Failed to log in:', error);
            }
        },
        logout() {
            this._isLoggedIn = false;
            // Add any additional logout logic here (e.g., clearing tokens)
        },
    },
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useAuthStore, import.meta.hot));
}
