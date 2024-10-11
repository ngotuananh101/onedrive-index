import './bootstrap';
import '../css/app.css';
import '../css/fa6/css/all.min.css';

// Import library
import { createApp } from 'vue';
import routers from './routers';
import { createPinia } from 'pinia'
import { PerfectScrollbarPlugin } from 'vue3-perfect-scrollbar';
import { createI18n } from 'vue-i18n';
import messages from './lang/index';

const i18n = createI18n({
    locale: 'vi',
    fallbackLocale: 'vi',
    messages: messages
})

// Import home component
import App from './App.vue';

// Create app
const app = createApp(App);
app.use(createPinia());
app.use(routers);
app.use(PerfectScrollbarPlugin);
app.use(i18n);

// Mount app
app.mount('#app');
