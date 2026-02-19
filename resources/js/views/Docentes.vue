<template>
  <div class="module-container">
    <div class="module-header">
      <div class="title-group">
        <h1>Gestión de Docentes</h1>
        <span class="badge-count">{{ docentes.length }} Registrados</span>
      </div>
      
      <div class="header-actions">
        <button @click="abrirModalCrear" class="btn-create">
          <i class="fa-solid fa-plus"></i> Nuevo Docente
        </button>
        <button @click="descargarExcel" class="btn-excel-top">
          <i class="fa-solid fa-file-excel"></i> Descargar Excel
        </button>
      </div>
    </div>

    <div class="filter-bar">
      <div class="search-box">
        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" v-model="busqueda" placeholder="Buscar por nombre, apellido o correo..." />
      </div>
    </div>

    <div class="table-responsive">
      <table class="custom-table" v-if="listaFiltrada.length > 0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Información del Docente</th>
            <th>Estatus</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="docente in listaFiltrada" :key="docente.id">
            <td class="id-cell">#{{ docente.id }}</td>
            <td>
              <div class="item-info">
                <span class="main-text">
                  <i class="fa-solid fa-user-tie text-muted"></i> 
                  {{ docente.nombre }} {{ docente.apellidos }}
                </span>
                <span class="sub-text">{{ docente.email }}</span>
              </div>
            </td>
            <td>
              <span :class="['status-pill', docente.estatus.toLowerCase()]">
                {{ docente.estatus }}
              </span>
            </td>
            <td class="actions-cell">
              <button @click="cargarParaEditar(docente)" class="btn-icon edit" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button @click="eliminarDocente(docente.id)" class="btn-icon delete" title="Eliminar">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="sin-datos">
        <i class="fa-solid fa-folder-open empty-icon"></i>
        <p>No se encontraron docentes registrados...</p>
      </div>
    </div>

    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal-card">
        <div class="modal-header">
          <h2>
            <i :class="editandoId ? 'fa-solid fa-user-pen' : 'fa-solid fa-user-plus'"></i>
            {{ editandoId ? 'Editar Docente' : 'Nuevo Docente' }}
          </h2>
          <button @click="cerrarModal" class="btn-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form @submit.prevent="guardarDocente" class="modal-form">
          <div class="form-grid">
            <div class="form-group">
              <label>Nombre(s):</label>
              <input type="text" v-model="nuevoDocente.nombre" :class="{'input-error': errores.nombre}" placeholder="Ej. Juan Carlos">
              <span class="text-error" v-if="errores.nombre">{{ errores.nombre[0] }}</span>
            </div>

            <div class="form-group">
              <label>Apellidos:</label>
              <input type="text" v-model="nuevoDocente.apellidos" :class="{'input-error': errores.apellidos}" placeholder="Ej. Pérez Gómez">
              <span class="text-error" v-if="errores.apellidos">{{ errores.apellidos[0] }}</span>
            </div>

            <div class="form-group">
              <label>Correo Electrónico:</label>
              <input type="email" v-model="nuevoDocente.email" :class="{'input-error': errores.email}" placeholder="correo@ugm.edu.mx">
              <span class="text-error" v-if="errores.email">{{ errores.email[0] }}</span>
            </div>

            <div class="form-group">
              <label>Estatus:</label>
              <select v-model="nuevoDocente.estatus" :class="{'input-error': errores.estatus}">
                <option value="Activo">🟢 Activo</option>
                <option value="Inactivo">🔴 Inactivo</option>
              </select>
              <span class="text-error" v-if="errores.estatus">{{ errores.estatus[0] }}</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" @click="cerrarModal" class="btn-cancelar">Cancelar</button>
            <button type="submit" class="btn-guardar" :disabled="enviando">
              <i class="fa-solid fa-floppy-disk"></i>
              {{ enviando ? 'Procesando...' : (editandoId ? 'Guardar Cambios' : 'Registrar Docente') }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// Estado
const docentes = ref([]);
const nuevoDocente = ref({ nombre: '', apellidos: '', email: '', estatus: 'Activo' });
const errores = ref({});
const enviando = ref(false);
const editandoId = ref(null);
const mostrarModal = ref(false);
const busqueda = ref('');

// Filtro Inteligente
const listaFiltrada = computed(() => {
  const termino = busqueda.value.toLowerCase();
  return docentes.value.filter(d => 
    d.nombre.toLowerCase().includes(termino) ||
    d.apellidos.toLowerCase().includes(termino) ||
    d.email.toLowerCase().includes(termino)
  );
});

// Lógica de Datos
const obtenerDocentes = async () => {
    try {
        const respuesta = await axios.get('/api/docentes');
        docentes.value = respuesta.data;
    } catch (error) { console.error(error); }
};

const descargarExcel = () => {
    window.open('/api/reportes/docentes', '_blank');
};

const abrirModalCrear = () => {
    editandoId.value = null;
    nuevoDocente.value = { nombre: '', apellidos: '', email: '', estatus: 'Activo' };
    errores.value = {};
    mostrarModal.value = true;
};

const cargarParaEditar = (docente) => {
    nuevoDocente.value = { ...docente };
    editandoId.value = docente.id;
    errores.value = {};
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
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
        cerrarModal();
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

const eliminarDocente = async (id) => {
    if (!confirm('¿Seguro que deseas eliminar este docente?')) return; 
    try {
        await axios.delete(`/api/docentes/${id}`);
        docentes.value = docentes.value.filter(d => d.id !== id);
    } catch (error) {
        if (error.response && error.response.status === 500) {
            alert("No puedes eliminar este docente porque ya tiene GRUPOS asignados.");
        } else {
            alert("Ocurrió un error.");
        }
    }
};

onMounted(obtenerDocentes);
</script>

<style scoped>
/* =========================================
   Reutilizando el diseño corporativo
   ========================================= */
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.title-group h1 { color: var(--ugm-dark); font-size: 1.8rem; margin: 0; }
.badge-count { background: #f0f2f5; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; color: #666; font-weight: bold; }

/* Buscador */
.filter-bar { margin-bottom: 20px; display: flex; gap: 15px; }
.search-box { position: relative; width: 100%; max-width: 400px; }
.search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6; }
.search-box input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #e0e4e8; border-radius: 8px; font-size: 0.95rem; background: #fafbfc; }
.search-box input:focus { outline: none; border-color: var(--ugm-red); box-shadow: 0 0 0 3px rgba(209, 16, 26, 0.1); }

/* Tabla y Textos */
.id-cell { font-weight: bold; color: #95a5a6; }
.item-info { display: flex; flex-direction: column; gap: 4px; }
.main-text { font-weight: 700; color: #2c3e50; font-size: 1rem; }
.text-muted { color: #bdc3c7; margin-right: 5px; }
.sub-text { font-size: 0.85rem; color: #7f8c8d; }

.actions-cell { text-align: center; white-space: nowrap; }
.btn-icon.edit { color: #3498db; }
.btn-icon.delete { color: #e74c3c; }

/* Modal y Formulario */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(4px); }
.modal-card { background: #fff; width: 100%; max-width: 500px; border-radius: 12px; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease; }
@keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
.modal-header h2 { margin: 0; font-size: 1.4rem; color: var(--ugm-dark); display: flex; align-items: center; gap: 10px; }
.btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #95a5a6; transition: color 0.2s; }
.btn-close:hover { color: #e74c3c; }

.form-group { margin-bottom: 15px; }
label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; font-size: 0.9rem;}
input, select { width: 100%; padding: 12px; border: 1px solid #dcdde1; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem;}
.input-error { border-color: #e74c3c; background-color: #fff6f6; }
.text-error { color: #e74c3c; font-size: 0.8rem; margin-top: 5px; display: block; }

.modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
.btn-guardar { background-color: var(--ugm-red); color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; }
.btn-guardar:hover:not(:disabled) { background-color: #b00d15; transform: translateY(-1px); }
.btn-cancelar { background-color: #f1f2f6; color: #576574; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
.btn-cancelar:hover { background-color: #dfe4ea; }

/* Botones Superiores */
.btn-create { background: var(--ugm-red); color: #fff; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
.btn-create:hover { background: #b00d15; box-shadow: 0 4px 10px rgba(209, 16, 26, 0.2); }

.sin-datos { text-align: center; color: #95a5a6; padding: 40px 20px; }
.empty-icon { font-size: 3rem; margin-bottom: 15px; color: #bdc3c7; }
</style>