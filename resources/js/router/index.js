import { createRouter, createWebHistory } from 'vue-router';

import Home from '../views/Home.vue';
import Espacios from '../views/Espacios.vue';
import Docentes from '../views/Docentes.vue';
import Grupos from '../views/Grupos.vue';
import Horarios from '../views/Horarios.vue';
import Login from '../views/Login.vue';


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
        meta: { requiresAuth: true } // 🔒 Etiqueta de seguridad
    },
    { 
        path: '/espacios', 
        name: 'espacios', 
        component: () => import('../views/Espacios.vue'),
        meta: { requiresAuth: true } // 🔒 Etiqueta de seguridad
    },
    { 
        path: '/grupos', 
        name: 'grupos', 
        component: () => import('../views/Grupos.vue'),
        meta: { requiresAuth: true } // 🔒 Etiqueta de seguridad
    },
    { 
        path: '/horarios', 
        name: 'horarios', 
        component: () => import('../views/Horarios.vue'),
        meta: { requiresAuth: true } // 🔒 Etiqueta de seguridad
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


router.beforeEach((to, from, next) => {
  
    const isAuthenticated = localStorage.getItem('auth'); 

  
    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' }); 
    }
    
    else if (to.name === 'login' && isAuthenticated) {
        next({ name: 'home' }); 
    }
    
    else {
        next();
    }
});

export default router;