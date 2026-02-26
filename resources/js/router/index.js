import { createRouter, createWebHistory } from 'vue-router';

import Home from '../views/Home.vue';
import Espacios from '../views/Espacios.vue';
import Docentes from '../views/Docentes.vue';
import Grupos from '../views/Grupos.vue';
import Horarios from '../views/Horarios.vue';
import Login from '../views/Login.vue';
import Asistencias from '../views/Asistencias.vue';
import Actividades from '../views/Actividades.vue';

const routes = [
    { 
        path: '/login', 
        name: 'login', 
        component: () => import('../views/Login.vue') 
    },
    { 
        path: '/', 
        name: 'home', 
        component: () => import('../views/Home.vue'),
        meta: { requiresAuth: true } // 🔒 Etiqueta de seguridad
    },
    { 
        path: '/docentes', 
        name: 'docentes', 
        component: () => import('../views/Docentes.vue'),
        meta: { requiresAuth: true } 
    },
    { 
        path: '/espacios', 
        name: 'espacios', 
        component: () => import('../views/Espacios.vue'),
        meta: { requiresAuth: true } 
    },
    { 
        path: '/grupos', 
        name: 'grupos', 
        component: () => import('../views/Grupos.vue'),
        meta: { requiresAuth: true } 
    },
    { 
        path: '/horarios', 
        name: 'horarios', 
        component: () => import('../views/Horarios.vue'),
        meta: { requiresAuth: true } 
    },
    {
    path: '/asistencias',
    name: 'asistencias',
    component: Asistencias,
    meta: { requiresAuth: true }
},
{
path: '/actividades',
    name: 'actividades',
    component: Actividades,
    meta: { requiresAuth: true }
}
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


router.beforeEach((to, from, next) => {
    // Usamos 'auth' como la fuente única de verdad
    const isAuthenticated = localStorage.getItem('auth'); 

    if (to.meta.requiresAuth && !isAuthenticated) {
        // Si intenta entrar a una ruta protegida (Docentes, Grupos, etc.) sin auth
        next({ name: 'login' }); 
    }
    else if (to.name === 'login' && isAuthenticated) {
        // Si ya está logueado e intenta ir al login, lo mandamos al home
        next({ name: 'home' }); 
    }
    else {
        next();
    }
});
export default router;