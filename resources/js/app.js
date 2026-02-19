import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import axios from 'axios';

axios.interceptors.response.use(
    (response) => {
        
        return response;
    },
    (error) => {
        
        if (error.response && (error.response.status === 401 || error.response.status === 419)) {
            console.warn('Sesión expirada o no autorizada.');
            
            
            localStorage.removeItem('auth');
            
          
            router.push({ name: 'login' });
        }
        
        return Promise.reject(error);
    }
);

const app = createApp(App);
app.use(router);
app.mount('#app');