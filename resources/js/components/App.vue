<template>
    <div class="app-container">
        <header class="navbar" v-if="$route.name !== 'login'">
            <div class="logo-container">
                <div class="logo-text"><span class="ugm-red">UGM</span> CENTRO</div>
                <div class="subtitle">Sistema de Gestión Académica</div>
            </div>
            <nav class="links">
                <router-link to="/" class="nav-item">Inicio</router-link>
                <router-link to="/espacios" class="nav-item">Espacios</router-link>
                <router-link to="/docentes" class="nav-item">Docentes</router-link>
                <router-link to="/grupos" class="nav-item">Grupos</router-link>
                <router-link to="/horarios" class="nav-item">Horarios</router-link>
                <button @click="cerrarSesion" class="btn-logout">Cerrar Sesión</button>
            </nav>
        </header>

        <main class="content">
            <router-view></router-view>
        </main>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const cerrarSesion = async () => {
    try {
        await axios.post('/api/logout'); // Avisamos al backend
    } catch (e) { console.error(e); }
    
    // Rompemos el pase de entrada y lo regresamos
    localStorage.removeItem('auth');
    router.push('/login');
};
</script>

<style>
/* Variables de color de la UGM */
:root {
    --rojo-ugm: #D1101A;
    --gris-oscuro: #2C3E50;
}

.navbar {
    background-color: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 4px solid var(--rojo-ugm);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.nav-item {
    text-decoration: none;
    color: var(--gris-oscuro);
    font-weight: bold;
    margin-right: 20px; /* Esto les da el espacio que falta */
    padding: 8px 12px;
    border-radius: 4px;
    transition: 0.3s;
}

.nav-item:hover {
    background-color: #fce4e4;
    color: var(--rojo-ugm);
}

.router-link-active {
    color: white !important;
    background-color: var(--rojo-ugm);
}
</style>