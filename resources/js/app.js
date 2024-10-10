import './bootstrap';
import '../css/app.css';
import '../css/fa6/css/all.css';
import 'primeicons/primeicons.css';
import 'vue3-perfect-scrollbar/style.css';

// Import library
import { createApp } from 'vue';
import PrimeVue from 'primevue/config';
import Lara from '@primevue/themes/lara';
import routers from './routers';
import { createPinia } from 'pinia'
import { PerfectScrollbarPlugin } from 'vue3-perfect-scrollbar';

// Import home component
import App from './App.vue';

// Create app
const app = createApp(App);
app.use(createPinia());
app.use(PrimeVue, {
    theme: {
        preset: Lara,
        options: {
            cssLayer: {
                name: 'primevue',
                order: 'tailwind-base, primevue, tailwind-utilities'
            },
            darkModeSelector: '.dark',
        }
    }
});
app.use(routers);
app.use(PerfectScrollbarPlugin);

// Mount app
app.mount('#app');