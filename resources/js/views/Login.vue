<template>
  <div class="login-wrapper">
    <div class="split-container">
      
      <div class="form-section">
        <div class="login-card" :class="{ 'shake-animation': animarError }">
          <div class="login-header">
            <div class="logo-circle">
              <h1 class="ugm-red">UGM</h1>
            </div>
            <h2>Acceso al Sistema</h2>
            <p>Gestión Académica de Espacios y Horarios</p>
          </div>

          <form @submit.prevent="iniciarSesion">
            <div class="form-group">
              <label>Correo Electrónico</label>
              <div class="input-icon-wrapper">
                <i class="fa-solid fa-envelope input-icon"></i>
                <input 
                  type="email" 
                  v-model="formulario.email" 
                  placeholder="admin@ugm.edu.mx" 
                  required
                  :class="{'input-error': mensajeError}"
                >
              </div>
            </div>

            <div class="form-group">
              <label>Contraseña</label>
              <div class="input-icon-wrapper">
                <i class="fa-solid fa-lock input-icon"></i>
                <input 
                  :type="mostrarPassword ? 'text' : 'password'" 
                  v-model="formulario.password" 
                  placeholder="••••••••" 
                  required
                  :class="{'input-error': mensajeError}"
                >
                <button 
                  type="button" 
                  class="btn-eye" 
                  @click="mostrarPassword = !mostrarPassword"
                  tabindex="-1"
                >
                  <i :class="mostrarPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                </button>
              </div>
            </div>

            <div v-if="mensajeError" class="alerta-error">
              <i class="fa-solid fa-circle-exclamation"></i> {{ mensajeError }}
            </div>

            <button type="submit" class="btn-login" :disabled="cargando">
              <i :class="cargando ? 'fa-solid fa-circle-notch fa-spin' : 'fa-solid fa-right-to-bracket'"></i>
              {{ cargando ? ' Autenticando...' : ' Iniciar Sesión' }}
            </button>
          </form>
        </div>
      </div>

      <div class="image-section">
        <div class="overlay-content">
          <div class="badge">Rectoría Centro</div>
          <h2>Universidad del Golfo de México</h2>
          <p>Sistema de Gestión de Actividades Culturales y Deportivas</p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { storage } from '../../utils/storage';

const router = useRouter();
const formulario = ref({ email: '', password: '' });
const mensajeError = ref('');
const cargando = ref(false);
const mostrarPassword = ref(false);
const animarError = ref(false);     

const iniciarSesion = async () => {
    mensajeError.value = '';
    cargando.value = true;
    animarError.value = false;

    try {
        await axios.get('/sanctum/csrf-cookie');
        const respuesta = await axios.post('/api/login', formulario.value);
        
        storage.set('auth', {
            isLoggedIn: true,
            token: respuesta.data.token,
            updated_at: new Date().getTime()
        });

        router.push('/');

    } catch (error) {
        animarError.value = true;
        setTimeout(() => { animarError.value = false }, 500);
        
        if (error.response && error.response.status === 401) {
            mensajeError.value = "Credenciales incorrectas.";
        } else {
            mensajeError.value = "Error de conexión. Intente más tarde.";
        }
    } finally {
        cargando.value = false;
    }
};
</script>

<style scoped>
/* =========================================
   LAYOUT PRINCIPAL (SPLIT SCREEN)
   ========================================= */
.login-wrapper {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f1f5f9;
  padding: 20px;
}

.split-container {
  display: flex;
  width: 100%;
  max-width: 1100px;
  min-height: 650px;
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
}

/* Columna del Formulario */
.form-section {
  flex: 1.2; /* Un poco más ancho que la imagen para el formulario */
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

/* Columna de la Imagen */
.image-section {
  flex: 1;
  background-image: linear-gradient(rgba(209, 16, 26, 0.2), rgba(209, 16, 26, 0.2)), 
                    url('https://unsplash.com/es/fotos/manzana-roja-en-cuatro-libros-de-pila-OyCl7Y4y0Bk');
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: flex-end;
  padding: 60px 40px;
  position: relative;
}

.overlay-content {
  color: white;
  z-index: 2;
  text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.overlay-content h2 { font-size: 2rem; margin-bottom: 10px; }
.badge {
  background: #D1101A;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  display: inline-block;
  margin-bottom: 15px;
  text-transform: uppercase;
  font-weight: bold;
}

/* =========================================
   ELEMENTOS DEL LOGIN (TU CÓDIGO ORIGINAL)
   ========================================= */
.login-card { width: 100%; max-width: 400px; }

.login-header { text-align: center; margin-bottom: 35px; }

.logo-circle {
  width: 80px; height: 80px; background-color: #fff1f2;
  border-radius: 50%; display: flex; justify-content: center;
  align-items: center; margin: 0 auto 15px auto;
}

.ugm-red {
  color: #D1101A; font-size: 2rem; font-weight: 900;
  margin: 0; letter-spacing: -1px;
}

.form-group { margin-bottom: 22px; }
label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;}

.input-icon-wrapper { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 15px; color: #94a3b8; font-size: 1.1rem; }

input { 
  width: 100%; padding: 14px 15px 14px 45px; border: 1px solid #e2e8f0; 
  border-radius: 8px; font-size: 1rem; background-color: #f8fafc; transition: all 0.2s;
}

input:focus { 
  outline: none; border-color: #D1101A; background-color: #ffffff;
  box-shadow: 0 0 0 3px rgba(209, 16, 26, 0.1);
}

.btn-eye { position: absolute; right: 15px; background: none; border: none; color: #94a3b8; cursor: pointer; }

.btn-login {
  background-color: #D1101A; color: white; padding: 14px; border: none;
  border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%;
  font-size: 1.05rem; transition: all 0.3s; display: flex;
  justify-content: center; align-items: center; gap: 10px; margin-top: 10px;
}

.btn-login:hover:not(:disabled) { 
  background-color: #b00d15; transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(209, 16, 26, 0.2);
}

.alerta-error {
  background-color: #fef2f2; color: #ef4444; padding: 12px;
  border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;
  border: 1px solid #fecaca; display: flex; align-items: center; justify-content: center; gap: 8px;
}

/* ANIMACIONES */
.shake-animation { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
@keyframes shake {
  10%, 90% { transform: translate3d(-2px, 0, 0); }
  20%, 80% { transform: translate3d(4px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-6px, 0, 0); }
  40%, 60% { transform: translate3d(6px, 0, 0); }
}

/* RESPONSIVO */
@media (max-width: 900px) {
  .image-section { display: none; }
  .split-container { max-width: 450px; }
}
</style>