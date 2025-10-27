
import { createApp } from 'vue';
import App from './App.vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import router from './router';
import api from './http.js'; 
import { useAuthStore } from './stores/auth';

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

const app = createApp(App);
app.use(pinia);
app.use(router);
app.mount('#app');

const auth = useAuthStore();

if (auth.token) {
  api.defaults.headers.common['Authorization'] = `Bearer ${auth.token}`;
  
}