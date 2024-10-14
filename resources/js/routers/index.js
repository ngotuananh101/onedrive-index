import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/authStore";

const routes = [
    {
        path: '/',
        component: () => import('../layouts/HomeLayout.vue'),
        redirect: '/home',
        children: [
            { path: 'home', name: 'index', component: () => import('../pages/home/Home.vue') },
            { path: 'folders/:id', name: 'folders.show', component: () => import('../pages/folders/Show.vue') },
        ]
    },
    {
        path: '/auth',
        component: () => import('../layouts/AuthLayout.vue'),
        redirect: '/auth/step-1',
        children: [
            { path: 'step-1', name: 'auth.step1', component: () => import('../pages/auth/Step1.vue') },
            { path: 'step-2', name: 'auth.step2', component: () => import('../pages/auth/Step2.vue') },
            { path: 'step-3', name: 'auth.step3', component: () => import('../pages/auth/Step3.vue') },
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
