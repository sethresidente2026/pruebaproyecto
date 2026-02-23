<template>
  <div class="module-container">
    <div class="module-header">
      <div class="title-group">
        <h1>Gestión de Espacios</h1>
        <span class="badge-count">{{ espacios.length }} Registrados</span>
      </div>
      
      <div class="header-actions">
        <button @click="abrirModalCrear" class="btn-create">
          <i class="fa-solid fa-plus"></i> Nuevo Espacio
        </button>
      </div>
    </div>

    <div class="filter-bar">
      <div class="search-box">
        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" v-model="busqueda" placeholder="Buscar por nombre de aula o cancha..." />
      </div>
    </div>

    <div class="table-responsive">
      <table class="custom-table" v-if="cargando || listaFiltrada.length > 0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre del Espacio</th>
            <th>Capacidad Máxima</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        
        <tbody v-if="cargando">
          <tr v-for="n in 5" :key="'skeleton-'+n">
            <td><div class="skeleton-box width-small"></div></td>
            <td><div class="skeleton-box width-medium"></div></td>
            <td><div class="skeleton-box width-small"></div></td>
            <td><div class="skeleton-box width-small" style="margin: 0 auto;"></div></td>
          </tr>
        </tbody>

        <tbody v-else>
          <tr v-for="espacio in listaFiltrada" :key="espacio.id">
            <td class="id-cell">#{{ espacio.id }}</td>
            <td>
              <div class="item-info">
                <span class="main-text">
                  <i class="fa-solid fa-door-open text-muted"></i> 
                  {{ espacio.nombre }}
                </span>
              </div>
            </td>
            <td>
              <span class="capacidad-badge">
                <i class="fa-solid fa-users"></i> {{ espacio.capacidad }} personas
              </span>
            </td>
            <td class="actions-cell">
              <button @click="cargarParaEditar(espacio)" class="btn-icon edit" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button @click="eliminarEspacio(espacio.id)" class="btn-icon delete" title="Eliminar">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-else class="sin-datos">
        <i class="fa-solid fa-building-circle-xmark empty-icon"></i>
        <p>No se encontraron espacios con ese nombre...</p>
      </div>
    </div>

    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal-card">
        <div class="modal-header">
          <h2>
            <i :class="editandoId ? 'fa-solid fa-pen-nib' : 'fa-solid fa-house-medical'"></i>
            {{ editandoId ? 'Editar Espacio' : 'Nuevo Espacio' }}
          </h2>
          <button @click="cerrarModal" class="btn-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form @submit.prevent="guardarEspacio" class="modal-form">
          <div class="form-grid">
            <div class="form-group full-width">
              <label>Nombre del Espacio:</label>
              <input type="text" v-model="nuevoEspacio.nombre" :class="{'input-error': errores.nombre}" placeholder="Ej. Aula 101, Cancha 2">
              <span class="text-error" v-if="errores.nombre">{{ errores.nombre[0] }}</span>
            </div>

            <div class="form-group full-width">
              <label>Capacidad (Personas):</label>
              <input type="number" v-model="nuevoEspacio.capacidad" :class="{'input-error': errores.capacidad}" placeholder="Ej. 35">
              <span class="text-error" v-if="errores.capacidad">{{ errores.capacidad[0] }}</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" @click="cerrarModal" class="btn-cancelar">Cancelar</button>
            <button type="submit" class="btn-guardar" :disabled="enviando">
               <i :class="enviando ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-floppy-disk'"></i>
              {{ enviando ? 'Procesando...' : (editandoId ? 'Guardar Cambios' : 'Registrar Espacio') }}
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
const espacios = ref([]);
const nuevoEspacio = ref({ nombre: '', capacidad: '' });
const errores = ref({});
const enviando = ref(false);
const editandoId = ref(null);
const mostrarModal = ref(false);
const busqueda = ref('');
const cargando = ref(true); // 🔴 Variable para el Skeleton Loader

// Filtro Inteligente
const listaFiltrada = computed(() => {
  const termino = busqueda.value.toLowerCase();
  return espacios.value.filter(e => e.nombre.toLowerCase().includes(termino));
});

// Lógica de Datos
const obtenerEspacios = async () => {
    cargando.value = true; // Inicia la carga
    try {
        const respuesta = await axios.get('/api/espacios');
        espacios.value = respuesta.data;
    } catch (error) { 
        console.error("Error al cargar:", error); 
    } finally {
        cargando.value = false; // Detiene la carga
    }
};

const abrirModalCrear = () => {
    editandoId.value = null;
    nuevoEspacio.value = { nombre: '', capacidad: '' };
    errores.value = {};
    mostrarModal.value = true;
};

const cargarParaEditar = (espacio) => {
    nuevoEspacio.value = { nombre: espacio.nombre, capacidad: espacio.capacidad };
    editandoId.value = espacio.id;
    errores.value = {};
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
};

