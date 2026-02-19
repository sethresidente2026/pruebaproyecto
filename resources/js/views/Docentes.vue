<template>
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6" style="color: var(--gris-oscuro);"> Gestión de Docentes</h1>

        <div class="formulario-card" :class="{'modo-edicion': editandoId}">
            <h2>{{ editandoId ? ' Editando Docente' : ' Agregar Nuevo Docente' }}</h2>
            
            <form @submit.prevent="guardarDocente">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre(s):</label>
                        <input type="text" v-model="nuevoDocente.nombre" :class="{'input-error': errores.nombre}">
                        <span class="text-error" v-if="errores.nombre">{{ errores.nombre[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Apellidos:</label>
                        <input type="text" v-model="nuevoDocente.apellidos" :class="{'input-error': errores.apellidos}">
                        <span class="text-error" v-if="errores.apellidos">{{ errores.apellidos[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Correo Electrónico:</label>
                        <input type="email" v-model="nuevoDocente.email" :class="{'input-error': errores.email}">
                        <span class="text-error" v-if="errores.email">{{ errores.email[0] }}</span>
                    </div>

                    <div class="form-group">
                        <label>Estatus:</label>
                        <select v-model="nuevoDocente.estatus" :class="{'input-error': errores.estatus}">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                        <span class="text-error" v-if="errores.estatus">{{ errores.estatus[0] }}</span>
                    </div>
                </div>

                <div class="botones-form mt-4">
                    <button type="submit" class="btn-guardar" :disabled="enviando">
                        {{ enviando ? 'Guardando...' : (editandoId ? ' Actualizar' : ' Guardar') }}
                    </button>
                    <button type="button" class="btn-cancelar" v-if="editandoId" @click="cancelarEdicion"> Cancelar</button>
                </div>
            </form>
        </div>

        <div class="tabla-container">
            <table v-if="docentes.length > 0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="docente in docentes" :key="docente.id">
                        <td>{{ docente.id }}</td>
                        <td>{{ docente.nombre }} {{ docente.apellidos }}</td>
                        <td>{{ docente.email }}</td>
                        <td>
                            <span :class="docente.estatus === 'Activo' ? 'badge-activo' : 'badge-inactivo'">
                                {{ docente.estatus }}
                            </span>
                        </td>
                        <td>
                            <button @click="cargarParaEditar(docente)" class="btn-editar"> Editar</button>
                            <button @click="eliminarDocente(docente.id)" class="btn-eliminar"> Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else class="sin-datos">No hay docentes registrados...</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const docentes = ref([]);
const nuevoDocente = ref({ nombre: '', apellidos: '', email: '', estatus: 'Activo' });
const errores = ref({});
const enviando = ref(false);
const editandoId = ref(null);

const obtenerDocentes = async () => {
    try {
        const respuesta = await axios.get('/api/docentes');
        docentes.value = respuesta.data;
    } catch (error) { console.error(error); }
};

const guardarDocente = async () => {
    errores.value = {};
    enviando.value = true;
    try {
        if (editandoId.value) {
            await axios.put(`/api/docentes/${editandoId.value}`, nuevoDocente.value);
        } else {
            await axios.post('/api/docentes', nuevoDocente.value);
        }
        
        
        await obtenerDocentes();
        
        cancelarEdicion();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errores.value = error.response.data.errors;
        } else {
            alert("Ocurrió un error en el servidor.");
        }
    } finally { 
        enviando.value = false; 
    }
};

const cargarParaEditar = (docente) => {
    nuevoDocente.value = { ...docente };
    editandoId.value = docente.id;
    errores.value = {};
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelarEdicion = () => {
    nuevoDocente.value = { nombre: '', apellidos: '', email: '', estatus: 'Activo' };
    editandoId.value = null;
    errores.value = {};
};

const eliminarDocente = async (id) => {
    if (!confirm('¿Seguro que deseas eliminar este docente?')) return; 
    try {
        await axios.delete(`/api/docentes/${id}`);
        docentes.value = docentes.value.filter(d => d.id !== id);
    } catch (error) {
        if (error.response && error.response.status === 500) {
            alert(" No puedes eliminar este docente porque ya tiene GRUPOS asignados.");
        } else {
            alert(" Ocurrió un error.");
        }
    }
};

onMounted(obtenerDocentes);
</script>

<style scoped>
/* Reciclamos los estilos de Espacios con ajustes para el grid */
.formulario-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 4px solid var(--gris-oscuro); transition: all 0.3s ease;}
.modo-edicion { border-top-color: #f1c40f; box-shadow: 0 0 15px rgba(241, 196, 15, 0.2); }
.formulario-card h2 { margin-top: 0; font-size: 1.3rem; color: var(--gris-oscuro); margin-bottom: 20px; }
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
.btn-editar { background-color: #f1c40f; border: none; padding: 6px 12px; cursor: pointer; margin-right: 8px; border-radius: 4px; color: #333; font-weight: 600; font-size: 0.85rem;}
.btn-eliminar { background-color: #e74c3c; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; font-weight: 600; font-size: 0.85rem;}
.sin-datos { text-align: center; color: #777; margin-top: 20px; padding: 20px; background: white; border-radius: 8px;}
.badge-activo { background-color: #e3fcef; color: #27ae60; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; }
.badge-inactivo { background-color: #fadbd8; color: #c0392b; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; }
@media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
</style>