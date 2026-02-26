<template>
  <div class="login-wrapper">
    <div class="login-container">
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
        // Laravel Sanctum
        await axios.get('/sanctum/csrf-cookie');
        const respuesta = await axios.post('/api/login', formulario.value);
        
        // 2. USO DEL ALMACENAMIENTO CIFRADO
        // Guardamos el objeto completo o solo el token, pero cifrado
        storage.set('auth', {
            isLoggedIn: true,
            token: respuesta.data.token, // Si tu API devuelve un token
            updated_at: new Date().getTime()
        });

        router.push('/');

    } catch (error) {
        animarError.value = true;
        setTimeout(() => { animarError.value = false }, 500);
        
        // Manejo de errores...
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
   Fondo y Contenedor Principal
   ========================================= */
.login-wrapper {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f1f5f9;
  /* Patrón sutil de fondo para que no se vea vacío en PC */
  background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
  background-size: 20px 20px;
  padding: 20px; /* Para que en celular no pegue a los bordes */
}

.login-container {
  width: 100%;
  max-width: 420px;
}

.login-card {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.08);
  border-top: 6px solid #D1101A; /* Rojo UGM */
  transition: transform 0.3s ease;
}

/* =========================================
   Cabecera del Login
   ========================================= */
.login-header {
  text-align: center;
  margin-bottom: 35px;
}

.logo-circle {
  width: 80px;
  height: 80px;
  background-color: #fff1f2;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto 15px auto;
}

.ugm-red {
  color: #D1101A;
  font-size: 2rem;
  font-weight: 900;
  margin: 0;
  letter-spacing: -1px;
}

.login-header h2 { margin: 0; color: #1e293b; font-size: 1.4rem; font-weight: 700;}
.login-header p { color: #64748b; font-size: 0.9rem; margin-top: 5px; font-weight: 500;}

/* =========================================
   Formulario e Inputs
   ========================================= */
.form-group { margin-bottom: 22px; }
label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;}

/* Envoltorio para los iconos dentro del input */
.input-icon-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 15px;
  color: #94a3b8;
  font-size: 1.1rem;
}

input { 
  width: 100%; 
  padding: 14px 15px 14px 45px; /* Espacio extra a la izquierda para el icono */
  border: 1px solid #e2e8f0; 
  border-radius: 8px; 
  box-sizing: border-box; 
  font-size: 1rem;
  background-color: #f8fafc;
  transition: all 0.2s;
}

input:focus { 
  outline: none; 
  border-color: #D1101A; 
  background-color: #ffffff;
  box-shadow: 0 0 0 3px rgba(209, 16, 26, 0.1);
}

.input-error {
  border-color: #ef4444 !important;
  background-color: #fef2f2 !important;
}

/* Botón del Ojo */
.btn-eye {
  position: absolute;
  right: 15px;
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  font-size: 1.1rem;
  padding: 0;
  transition: color 0.2s;
}

.btn-eye:hover { color: #475569; }

/* =========================================
   Botón Principal y Alertas
   ========================================= */
.btn-login {
  background-color: #D1101A;
  color: white;
  padding: 14px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  width: 100%;
  font-size: 1.05rem;
  transition: all 0.3s;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-top: 10px;
}

.btn-login:hover:not(:disabled) { 
  background-color: #b00d15; 
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(209, 16, 26, 0.2);
}

.btn-login:disabled { 
  background-color: #e2e8f0; 
  cursor: not-allowed; 
  color: #94a3b8;
}

.alerta-error {
  background-color: #fef2f2;
  color: #ef4444;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 0.9rem;
  text-align: center;
  border: 1px solid #fecaca;
  font-weight: 500;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.login-footer {
  text-align: center;
  margin-top: 30px;
  border-top: 1px solid #f1f5f9;
  padding-top: 20px;
}

.login-footer p {
  color: #94a3b8;
  font-size: 0.8rem;
  margin: 0;
}

/* =========================================
   Animación de Error (Shake)
   ========================================= */
.shake-animation {
  animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes shake {
  10%, 90% { transform: translate3d(-2px, 0, 0); }
  20%, 80% { transform: translate3d(4px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-6px, 0, 0); }
  40%, 60% { transform: translate3d(6px, 0, 0); }
}

/* =========================================
   Responsivo
   ========================================= */
@media (max-width: 480px) {
  .login-card {
    padding: 30px 20px;
  }
}
</style>