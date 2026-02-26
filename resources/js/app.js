import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = 'http://sistemapractica.test';

const app = createApp(App);
app.use(router);
app.mount('#app');

axios.interceptors.response.use(
    response => response, 
    error => {
        if (error.response && (error.response.status === 401 || error.response.status === 419)) {
            
            
            localStorage.removeItem('token'); 
            localStorage.removeItem('auth'); 
            
            
            window.location.href = '/login'; 
            
            alert("Tu sesión ha expirado por seguridad. Por favor, inicia sesión de nuevo.");
        }
        return Promise.reject(error);
    }
);