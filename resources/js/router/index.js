import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import Espacios from '../views/Espacios.vue';
import Docentes from '../views/Docentes.vue';
import Grupos from '../views/Grupos.vue';
import Horarios from '../views/Horarios.vue';
import Login from '../views/Login.vue'; // <--- 1. Importamos Login

const routes = [
    { path: '/login', name: 'login', component: Login }, // Ruta pública
    
    // TODAS ESTAS RUTAS AHORA ESTÁN PROTEGIDAS
    { path: '/', name: 'home', component: Home, meta: { requiresAuth: true } },
    { path: '/espacios', name: 'espacios', component: Espacios, meta: { requiresAuth: true } },
    { path: '/docentes', name: 'docentes', component: Docentes, meta: { requiresAuth: true } },
    { path: '/grupos', name: 'grupos', component: Grupos, meta: { requiresAuth: true } },
    { path: '/horarios', name: 'horarios', component: Horarios, meta: { requiresAuth: true } }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// 2. EL CADENERO: Se ejecuta antes de cambiar de página
router.beforeEach((to, from, next) => {
    // Revisamos si el navegador tiene el "pase de entrada"
    const estaAutenticado = localStorage.getItem('auth') === 'true';

    if (to.meta.requiresAuth && !estaAutenticado) {
        // Si quiere entrar a una zona protegida y no tiene pase -> Al Login
        next('/login');
    } else if (to.name === 'login' && estaAutenticado) {
        // Si ya inició sesión y quiere ir al Login -> Lo mandamos al Inicio
        next('/');
    } else {
        // En cualquier otro caso, lo dejamos pasar
        next();
    }
});

export default router;