const guardarEspacio = async () => {
    errores.value = {};
    enviando.value = true;
    try {
        if (editandoId.value) {
            await axios.put(`/api/espacios/${editandoId.value}`, nuevoEspacio.value);
        } else {
            await axios.post('/api/espacios', nuevoEspacio.value);
        }
        await obtenerEspacios();
        cerrarModal();
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

const eliminarEspacio = async (id) => {
    if (!confirm('¿Seguro que deseas eliminar este espacio?')) return; 
    try {
        await axios.delete(`/api/espacios/${id}`);
        // Actualizamos localmente sin recargar todo para mejor UX
        espacios.value = espacios.value.filter(e => e.id !== id);
    } catch (error) {
        alert("No se pudo eliminar el espacio. Verifica que no tenga horarios asignados.");
    }
};

onMounted(obtenerEspacios);
</script>

<style scoped>
/* =========================================
   Reutilizando el diseño corporativo
   ========================================= */
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.title-group h1 { color: var(--ugm-dark); font-size: 1.8rem; margin: 0; }
.badge-count { background: #f0f2f5; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; color: #666; font-weight: bold; margin-top: 5px; display: inline-block; }

/* Buscador */
.filter-bar { margin-bottom: 20px; display: flex; gap: 15px; }
.search-box { position: relative; width: 100%; max-width: 400px; }
.search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6; }
.search-box input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #e0e4e8; border-radius: 8px; font-size: 0.95rem; background: #fafbfc; }
.search-box input:focus { outline: none; border-color: var(--ugm-red); box-shadow: 0 0 0 3px rgba(209, 16, 26, 0.1); }

/* Contenedor Responsivo para la Tabla */
.table-responsive { width: 100%; overflow-x: auto; background: #fff; border-radius: 8px; }
.custom-table { width: 100%; border-collapse: collapse; min-width: 600px; }

/* Tabla y Textos */
.id-cell { font-weight: bold; color: #95a5a6; }
.item-info { display: flex; flex-direction: column; gap: 4px; }
.main-text { font-weight: 700; color: #2c3e50; font-size: 1rem; }
.text-muted { color: #bdc3c7; margin-right: 5px; }

.capacidad-badge { background: #eef2f5; border: 1px solid #dcdde1; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; color: #34495e; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }

.actions-cell { text-align: center; white-space: nowrap; }
.btn-icon { background: none; border: none; font-size: 1.1rem; cursor: pointer; padding: 5px 10px; transition: 0.2s; }
.btn-icon.edit { color: #3498db; }
.btn-icon.delete { color: #e74c3c; }
.btn-icon:hover { transform: scale(1.2); }

/* =========================================
   SKELETON LOADERS (Efecto de Carga)
   ========================================= */
.skeleton-box {
  height: 18px;
  background-color: #e2e8f0;
  border-radius: 4px;
  position: relative;
  overflow: hidden;
}
.skeleton-box::after {
  content: "";
  position: absolute;
  top: 0; right: 0; bottom: 0; left: 0;
  transform: translateX(-100%);
  background-image: linear-gradient(90deg, rgba(255, 255, 255, 0) 0, rgba(255, 255, 255, 0.4) 20%, rgba(255, 255, 255, 0.6) 60%, rgba(255, 255, 255, 0));
  animation: shimmer 1.5s infinite;
}
@keyframes shimmer { 100% { transform: translateX(100%); } }
.width-small { width: 40px; }
.width-medium { width: 120px; }

/* Modal y Formulario */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(4px); padding: 15px; }
.modal-card { background: #fff; width: 100%; max-width: 450px; border-radius: 12px; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease; }
@keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
.modal-header h2 { margin: 0; font-size: 1.4rem; color: var(--ugm-dark); display: flex; align-items: center; gap: 10px; }
.btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #95a5a6; transition: color 0.2s; }
.btn-close:hover { color: #e74c3c; }

.form-grid { display: flex; flex-direction: column; gap: 15px; }
.form-group.full-width { width: 100%; }
label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; font-size: 0.9rem;}
input { width: 100%; padding: 12px; border: 1px solid #dcdde1; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem;}
input:focus { outline: none; border-color: #c0392b; }
.input-error { border-color: #e74c3c; background-color: #fff6f6; }
.text-error { color: #e74c3c; font-size: 0.8rem; margin-top: 5px; display: block; }

.modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
.btn-guardar { background-color: var(--ugm-red); color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; }
.btn-guardar:hover:not(:disabled) { background-color: #b00d15; transform: translateY(-1px); }
.btn-guardar:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-cancelar { background-color: #f1f2f6; color: #576574; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
.btn-cancelar:hover { background-color: #dfe4ea; }

/* Botones Superiores */
.btn-create { background: var(--ugm-red); color: #fff; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
.btn-create:hover { background: #b00d15; box-shadow: 0 4px 10px rgba(209, 16, 26, 0.2); }

.sin-datos { text-align: center; color: #95a5a6; padding: 40px 20px; }
.empty-icon { font-size: 3rem; margin-bottom: 15px; color: #bdc3c7; }

/* =========================================
   DISEÑO RESPONSIVO (Móviles y Tablets)
   ========================================= */
@media (max-width: 768px) {
  .module-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .header-actions {
    width: 100%;
  }

  .btn-create {
    width: 100%;
    justify-content: center;
  }
  
  .title-group h1 {
    font-size: 1.5rem;
  }
  
  .search-box {
    max-width: 100%;
  }

  .table-responsive {
    border: 1px solid #eee;
    padding-bottom: 10px;
  }
}
</style>