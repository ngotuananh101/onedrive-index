import { defineStore, acceptHMRUpdate } from 'pinia';
import { checkLogin, getToken } from '../services/apiService';

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
                // console.error('Failed to log in:', error);
            }
        },
        async getToken(code) {
            let data = await getToken(code);
            return data;
        },
    },
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useAuthStore, import.meta.hot));
}
