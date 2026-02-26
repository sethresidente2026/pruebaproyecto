import { createRouter, createWebHistory } from 'vue-router';
import { storage } from '../../utils/storage';


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
    meta: { requiresAuth: true } 
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
    component: () => import('../views/Asistencias.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/actividades',
    name: 'actividades',
    component: () => import('../views/Actividades.vue'),
    meta: { requiresAuth: true }
  },
  
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    redirect: { name: 'home' } 
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Guard de navegación
router.beforeEach((to, from, next) => {
    
    
    const isAuthenticated = storage.get('auth'); 

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