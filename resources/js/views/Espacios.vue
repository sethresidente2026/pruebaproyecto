<template>
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6">Gestión de Espacios</h1>

        <div class="formulario-card" :class="{'modo-edicion': editandoId}">
            <h2>{{ editandoId ? ' Editando Espacio' : ' Agregar Nuevo Espacio' }}</h2>
            
            <form @submit.prevent="guardarEspacio">
                <div class="form-group">
                    <label>Nombre del Espacio:</label>
                    <input 
                        type="text" 
                        v-model="nuevoEspacio.nombre" 
                        placeholder="Ej. Aula 101"
                        :class="{'input-error': errores.nombre}"
                    >
                    <span class="text-error" v-if="errores.nombre">{{ errores.nombre[0] }}</span>
                </div>

                <div class="form-group">
                    <label>Capacidad (personas):</label>
                    <input 
                        type="number" 
                        v-model="nuevoEspacio.capacidad" 
                        placeholder="Ej. 30"
                        :class="{'input-error': errores.capacidad}"
                    >
                    <span class="text-error" v-if="errores.capacidad">{{ errores.capacidad[0] }}</span>
                </div>

                <div class="botones-form">
                    <button type="submit" class="btn-guardar" :disabled="enviando">
                        {{ enviando ? 'Guardando...' : (editandoId ? ' Actualizar' : ' Guardar') }}
                    </button>
                    <button type="button" class="btn-cancelar" v-if="editandoId" @click="cancelarEdicion">
                        ❌ Cancelar
                    </button>
                </div>
            </form>
        </div>

        <div class="tabla-container">
            <table v-if="espacios.length > 0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Espacio</th>
                        <th>Capacidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="espacio in espacios" :key="espacio.id">
                        <td>{{ espacio.id }}</td>
                        <td>{{ espacio.nombre }}</td>
                        <td>{{ espacio.capacidad }} personas</td>
                        <td>
                            <button @click="cargarParaEditar(espacio)" class="btn-editar"> Editar</button>
                            <button @click="eliminarEspacio(espacio.id)" class="btn-eliminar"> Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else class="sin-datos">No hay espacios registrados...</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const espacios = ref([]);
const nuevoEspacio = ref({ nombre: '', capacidad: '' });
const errores = ref({});
const enviando = ref(false);
const editandoId = ref(null); // Variable para saber si estamos editando

// 1. OBTENER DATOS (GET)
const obtenerEspacios = async () => {
    try {
        const respuesta = await axios.get('/api/espacios');
        espacios.value = respuesta.data;
    } catch (error) {
        console.error("Error al cargar:", error);
    }
};

// 2. GUARDAR DATOS (POST o PUT)
const guardarEspacio = async () => {
    errores.value = {};
    enviando.value = true;

    try {
        if (editandoId.value) {
            // SI ESTAMOS EDITANDO (PUT)
            const respuesta = await axios.put(`/api/espacios/${editandoId.value}`, nuevoEspacio.value);
            
            // Buscamos el espacio en la tabla y lo actualizamos al instante
            const index = espacios.value.findIndex(e => e.id === editandoId.value);
            if (index !== -1) {
                espacios.value[index] = respuesta.data.data;
            }
        } else {
            // SI ESTAMOS CREANDO (POST)
            const respuesta = await axios.post('/api/espacios', nuevoEspacio.value);
            espacios.value.push(respuesta.data.data);
        }
        
        // Limpiamos todo al terminar
        cancelarEdicion();

    } catch (error) {
        if (error.response && error.response.status === 422) {
            errores.value = error.response.data.errors;
        } else {
            alert("Error al guardar en el servidor.");
        }
    } finally {
        enviando.value = false;
    }
};

// 3. CARGAR DATOS AL FORMULARIO PARA EDITAR
const cargarParaEditar = (espacio) => {
    nuevoEspacio.value = { nombre: espacio.nombre, capacidad: espacio.capacidad };
    editandoId.value = espacio.id;
    errores.value = {};
    window.scrollTo({ top: 0, behavior: 'smooth' }); // Sube la pantalla al formulario
};

// 4. CANCELAR EDICIÓN
const cancelarEdicion = () => {
    nuevoEspacio.value = { nombre: '', capacidad: '' };
    editandoId.value = null;
    errores.value = {};
};

// 5. ELIMINAR (DELETE)
const eliminarEspacio = async (id) => {
    if (!confirm('¿Estás seguro de que deseas eliminar este espacio?')) return; 

    try {
        await axios.delete(`/api/espacios/${id}`);
        // Filtramos la tabla para quitar el eliminado
        espacios.value = espacios.value.filter(espacio => espacio.id !== id);
    } catch (error) {
        console.error("Error al eliminar:", error);
        alert("No se pudo eliminar el espacio.");
    }
};

onMounted(() => {
    obtenerEspacios();
});
</script>

<style scoped>
/* Estilos del formulario y tabla */
.formulario-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px; max-width: 500px; transition: all 0.3s ease; }
.modo-edicion { border: 2px solid #f1c40f; box-shadow: 0 0 10px rgba(241, 196, 15, 0.3); }
.formulario-card h2 { margin-top: 0; font-size: 1.2rem; color: #2c3e50; margin-bottom: 15px; }
.form-group { margin-bottom: 15px; }
label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
input:focus { outline: none; border-color: #3498db; box-shadow: 0 0 5px rgba(52, 152, 219, 0.3); }
.input-error { border-color: #e74c3c; background-color: #fadbd8; }
.text-error { color: #e74c3c; font-size: 0.85rem; margin-top: 4px; display: block; }
.botones-form { display: flex; gap: 10px; }
.btn-guardar { background-color: #2ecc71; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; flex-grow: 1; }
.btn-cancelar { background-color: #95a5a6; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
.tabla-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
th { background-color: #f8f9fa; font-weight: bold; color: #333; }
tr:hover { background-color: #f1f1f1; }
.btn-editar { background-color: #f1c40f; border: none; padding: 5px 10px; cursor: pointer; margin-right: 5px; border-radius: 3px; color: black; font-weight: bold;}
.btn-eliminar { background-color: #e74c3c; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; font-weight: bold;}
.sin-datos { text-align: center; color: #777; margin-top: 20px; }
</style>