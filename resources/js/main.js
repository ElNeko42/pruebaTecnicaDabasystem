import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createVuetify } from 'vuetify';
import axios from 'axios';
import App from './App.vue';

axios.defaults.baseURL = '/api';
axios.defaults.headers.common.Accept = 'application/json';
const token = localStorage.getItem('admin_token');
if (token) axios.defaults.headers.common.Authorization = `Bearer ${token}`;
const vuetify = createVuetify({ theme: { defaultTheme: 'light' } });
createApp(App).use(createPinia()).use(vuetify).mount('#app');
