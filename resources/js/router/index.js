// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router';

// Importamos las vistas que acabamos de crear
import Home from '../views/Home.vue';
import Espacios from '../views/Espacios.vue';
import Docentes from '../views/Docentes.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/espacios',
        name: 'espacios',
        component: Espacios
    },
    {
        path: '/docentes',
        name: 'docentes',
        component: Docentes
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;