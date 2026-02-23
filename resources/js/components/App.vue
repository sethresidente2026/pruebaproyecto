<template>
    <div class="app-layout">
        
        <div class="mobile-header" v-if="$route.name !== 'login'">
            <div class="logo-text"><span class="text-ugm-red">UGM</span> CENTRO</div>
            <button class="btn-menu" @click="menuAbierto = !menuAbierto">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <div class="sidebar-overlay" v-if="menuAbierto" @click="menuAbierto = false"></div>

        <aside class="sidebar" :class="{ 'sidebar-open': menuAbierto }" v-if="$route.name !== 'login'">
            <div class="sidebar-header">
                <div class="logo-text"><span class="text-ugm-red">UGM</span> CENTRO</div>
                <div class="subtitle">Gestión Académica</div>
            </div>

            <nav class="sidebar-nav" @click="menuAbierto = false">
                <router-link to="/" class="nav-item">
                    <i class="fa-solid fa-house"></i> <span>Inicio</span>
                </router-link>
                
                <div class="nav-section-title">Catálogos</div>
                
                <router-link to="/actividades" class="nav-item">
                    <i class="fa-solid fa-palette"></i> <span>Actividades</span>
                </router-link>
                
                <router-link to="/espacios" class="nav-item">
                    <i class="fa-solid fa-building"></i> <span>Espacios</span>
                </router-link>
                
                <router-link to="/docentes" class="nav-item">
                    <i class="fa-solid fa-user-tie"></i> <span>Docentes</span>
                </router-link>

                <div class="nav-section-title">Operación</div>

                <router-link to="/grupos" class="nav-item">
                    <i class="fa-solid fa-users"></i> <span>Grupos</span>
                </router-link>
                
                <router-link to="/horarios" class="nav-item">
                    <i class="fa-solid fa-calendar-days"></i> <span>Horarios</span>
                </router-link>
                
                <router-link to="/asistencias" class="nav-item">
                    <i class="fa-solid fa-clipboard-user"></i> <span>Asistencias</span>
                </router-link>
            </nav>

            <div class="sidebar-footer">
                <button @click="cerrarSesion" class="btn-logout-sidebar">
                    <i class="fa-solid fa-right-from-bracket"></i> 
                    <span>Cerrar Sesión</span>
                </button>
                
                <button class="btn-close-menu" @click="menuAbierto = false">
                    <i class="fa-solid fa-xmark"></i> Cerrar Menú
                </button>
            </div>
        </aside>

        <main class="main-content" :class="{ 'full-width': $route.name === 'login' }">
            <div class="top-bar" v-if="$route.name !== 'login'">
                <div class="breadcrumb">Panel de Administración / {{ $route.name }}</div>
                <div class="user-info">
                    <i class="fa-solid fa-circle-user"></i> Coordinador
                </div>
            </div>
            
            <div class="view-container">
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const menuAbierto = ref(false); // 🔴 Estado para controlar el menú en móviles

const cerrarSesion = async () => {
    try {
        await axios.post('/logout'); 
    } catch (e) { console.error("Error al cerrar sesión", e); }
    
    localStorage.removeItem('auth');
    router.push('/login');
};
</script>

<style>
:root {
  --sidebar-bg: #1e293b; 
  --sidebar-hover: #334155;
  --ugm-red: #D1101A;
  --text-light: #f8fafc;
  --text-muted: #94a3b8;
  --bg-main: #f1f5f9;
}

body { margin: 0; font-family: 'Inter', sans-serif; background-color: var(--bg-main); }

/* Layout Base */
.app-layout {
    display: flex;
    min-height: 100vh;
}

/* =========================================
   SIDEBAR
   ========================================= */
.sidebar {
    width: 260px;
    background-color: var(--sidebar-bg);
    color: var(--text-light);
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100vh;
    box-shadow: 4px 0 10px rgba(0,0,0,0.1);
    z-index: 1000; /* Z-index alto para sobreponerse en móvil */
    transition: transform 0.3s ease-in-out;
}

.sidebar-header {
    padding: 30px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.logo-text { font-size: 1.4rem; font-weight: 800; letter-spacing: 1px; }
.text-ugm-red { color: #ff4d4d; }
.subtitle { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; margin-top: 5px; }

.sidebar-nav {
    flex-grow: 1;
    padding: 20px 0;
    overflow-y: auto;
}

.nav-section-title {
    padding: 15px 25px 5px;
    font-size: 0.65rem;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 700;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 12px 25px;
    color: var(--text-muted);
    text-decoration: none;
    transition: all 0.2s ease;
    gap: 12px;
}

.nav-item i { width: 20px; font-size: 1.1rem; }

.nav-item:hover {
    background-color: var(--sidebar-hover);
    color: white;
}

.router-link-active {
    background-color: rgba(209, 16, 26, 0.15);
    color: white;
    border-left: 4px solid var(--ugm-red);
}

.sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(255,255,255,0.05);
}

.btn-logout-sidebar {
    width: 100%;
    background: transparent;
    border: 1px solid #ef4444;
    color: #ef4444;
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-weight: 600;
}

.btn-logout-sidebar:hover { background: #ef4444; color: white; }

/* =========================================
   CONTENIDO PRINCIPAL
   ========================================= */
.main-content {
    flex-grow: 1;
    margin-left: 260px; 
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    transition: margin-left 0.3s ease;
}

.main-content.full-width { margin-left: 0; }

.top-bar {
    height: 60px;
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 30px;
    border-bottom: 1px solid #e2e8f0;
}

.breadcrumb { font-size: 0.85rem; color: var(--text-muted); }
.user-info { font-weight: 600; display: flex; align-items: center; gap: 8px; color: var(--ugm-dark); }

.view-container {
    padding: 30px;
    flex-grow: 1;
}

/* Transiciones de Vistas */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(10px); 
}

/* =========================================
   DISEÑO RESPONSIVO (MÓVILES)
   ========================================= */
.mobile-header { display: none; }
.btn-close-menu { display: none; }
.sidebar-overlay { display: none; }

@media (max-width: 768px) {
    .top-bar { display: none; } /* Ocultamos la barra superior de PC */
    
    .mobile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: var(--sidebar-bg);
        color: white;
        padding: 15px 20px;
        position: sticky;
        top: 0;
        z-index: 900;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .btn-menu {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
    }

    /* Escondemos la Sidebar fuera de la pantalla por defecto */
    .sidebar {
        transform: translateX(-100%);
    }

    /* Clase activa para deslizar la Sidebar hacia adentro */
    .sidebar.sidebar-open {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0; /* El contenido usa todo el ancho en móvil */
    }

    /* Velo oscuro de fondo */
    .sidebar-overlay {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        backdrop-filter: blur(2px);
    }

    /* Mostramos el botón de cerrar en el menú */
    .btn-close-menu {
        display: block;
        width: 100%;
        padding: 15px;
        margin-top: 15px;
        background: transparent;
        color: var(--text-muted);
        border: none;
        border-top: 1px solid rgba(255,255,255,0.05);
        cursor: pointer;
        text-align: center;
        font-weight: bold;
        transition: color 0.2s;
    }
    .btn-close-menu:hover {
        color: white;
    }
    
    .view-container {
        padding: 15px; /* Menos espacio en blanco en celulares */
    }
}
</style>