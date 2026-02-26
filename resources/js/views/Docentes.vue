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
      <table class="custom-table" v-if="cargando || listaFiltrada.length > 0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Información del Docente</th>
            <th>Estatus</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        
        <tbody v-if="cargando">
          <tr v-for="n in 5" :key="'skeleton-'+n">
            <td><div class="skeleton-box width-small"></div></td>
            <td>
              <div class="item-info">
                <div class="skeleton-box width-large" style="margin-bottom: 5px;"></div>
                <div class="skeleton-box width-medium"></div>
              </div>
            </td>
            <td><div class="skeleton-box width-medium" style="border-radius: 15px;"></div></td>
            <td><div class="skeleton-box width-small" style="margin: 0 auto;"></div></td>
          </tr>
        </tbody>

        <tbody v-else>
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
      
      <div v-if="!cargando && listaFiltrada.length === 0" class="sin-datos">
        <i class="fa-solid fa-folder-open empty-icon"></i>
        <p>No se encontraron docentes con esos datos...</p>
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
              <i :class="enviando ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-floppy-disk'"></i>
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
// 🔴 1. Importamos SweetAlert2
import Swal from 'sweetalert2';

// Estado
const docentes = ref([]);
const nuevoDocente = ref({ nombre: '', apellidos: '', email: '', estatus: 'Activo' });
const errores = ref({});
const enviando = ref(false);
const editandoId = ref(null);
const mostrarModal = ref(false);
const busqueda = ref('');
const cargando = ref(true); 

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
    cargando.value = true;
    try {
        const respuesta = await axios.get('/api/docentes');
        docentes.value = respuesta.data;
    } catch (error) { 
        console.error(error); 
    } finally {
        cargando.value = false;
    }
};

const descargarExcel = () => {
    window.location.href = '/api/reportes/docentes';
};

const abrirModalCrear = () => {
    editandoId.value = null; 
    nuevoDocente.value = { nombre: '', apellidos: '', email: '', estatus: 'Activo' };
    errores.value = {};
    mostrarModal.value = true;
};

const cargarParaEditar = (docente) => {
    nuevoDocente.value = { 
        nombre: docente.nombre, 
        apellidos: docente.apellidos, 
        email: docente.email, 
        estatus: docente.estatus 
    };
    editandoId.value = docente.id;
    errores.value = {};
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
};

// 🔴 2. Guardar Docente con Notificaciones Modernas
const guardarDocente = async () => {
    errores.value = {};
    enviando.value = true;
    try {
        if (editandoId.value) {
            await axios.put(`/api/docentes/${editandoId.value}`, nuevoDocente.value);
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Datos actualizados', showConfirmButton: false, timer: 3000, timerProgressBar: true });
        } else {
            await axios.post('/api/docentes', nuevoDocente.value);
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Docente registrado', showConfirmButton: false, timer: 3000, timerProgressBar: true });
        }
        await obtenerDocentes();
        cerrarModal();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            if (error.response.data.errors) {
                errores.value = error.response.data.errors;
            } else if (error.response.data.message) {
                // Error de lógica de negocio (ej: docente con grupos activos)
                Swal.fire({ icon: 'warning', title: 'Acción restringida', text: error.response.data.message, confirmButtonColor: '#e67e22' });
            }
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un error inesperado en el servidor.', confirmButtonColor: '#c0392b' });
        }
    } finally { 
        enviando.value = false; 
    }
};

// 🔴 3. Eliminar Docente con Confirmación Estilizada
const eliminarDocente = async (id) => {
    const result = await Swal.fire({
        title: '¿Eliminar docente?',
        text: "Esta acción es irreversible. Se recomienda cambiar el estatus a 'Inactivo' en su lugar.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#95a5a6',
        confirmButtonText: 'Sí, eliminar permanentemente',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/docentes/${id}`);
            docentes.value = docentes.value.filter(d => d.id !== id);
            Swal.fire({ title: '¡Eliminado!', text: 'El docente ha sido borrado del sistema.', icon: 'success', confirmButtonColor: '#27ae60' });
        } catch (error) {
            if (error.response && error.response.status === 422) {
                // Mensaje enviado desde tu DocenteService.php
                Swal.fire({ icon: 'error', title: 'No se puede eliminar', text: error.response.data.message, confirmButtonColor: '#c0392b' });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo completar la eliminación.', confirmButtonColor: '#c0392b' });
            }
        }
    }
};

onMounted(obtenerDocentes);
</script>

<style scoped>
/* =========================================
   Diseño General
   ========================================= */
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.title-group h1 { color: var(--ugm-dark); font-size: 1.8rem; margin: 0; }
.badge-count { background: #f0f2f5; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; color: #666; font-weight: bold; margin-top: 5px; display: inline-block; }

.header-actions { display: flex; gap: 10px; }

/* Buscador */
.filter-bar { margin-bottom: 20px; display: flex; gap: 15px; }
.search-box { position: relative; width: 100%; max-width: 400px; }
.search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6; }
.search-box input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #e0e4e8; border-radius: 8px; font-size: 0.95rem; background: #fafbfc; }
.search-box input:focus { outline: none; border-color: var(--ugm-red); box-shadow: 0 0 0 3px rgba(209, 16, 26, 0.1); }

/* Contenedor Responsivo para la Tabla */
.table-responsive { width: 100%; overflow-x: auto; background: #fff; border-radius: 8px; }
.custom-table { width: 100%; border-collapse: collapse; min-width: 700px; } /* Ajustado para que no se aprieten las columnas */

/* Tabla y Textos */
.id-cell { font-weight: bold; color: #95a5a6; }
.item-info { display: flex; flex-direction: column; gap: 4px; }
.main-text { font-weight: 700; color: #2c3e50; font-size: 1rem; }
.text-muted { color: #bdc3c7; margin-right: 5px; }
.sub-text { font-size: 0.85rem; color: #7f8c8d; }

.status-pill { padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; font-weight: bold; text-transform: capitalize; display: inline-block; }
.status-pill.activo { background: #E8F5E9; color: #2E7D32; }
.status-pill.inactivo { background: #FFEBEE; color: #C62828; }

.actions-cell { text-align: center; white-space: nowrap; }
.btn-icon.edit { color: #3498db; }
.btn-icon.delete { color: #e74c3c; }

/* Modal y Formulario */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(4px); padding: 15px; }
.modal-card { background: #fff; width: 100%; max-width: 500px; border-radius: 12px; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease; }
@keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
.modal-header h2 { margin: 0; font-size: 1.4rem; color: var(--ugm-dark); display: flex; align-items: center; gap: 10px; }
.btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #95a5a6; transition: color 0.2s; }
.btn-close:hover { color: #e74c3c; }

.form-group { margin-bottom: 15px; }
label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; font-size: 0.9rem;}
input, select { width: 100%; padding: 12px; border: 1px solid #dcdde1; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem;}
input:focus, select:focus { outline: none; border-color: #c0392b; }
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
.btn-excel-top { background-color: #27ae60; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
.btn-excel-top:hover { background-color: #219653; box-shadow: 0 4px 10px rgba(39, 174, 96, 0.2); }

.sin-datos { text-align: center; color: #95a5a6; padding: 40px 20px; }
.empty-icon { font-size: 3rem; margin-bottom: 15px; color: #bdc3c7; }

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
.width-medium { width: 100px; }
.width-large { width: 180px; }

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
    flex-direction: column; /* Apila los botones en móvil */
  }

  .btn-create, .btn-excel-top {
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