import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/authStore";

const routes = [
    {
        path: '/',
        component: () => import('../layouts/AppLayout.vue'),
        redirect: '/home',
        children: [
            { path: 'home', name: 'index', component: () => import('../pages/Home.vue') },
        ]
    },
    { 
        path: '/auth', 
        component: () => import('../layouts/AuthLayout.vue'),
        redirect: '/auth/step-1',
        children: [
            { path: 'step-1', name: 'auth.step1', component: () => import('../pages/auth/Step1.vue') },
        ]
    },
];
const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;

router.beforeEach(async (to, from, next) => {
    if (to.name.includes('auth')) {
        return next();
    } else {
        const authStore = useAuthStore();
        await authStore.login();
        const isLoggedIn = authStore.isLoggedIn;
        if (!isLoggedIn) {
            return next({ name: 'auth.step1' });
        }
        return next();
    }
});