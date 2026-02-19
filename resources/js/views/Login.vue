<template>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="ugm-red">UGM</h1>
                <h2>Acceso al Sistema</h2>
                <p>Ingresa tus credenciales para continuar</p>
            </div>

            <form @submit.prevent="iniciarSesion">
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input 
                        type="email" 
                        v-model="formulario.email" 
                        placeholder="admin@ugm.edu.mx" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <input 
                        type="password" 
                        v-model="formulario.password" 
                        placeholder="••••••••" 
                        required
                    >
                </div>

                <div v-if="mensajeError" class="alerta-error">
                    {{ mensajeError }}
                </div>

                <button type="submit" class="btn-login" :disabled="cargando">
                    {{ cargando ? 'Verificando...' : 'Iniciar Sesión' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const formulario = ref({ email: '', password: '' });
const mensajeError = ref('');
const cargando = ref(false);

const iniciarSesion = async () => {
    mensajeError.value = '';
    cargando.value = true;

    try {
        // 1. EL TOQUE DE PUERTA: Pedimos el token CSRF a Laravel Sanctum
        // Esto es obligatorio antes de hacer el POST del login
       await axios.get('/sanctum/csrf-cookie')

        // 2. ENVIAR CREDENCIALES: Ahora sí enviamos el correo y la contraseña
       const respuesta = await axios.post('/api/login', formulario.value);
        
        // 3. GUARDAR EL PASE: Le decimos a Vue que ya estamos logueados
        localStorage.setItem('auth', 'true');
        
        // 4. ENTRAR AL SISTEMA: Redirigimos al Dashboard
        router.push('/');

    } catch (error) {
        // Manejo de errores (Esto lo tenías perfecto)
        if (error.response && error.response.status === 401) {
            mensajeError.value = "Credenciales incorrectas. Intenta de nuevo.";
        } else {
            mensajeError.value = "Error al conectar con el servidor.";
        }
    } finally {
        cargando.value = false;
    }
};
</script>

<style scoped>
/* Fondo que cubre toda la pantalla */
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
}

.login-card {
    background: white;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
    border-top: 5px solid #D1101A; /* Rojo UGM */
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.ugm-red {
    color: #D1101A;
    font-size: 2.5rem;
    font-weight: 900;
    margin: 0;
    letter-spacing: -1px;
}

.login-header h2 { margin: 5px 0; color: #2C3E50; font-size: 1.2rem;}
.login-header p { color: #7f8c8d; font-size: 0.9rem; margin-top: 0;}

.form-group { margin-bottom: 20px; }
label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 0.9rem;}
input { width: 100%; padding: 12px; border: 1px solid #dcdde1; border-radius: 4px; box-sizing: border-box; font-size: 1rem;}
input:focus { outline: none; border-color: #D1101A; }

.btn-login {
    background-color: #D1101A;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    width: 100%;
    font-size: 1rem;
    transition: background 0.3s;
}

.btn-login:hover:not(:disabled) { background-color: #b00d15; }
.btn-login:disabled { background-color: #e0e0e0; cursor: not-allowed; color: #888;}

.alerta-error {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
    font-size: 0.85rem;
    text-align: center;
}
</style>