<template>
    <div class="app-container">
        <header class="navbar">
            <div class="logo-container">
                <div class="logo-text">
                    <span class="ugm-red">UGM</span> CENTRO
                </div>
                <div class="subtitle">Sistema de Gestión Académica</div>
            </div>
            <nav class="links">
                <router-link to="/" class="nav-item">Inicio</router-link>
                <router-link to="/espacios" class="nav-item">Espacios</router-link>
                <router-link to="/docentes" class="nav-item">Docentes</router-link>
                <router-link to="/grupos" class="nav-item">Grupos</router-link>
            </nav>
        </header>

        <main class="content">
            <router-view></router-view>
        </main>
    </div>
</template>

<style>
/* Reset básico */
body { 
    margin: 0; 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    background-color: #f4f6f9; 
    color: #333;
}

/* Variables institucionales */
:root {
    --rojo-ugm: #D1101A; /* Rojo extraído del logo */
    --gris-oscuro: #2C3E50;
    --gris-claro: #ECF0F1;
}

/* Cabecera / Navbar */
.navbar {
    background-color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 4px solid var(--rojo-ugm);
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* Estilos del Logo Texto */
.logo-container {
    display: flex;
    flex-direction: column;
}

.logo-text { 
    font-weight: 900; 
    font-size: 1.8rem; 
    letter-spacing: -0.5px;
    color: var(--gris-oscuro);
}

.ugm-red {
    color: var(--rojo-ugm);
}

.subtitle {
    font-size: 0.75rem;
    color: #7f8c8d;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: bold;
}

/* Enlaces de navegación */
.links {
    display: flex;
    gap: 10px;
}

.nav-item {
    color: var(--gris-oscuro);
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.nav-item:hover {
    background-color: #fce4e4;
    color: var(--rojo-ugm);
}

/* Enlace activo */
.router-link-active {
    background-color: var(--rojo-ugm);
    color: white !important;
}

/* Contenedor de las vistas */
.content { 
    padding: 40px; 
    max-width: 1200px;
    margin: 0 auto;
}
</style>