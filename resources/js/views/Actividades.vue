<template>
  <div class="module-container">
    <div class="module-header">
      <div class="title-group">
        <h1>Gestión de Actividades</h1>
        <span class="badge-count">{{ actividades.length }} Promotorías</span>
      </div>
      <button @click="abrirModalCrear" class="btn-create">
        <i class="fa-solid fa-plus"></i> Nueva Actividad
      </button>
    </div>

    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre de la Actividad</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        
        <tbody v-if="cargando">
          <tr v-for="n in 5" :key="'skeleton-'+n">
            <td><div class="skeleton-box width-small"></div></td>
            <td><div class="skeleton-box width-large"></div></td>
            <td><div class="skeleton-box width-medium" style="margin: 0 auto;"></div></td>
          </tr>
        </tbody>

        <tbody v-else>
          <tr v-for="act in actividades" :key="act.id">
            <td class="id-cell">#{{ act.id }}</td>
            <td class="main-text">{{ act.nombre }}</td>
            <td class="actions-cell">
              <button @click="abrirModalEditar(act)" class="btn-icon edit" title="Editar Nombre">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button @click="eliminarActividad(act.id)" class="btn-icon delete" title="Eliminar Actividad">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="!cargando && actividades.length === 0" class="sin-datos">
        <p><i class="fa-regular fa-folder-open"></i> No hay actividades registradas aún.</p>
      </div>
    </div>

    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal-card">
        <div class="modal-header">
          <h2>
            <i :class="editandoId ? 'fa-solid fa-pen-nib' : 'fa-solid fa-palette'"></i>
            {{ editandoId ? 'Editar Actividad' : 'Nueva Actividad' }}
          </h2>
          <button @click="cerrarModal" class="btn-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form @submit.prevent="guardarActividad">
          <div class="form-group mt-3">
            <label>Nombre de la Promotoría:</label>
            <input 
              type="text" 
              v-model="nombreActividad" 
              placeholder="Ej. Danza Folklórica, Fútbol, Robótica..." 
              class="form-control"
              required
            >
            <span v-if="errorNombre" class="text-error">{{ errorNombre }}</span>
          </div>

          <div class="modal-footer">
            <button type="button" @click="cerrarModal" class="btn-cancelar">Cancelar</button>
            <button type="submit" class="btn-guardar" :disabled="enviando">
              <i :class="enviando ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-floppy-disk'"></i>
              {{ enviando ? ' Procesando...' : (editandoId ? ' Actualizar' : ' Guardar') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Variables de Estado
const actividades = ref([]);
const mostrarModal = ref(false);
const nombreActividad = ref('');
const editandoId = ref(null);
const enviando = ref(false);
const errorNombre = ref('');
const cargando = ref(true); // 🔴 Nueva variable para el Skeleton Loader

// Cargar datos del servidor
const cargarActividades = async () => {
  cargando.value = true; // Iniciamos la animación
  try {
    const res = await axios.get('/api/actividades');
    actividades.value = res.data;
  } catch (error) {
    console.error("Error al cargar actividades:", error);
  } finally {
    cargando.value = false; // Detenemos la animación
  }
};

// Control de Modales
const abrirModalCrear = () => {
  editandoId.value = null;
  nombreActividad.value = '';
  errorNombre.value = '';
  mostrarModal.value = true;
};

const abrirModalEditar = (act) => {
  editandoId.value = act.id;
  nombreActividad.value = act.nombre;
  errorNombre.value = '';
  mostrarModal.value = true;
};

const cerrarModal = () => {
  mostrarModal.value = false;
  errorNombre.value = '';
};

// Guardar (POST o PUT)
const guardarActividad = async () => {
  if (!nombreActividad.value.trim()) {
    errorNombre.value = 'El nombre es obligatorio';
    return;
  }

  enviando.value = true;
  errorNombre.value = '';

  try {
    if (editandoId.value) {
      await axios.put(`/api/actividades/${editandoId.value}`, { nombre: nombreActividad.value });
    } else {
      await axios.post('/api/actividades', { nombre: nombreActividad.value });
    }
    
    await cargarActividades();
    cerrarModal();
  } catch (error) {
    if (error.response?.status === 422) {
      errorNombre.value = error.response.data.errors.nombre[0];
    } else {
      alert("Error al procesar la solicitud.");
    }
  } finally {
    enviando.value = false;
  }
};

// Eliminar
const eliminarActividad = async (id) => {
  if (confirm('¿Estás seguro de eliminar esta actividad? Esto podría afectar a los grupos asignados.')) {
    try {
      await axios.delete(`/api/actividades/${id}`);
      cargarActividades();
    } catch (error) {
      alert(error.response?.data?.mensaje || "No se pudo eliminar la actividad.");
    }
  }
};

onMounted(cargarActividades);
</script>

<style scoped>
/* Estilos Base */
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.title-group h1 { color: #2c3e50; font-size: 1.8rem; margin: 0; }
.badge-count { background: #f0f2f5; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; color: #666; font-weight: bold; margin-top: 5px; display: inline-block; }

/* Contenedor Responsivo para la Tabla */
.table-responsive { width: 100%; overflow-x: auto; background: #fff; border-radius: 8px; }
.custom-table { width: 100%; border-collapse: collapse; min-width: 600px; /* Evita que las columnas se aplasten */ }

.id-cell { font-weight: bold; color: #95a5a6; width: 80px; }
.main-text { font-weight: 600; color: #34495e; }
.actions-cell { text-align: center; white-space: nowrap; width: 120px; }
.btn-icon { background: none; border: none; font-size: 1.1rem; cursor: pointer; padding: 5px 10px; transition: 0.2s; }
.btn-icon.edit { color: #3498db; }
.btn-icon.delete { color: #e74c3c; }
.btn-icon:hover { transform: scale(1.2); }

.sin-datos { text-align: center; padding: 30px; color: #95a5a6; font-size: 1.1rem; }

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
.width-medium { width: 60px; }
.width-large { width: 150px; }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(4px); padding: 15px; }
.modal-card { background: #fff; width: 100%; max-width: 450px; border-radius: 12px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
.modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; }
.modal-header h2 { font-size: 1.3rem; margin: 0; color: #2c3e50; }
.btn-close { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #95a5a6; }

.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
.form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
.form-control:focus { border-color: #c0392b; outline: none; }

.modal-footer { display: flex; justify-content: flex-end; margin-top: 20px; }
.btn-create, .btn-guardar { background: #c0392b; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background 0.3s; }
.btn-create:hover, .btn-guardar:hover:not(:disabled) { background: #a93226; }
.btn-guardar:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-cancelar { background: #f1f2f6; color: #2f3542; border: none; padding: 10px 20px; border-radius: 6px; margin-right: 10px; cursor: pointer; font-weight: 600; transition: background 0.3s; }
.btn-cancelar:hover { background: #dfe4ea; }
.text-error { color: #e74c3c; font-size: 0.8rem; margin-top: 5px; display: block; font-weight: 500; }

/* =========================================
   DISEÑO RESPONSIVO (Móviles y Tablets)
   ========================================= */
@media (max-width: 768px) {
  .module-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .btn-create {
    width: 100%;
    justify-content: center;
  }
  
  .title-group h1 {
    font-size: 1.5rem;
  }
  
  /* Permite que la tabla haga scroll horizontal sin romper el diseño */
  .table-responsive {
    border: 1px solid #eee;
    padding-bottom: 10px;
  }
}
</style>