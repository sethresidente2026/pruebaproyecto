<template>
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6" style="color: var(--gris-oscuro);"> Gestión de Grupos</h1>

        <div class="formulario-card" :class="{'modo-edicion': editandoId}">
            <h2>{{ editandoId ? ' Editando Grupo' : 'Crear Nuevo Grupo' }}</h2>
            
            <form @submit.prevent="guardarGrupo">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre del Grupo:</label>
                        <input type="text" v-model="nuevoGrupo.nombre" placeholder="Ej. Selectivo Varonil" :class="{'input-error': errores.nombre}">
                        <span class="text-error" v-if="errores.nombre">{{ errores.nombre[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Cupo Máximo:</label>
                        <input type="number" v-model="nuevoGrupo.cupo_maximo" placeholder="Ej. 20" :class="{'input-error': errores.cupo_maximo}">
                        <span class="text-error" v-if="errores.cupo_maximo">{{ errores.cupo_maximo[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Actividad:</label>
                        <select v-model="nuevoGrupo.actividad_id" :class="{'input-error': errores.actividad_id}">
                            <option value="" disabled>Seleccione una actividad...</option>
                            <option v-for="act in actividades" :key="act.id" :value="act.id">{{ act.nombre }}</option>
                        </select>
                        <span class="text-error" v-if="errores.actividad_id">{{ errores.actividad_id[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Docente Asignado:</label>
                        <select v-model="nuevoGrupo.docente_id" :class="{'input-error': errores.docente_id}">
                            <option value="" disabled>Seleccione un docente...</option>
                            <option v-for="doc in docentes" :key="doc.id" :value="doc.id">{{ doc.nombre }} {{ doc.apellidos }}</option>
                        </select>
                        <span class="text-error" v-if="errores.docente_id">{{ errores.docente_id[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Ciclo Escolar:</label>
                        <select v-model="nuevoGrupo.ciclo_id" :class="{'input-error': errores.ciclo_id}">
                            <option value="" disabled>Seleccione un ciclo...</option>
                            <option v-for="ciclo in ciclos" :key="ciclo.id" :value="ciclo.id">{{ ciclo.nombre }}</option>
                        </select>
                        <span class="text-error" v-if="errores.ciclo_id">{{ errores.ciclo_id[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Nivel:</label>
                        <select v-model="nuevoGrupo.nivel_id" :class="{'input-error': errores.nivel_id}">
                            <option value="" disabled>Seleccione un nivel...</option>
                            <option v-for="nivel in niveles" :key="nivel.id" :value="nivel.id">{{ nivel.nombre }}</option>
                        </select>
                        <span class="text-error" v-if="errores.nivel_id">{{ errores.nivel_id[0] }}</span>
                    </div>
                </div>

                <div class="botones-form mt-4">
                    <button type="submit" class="btn-guardar" :disabled="enviando">
                        {{ enviando ? 'Guardando...' : (editandoId ? ' Actualizar' : ' Guardar Grupo') }}
                    </button>
                    <button type="button" class="btn-cancelar" v-if="editandoId" @click="cancelarEdicion"> Cancelar</button>
                </div>
            </form>
        </div>

        <div class="tabla-container">
            <table v-if="grupos.length > 0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Grupo</th>
                        <th>Cupo</th>
                        <th>Actividad</th>
                        <th>Docente</th>
                        <th>Ciclo / Nivel</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="grupo in grupos" :key="grupo.id">
                        <td>{{ grupo.id }}</td>
                        <td><strong>{{ grupo.nombre }}</strong></td>
                        <td>{{ grupo.cupo_maximo }}</td>
                        <td>{{ grupo.actividad?.nombre }}</td>
                        <td>{{ grupo.docente?.nombre }} {{ grupo.docente?.apellidos }}</td>
                        <td>{{ grupo.ciclo?.nombre }} <br> <small class="text-gray-500">{{ grupo.nivel?.nombre }}</small></td>
                        <td>
                            <button @click="eliminarGrupo(grupo.id)" class="btn-eliminar">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else class="sin-datos">No hay grupos registrados...</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Variables
const grupos = ref([]);
const actividades = ref([]);
const docentes = ref([]);
const ciclos = ref([]);
const niveles = ref([]);

const nuevoGrupo = ref({ nombre: '', cupo_maximo: '', actividad_id: '', docente_id: '', ciclo_id: '', nivel_id: '' });
const errores = ref({});
const enviando = ref(false);
const editandoId = ref(null);

// Cargar TODO al inicio
const inicializarDatos = async () => {
    try {
        // Obtenemos los grupos y todos los catálogos al mismo tiempo
        const [resGrupos, resAct, resDoc, resCiclos, resNiveles] = await Promise.all([
            axios.get('/api/grupos'),
            axios.get('/api/actividades'),
            axios.get('/api/docentes'),
            axios.get('/api/ciclos'),
            axios.get('/api/niveles')
        ]);
        
        grupos.value = resGrupos.data;
        actividades.value = resAct.data;
        docentes.value = resDoc.data;
        ciclos.value = resCiclos.data;
        niveles.value = resNiveles.data;
    } catch (error) { console.error("Error al cargar datos:", error); }
};

const guardarGrupo = async () => {
    errores.value = {};
    enviando.value = true;
    try {
        if (editandoId.value) {
            // Lógica de update si la deseas implementar luego
        } else {
            // Guardamos
            await axios.post('/api/grupos', nuevoGrupo.value);
            // Recargamos la lista completa para que traiga los nombres de las relaciones (docente, actividad, etc.)
            const resGrupos = await axios.get('/api/grupos');
            grupos.value = resGrupos.data;
        }
        cancelarEdicion();
    } catch (error) {
        if (error.response && error.response.status === 422) errores.value = error.response.data.errors;
    } finally { enviando.value = false; }
};

const cancelarEdicion = () => {
    nuevoGrupo.value = { nombre: '', cupo_maximo: '', actividad_id: '', docente_id: '', ciclo_id: '', nivel_id: '' };
    editandoId.value = null;
    errores.value = {};
};

const eliminarGrupo = async (id) => {
    if (!confirm('¿Seguro que deseas eliminar este grupo?')) return; 
    try {
        await axios.delete(`/api/grupos/${id}`);
        grupos.value = grupos.value.filter(g => g.id !== id);
    } catch (error) {
        alert(" Error al eliminar. Revisa si tiene horarios asignados.");
    }
};

onMounted(inicializarDatos);
</script>

<style scoped>
/* Pegamos exactamente los mismos estilos de Docentes.vue aquí abajo para mantener la identidad UGM */
.formulario-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 4px solid var(--gris-oscuro); transition: all 0.3s ease;}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.form-group { margin-bottom: 5px; }
label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.9rem;}
input, select { width: 100%; padding: 10px; border: 1px solid #dcdde1; border-radius: 4px; box-sizing: border-box; background-color: #fcfcfc;}
input:focus, select:focus { outline: none; border-color: var(--rojo-ugm); box-shadow: 0 0 0 2px rgba(209, 16, 26, 0.1); }
.input-error { border-color: #e74c3c; background-color: #fadbd8; }
.text-error { color: #e74c3c; font-size: 0.8rem; margin-top: 4px; display: block; }
.botones-form { display: flex; gap: 10px; margin-top: 20px;}
.btn-guardar { background-color: var(--rojo-ugm); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; flex-grow: 1; transition: background 0.3s; }
.btn-guardar:hover:not(:disabled) { background-color: #b00d15; }
.btn-cancelar { background-color: #95a5a6; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
.tabla-container { overflow-x: auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
table { width: 100%; border-collapse: collapse; background: white; }
th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
th { background-color: var(--gris-oscuro); font-weight: 600; color: white; }
tr:hover { background-color: #f9f9fa; }
.btn-eliminar { background-color: #e74c3c; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; font-weight: 600; font-size: 0.85rem;}
.sin-datos { text-align: center; color: #777; margin-top: 20px; padding: 20px; background: white; border-radius: 8px;}
@media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
</style>