<template>
    <div class="app-container">
        <header class="navbar" v-if="$route.name !== 'login'">
            <div class="navbar-brand">
                <div class="logo-text"><span class="text-ugm-red">UGM</span> CENTRO</div>
                <div class="subtitle">Sistema de Gestión Académica</div>
            </div>
            
            <nav class="navbar-nav">
    <router-link to="/" class="nav-item">
        <i class="fa-solid fa-house"></i> Inicio
    </router-link>
    <router-link to="/espacios" class="nav-item">
        <i class="fa-solid fa-building"></i> Espacios
    </router-link>
    <router-link to="/docentes" class="nav-item">
        <i class="fa-solid fa-user-tie"></i> Docentes
    </router-link>
    <router-link to="/grupos" class="nav-item">
        <i class="fa-solid fa-users"></i> Grupos
    </router-link>
    <router-link to="/horarios" class="nav-item">
        <i class="fa-solid fa-calendar-days"></i> Horarios
    </router-link>
    <router-link to="/asistencias" class="nav-link" active-class="active">
    <i class="fa-solid fa-clipboard-user"></i>
    <span>Asistencias</span>
</router-link>
    
    <div class="nav-divider"></div>
    
    <button @click="cerrarSesion" class="btn-logout">
        <i class="fa-solid fa-right-from-bracket"></i> Salir
    </button>
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
        await axios.post('/logout'); // Corrección: quitamos /api/ si estás usando web.php para la sesión
    } catch (e) { console.error("Error al cerrar sesión", e); }
    
    localStorage.removeItem('auth');
    router.push('/login');
};
</script>

<style>
/* =========================================
   1. VARIABLES GLOBALES (Paleta UGM)
   ========================================= */
:root {
  --ugm-red: #D1101A;
  --ugm-dark: #2C3E50;
  --ugm-light: #F8F9FA;
  --excel-green: #27ae60;
  --border-color: #E0E4E8;
  --bg-app: #F4F7F6; /* Fondo gris muy suave para que las tarjetas resalten */
}

/* Reseteo básico y fondo de la app */
body {
    margin: 0;
    font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
    background-color: var(--bg-app);
    color: var(--ugm-dark);
}

.app-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* =========================================
   2. BARRA DE NAVEGACIÓN (Navbar)
   ========================================= */
.navbar {
    background-color: #ffffff;
    height: 70px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
    border-bottom: 3px solid var(--ugm-red); /* Detalle institucional */
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar-brand {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.logo-text {
    font-size: 1.5rem;
    font-weight: 900;
    letter-spacing: 0.5px;
    color: var(--ugm-dark);
}

.text-ugm-red {
    color: var(--ugm-red);
}

.subtitle {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #7f8c8d;
    letter-spacing: 1px;
    font-weight: 600;
}

.navbar-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Estilos de los Enlaces */
.nav-item {
    text-decoration: none;
    color: #5c6a79;
    font-weight: 600;
    font-size: 0.95rem;
    padding: 8px 14px;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.nav-item:hover {
    background-color: #f0f2f5;
    color: var(--ugm-dark);
}

/* Estado Activo (Cuando estás en esa página) */
.router-link-active {
    background-color: #fce4e4;
    color: var(--ugm-red);
}

.nav-divider {
    width: 1px;
    height: 30px;
    background-color: var(--border-color);
    margin: 0 10px;
}

/* Botón de Cerrar Sesión Elegante */
.btn-logout {
    background: transparent;
    border: 1px solid #e74c3c;
    color: #e74c3c;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-logout:hover {
    background: #e74c3c;
    color: white;
    box-shadow: 0 4px 10px rgba(231, 76, 60, 0.2);
}

/* =========================================
   3. CONTENEDOR DE VISTAS (Main)
   ========================================= */
.content {
    flex-grow: 1;
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
    box-sizing: border-box;
}

/* =========================================
   4. ESTILOS GLOBALES (Tablas, Cards, etc.)
   ========================================= */
.module-container {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.04);
}

/* ... (Aquí continúan intactos tus estilos de .custom-table, .badge-count, etc. que ya teníamos) ... */
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.badge-count { background: var(--ugm-light); color: var(--ugm-dark); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; margin-left: 10px; }
.custom-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
.custom-table th { padding: 12px; color: #7F8C8D; text-transform: uppercase; font-size: 0.75rem; border-bottom: 2px solid var(--border-color); }
.custom-table tr td { background: #FFFFFF; padding: 15px; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
.custom-table tr td:first-child { border-left: 1px solid var(--border-color); border-radius: 8px 0 0 8px; }
.custom-table tr td:last-child { border-right: 1px solid var(--border-color); border-radius: 0 8px 8px 0; }
.status-pill { padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; font-weight: bold; text-transform: capitalize; }
.status-pill.activo { background: #E8F5E9; color: #2E7D32; }
.status-pill.inactivo { background: #FFEBEE; color: #C62828; }
.btn-icon { background: none; border: none; font-size: 1.1rem; cursor: pointer; margin: 0 5px; transition: transform 0.2s; }
.btn-icon:hover { transform: scale(1.2); }
.btn-excel-top { background-color: var(--excel-green); color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 4px rgba(39, 174, 96, 0.3); }
</style>