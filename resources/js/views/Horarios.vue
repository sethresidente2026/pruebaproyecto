<template>
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6" style="color: var(--gris-oscuro);"> Asignación de Horarios</h1>

        <div v-if="errorEmpalme" class="alerta-empalme">
            <strong>Conflicto Detectado:</strong> {{ errorEmpalme }}
        </div>

        <div class="formulario-card">
            <h2> Asignar Nuevo Horario</h2>
            
            <form @submit.prevent="guardarHorario">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Grupo a Asignar:</label>
                        <select v-model="nuevoHorario.grupo_id" :class="{'input-error': errores.grupo_id}">
                            <option value="" disabled>Seleccione un grupo...</option>
                            <option v-for="grupo in grupos" :key="grupo.id" :value="grupo.id">
                                {{ grupo.nombre }} (Docente: {{ grupo.docente?.nombre }})
                            </option>
                        </select>
                        <span class="text-error" v-if="errores.grupo_id">{{ errores.grupo_id[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Espacio (Salón/Cancha):</label>
                        <select v-model="nuevoHorario.espacio_id" :class="{'input-error': errores.espacio_id}">
                            <option value="" disabled>Seleccione un espacio...</option>
                            <option v-for="espacio in espacios" :key="espacio.id" :value="espacio.id">
                                {{ espacio.nombre }} (Capacidad: {{ espacio.capacidad }})
                            </option>
                        </select>
                        <span class="text-error" v-if="errores.espacio_id">{{ errores.espacio_id[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Día de la Semana:</label>
                        <select v-model="nuevoHorario.dia_semana" :class="{'input-error': errores.dia_semana}">
                            <option value="" disabled>Seleccione el día...</option>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sábado">Sábado</option>
                        </select>
                        <span class="text-error" v-if="errores.dia_semana">{{ errores.dia_semana[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Hora de Inicio:</label>
                        <input type="time" v-model="nuevoHorario.hora_inicio" :class="{'input-error': errores.hora_inicio}">
                        <span class="text-error" v-if="errores.hora_inicio">{{ errores.hora_inicio[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Hora de Fin:</label>
                        <input type="time" v-model="nuevoHorario.hora_fin" :class="{'input-error': errores.hora_fin}">
                        <span class="text-error" v-if="errores.hora_fin">{{ errores.hora_fin[0] }}</span>
                    </div>
                </div>

                <div class="botones-form mt-4">
                    <button type="submit" class="btn-guardar" :disabled="enviando">
                        {{ enviando ? 'Validando y Guardando...' : ' Confirmar Horario' }}
                    </button>
                </div>
            </form>
        </div>

        <div class="tabla-container">
            <table v-if="horarios.length > 0">
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Horario</th>
                        <th>Grupo (Actividad)</th>
                        <th>Docente</th>
                        <th>Espacio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="horario in horarios" :key="horario.id">
                        <td><strong>{{ horario.dia_semana }}</strong></td>
                        <td>{{ horario.hora_inicio }} - {{ horario.hora_fin }}</td>
                        <td>{{ horario.grupo?.nombre }} <br><small class="text-gray-500">{{ horario.grupo?.actividad?.nombre }}</small></td>
                        <td>{{ horario.grupo?.docente?.nombre }} {{ horario.grupo?.docente?.apellidos }}</td>
                        <td>{{ horario.espacio?.nombre }}</td>
                        <td>
                            <button @click="eliminarHorario(horario.id)" class="btn-eliminar">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else class="sin-datos">No hay horarios registrados aún...</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Variables
const horarios = ref([]);
const grupos = ref([]);
const espacios = ref([]);

const nuevoHorario = ref({ grupo_id: '', espacio_id: '', dia_semana: '', hora_inicio: '', hora_fin: '' });
const errores = ref({});
const errorEmpalme = ref(''); // Variable especial para guardar el error 409
const enviando = ref(false);

// Cargar catálogos iniciales
const inicializarDatos = async () => {
    try {
        const [resHorarios, resGrupos, resEspacios] = await Promise.all([
            axios.get('/api/horarios'),
            axios.get('/api/grupos'),
            axios.get('/api/espacios')
        ]);
        
        horarios.value = resHorarios.data;
        grupos.value = resGrupos.data;
        espacios.value = resEspacios.data;
    } catch (error) { console.error("Error al cargar datos:", error); }
};

const guardarHorario = async () => {
    errores.value = {};
    errorEmpalme.value = ''; // Limpiamos errores previos
    enviando.value = true;

    try {
        await axios.post('/api/horarios', nuevoHorario.value);
        
        // Si todo sale bien, recargamos la lista
        const resHorarios = await axios.get('/api/horarios');
        horarios.value = resHorarios.data;
        
        // Limpiamos el formulario
        nuevoHorario.value = { grupo_id: '', espacio_id: '', dia_semana: '', hora_inicio: '', hora_fin: '' };
        alert('¡Horario asignado con éxito!');

    } catch (error) {
        if (error.response) {
            // SI FALTAN DATOS (422)
            if (error.response.status === 422) {
                errores.value = error.response.data.errors;
            } 
            // SI HAY EMPALME (409) - ¡Aquí atrapamos tu validación de Laravel!
            else if (error.response.status === 409) {
                errorEmpalme.value = error.response.data.mensaje;
                // Hacemos scroll hacia arriba para que el usuario vea la alerta
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } else {
            alert("Error de conexión.");
        }
    } finally {
        enviando.value = false;
    }
};

const eliminarHorario = async (id) => {
    if (!confirm('¿Seguro que deseas liberar este horario?')) return; 
    try {
        await axios.delete(`/api/horarios/${id}`);
        horarios.value = horarios.value.filter(h => h.id !== id);
    } catch (error) {
        alert(" Error al eliminar el horario.");
    }
};

onMounted(inicializarDatos);
</script>

<style scoped>
/* Estilos UGM + Alerta Especial */
.alerta-empalme { background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px; box-shadow: 0 2px 4px rgba(220,53,69,0.2); animation: shake 0.5s; }
@keyframes shake { 0%, 100% {transform: translateX(0);} 25% {transform: translateX(-5px);} 75% {transform: translateX(5px);} }

.formulario-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 4px solid var(--gris-oscuro); }
.formulario-card h2 { margin-top: 0; font-size: 1.3rem; color: var(--gris-oscuro); margin-bottom: 20px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.form-group { margin-bottom: 5px; }
label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.9rem;}
input, select { width: 100%; padding: 10px; border: 1px solid #dcdde1; border-radius: 4px; box-sizing: border-box; background-color: #fcfcfc;}
input:focus, select:focus { outline: none; border-color: var(--rojo-ugm); box-shadow: 0 0 0 2px rgba(209, 16, 26, 0.1); }
.input-error { border-color: #e74c3c; background-color: #fadbd8; }
.text-error { color: #e74c3c; font-size: 0.8rem; margin-top: 4px; display: block; }
.botones-form { display: flex; margin-top: 20px;}
.btn-guardar { background-color: var(--rojo-ugm); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; transition: background 0.3s; }
.btn-guardar:hover:not(:disabled) { background-color: #b00d15; }
.btn-guardar:disabled { background-color: #95a5a6; cursor: not-allowed; }
.tabla-container { overflow-x: auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
table { width: 100%; border-collapse: collapse; background: white; }
th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
th { background-color: var(--gris-oscuro); font-weight: 600; color: white; }
tr:hover { background-color: #f9f9fa; }
.btn-eliminar { background-color: #e74c3c; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; font-weight: 600; font-size: 0.85rem;}
.sin-datos { text-align: center; color: #777; margin-top: 20px; padding: 20px; background: white; border-radius: 8px;}
@media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
</style>