import axios from 'axios';
import { storage } from './storage';
const api = axios.create({
    baseURL: 'http://tu-api-ugm.test/api'
});

api.interceptors.request.use(config => {
    const auth = storage.get('auth');
    if (auth && auth.token) {
        config.headers.Authorization = `Bearer ${auth.token}`;
    }
    return config;
});

export default api;