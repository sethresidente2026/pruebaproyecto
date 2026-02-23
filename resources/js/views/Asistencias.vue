<template>
  <div class="module-container">
    <div class="module-header">
      <div class="title-group">
        <h1>Control de Asistencias</h1>
        <span class="badge-count">{{ asistencias.length }} Registros</span>
      </div>
      
      <div class="header-actions">
        <button @click="abrirModalCrear" class="btn-create">
          <i class="fa-solid fa-clipboard-user"></i> Pasar Lista
        </button>
        <button @click="descargarReporte" class="btn-excel-top" title="Descargar cálculo de clases">
  <i class="fa-solid fa-file-invoice-dollar"></i> Reporte de Pagos
</button>
      </div>
    </div>

    <div class="filter-bar">
      <div class="search-box">
        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" v-model="busqueda" placeholder="Buscar por grupo o docente..." />
      </div>
    </div>

    <div class="table-responsive">
      <table class="custom-table" v-if="listaFiltrada.length > 0">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Grupo / Actividad</th>
            <th>Docente Titular</th>
            <th>Estado</th>
            <th>Sustituto (Si aplica)</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in listaFiltrada" :key="item.id">
            <td class="id-cell"><i class="fa-regular fa-calendar text-muted"></i> {{ formatearFecha(item.fecha) }}</td>
            <td>
              <div class="item-info">
                <span class="main-text">{{ item.grupo?.nombre }}</span>
              </div>
            </td>
            <td>
              <div class="docente-pill">
                <i class="fa-solid fa-user-tie text-muted"></i> 
                {{ item.docente_titular?.nombre }} {{ item.docente_titular?.apellidos }}
              </div>
            </td>
            <td>
              <span :class="['estado-badge', 'estado-' + item.estado.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')]">
                {{ item.estado }}
              </span>
            </td>
            <td>
              <div v-if="item.estado === 'Sustitución'" class="docente-pill sustituto-pill">
                <i class="fa-solid fa-user-shield text-muted"></i> 
                {{ item.docente_sustituto?.nombre }} {{ item.docente_sustituto?.apellidos }}
              </div>
              <span v-else class="text-muted">-</span>
            </td>
            <td class="actions-cell">
              <button @click="eliminarAsistencia(item.id)" class="btn-icon delete" title="Eliminar Registro">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="sin-datos">
        <i class="fa-solid fa-clipboard-list empty-icon"></i>
        <p>No hay pases de lista registrados...</p>
      </div>
    </div>

    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal-card">
        <div class="modal-header">
          <h2><i class="fa-solid fa-clipboard-check"></i> Registrar Pase de Lista</h2>
          <button @click="cerrarModal" class="btn-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form @submit.prevent="guardarAsistencia" class="modal-form">
          <div class="form-grid">
            
            <div class="form-group">
              <label>Fecha de la Clase:</label>
              <input type="date" v-model="nuevaAsistencia.fecha" :class="{'input-error': errores.fecha}" required>
              <span class="text-error" v-if="errores?.fecha">{{ errores.fecha[0] }}</span>
            </div>

            <div class="form-group">
              <label>Grupo:</label>
              <select v-model="nuevaAsistencia.grupo_id" :class="{'input-error': errores.grupo_id}" required>
                <option value="" disabled>Seleccione el grupo...</option>
                <option v-for="grupo in grupos" :key="grupo.id" :value="grupo.id">{{ grupo.nombre }}</option>
              </select>
              <span class="text-error" v-if="errores?.grupo_id">{{ errores.grupo_id[0] }}</span>
            </div>

            <div class="form-group">
              <label>Docente Titular:</label>
              <select v-model="nuevaAsistencia.docente_id" :class="{'input-error': errores.docente_id}" required>
                <option value="" disabled>¿A quién le tocaba la clase?</option>
                <option v-for="doc in docentes" :key="doc.id" :value="doc.id">{{ doc.nombre }} {{ doc.apellidos }}</option>
              </select>
              <span class="text-error" v-if="errores?.docente_id">{{ errores.docente_id[0] }}</span>
            </div>

            <div class="form-group">
              <label>Estado de Asistencia:</label>
              <select v-model="nuevaAsistencia.estado" :class="{'input-error': errores.estado}" required>
                <option value="Asistió">Asistió</option>
                <option value="Falta">Falta</option>
                <option value="Retardo">Retardo</option>
                <option value="Sustitución">Sustitución (Mandó a alguien más)</option>
              </select>
              <span class="text-error" v-if="errores?.estado">{{ errores.estado[0] }}</span>
            </div>

            <div class="form-group" v-if="nuevaAsistencia.estado === 'Sustitución'" style="grid-column: span 2;">
              <label>Docente Sustituto (¿Quién dio la clase realmente?):</label>
              <select v-model="nuevaAsistencia.docente_sustituto_id" :class="{'input-error': errores.docente_sustituto_id}" required>
                <option value="" disabled>Seleccione al maestro que cubrió...</option>
                <option v-for="doc in docentes" :key="doc.id" :value="doc.id">{{ doc.nombre }} {{ doc.apellidos }}</option>
              </select>
              <span class="text-error" v-if="errores?.docente_sustituto_id">{{ errores.docente_sustituto_id[0] }}</span>
            </div>

            <div class="form-group" style="grid-column: span 2;">
              <label>Observaciones (Opcional):</label>
              <input type="text" v-model="nuevaAsistencia.observaciones" placeholder="Justificantes, reportes, etc.">
            </div>

          </div>

          <div class="modal-footer">
            <button type="button" @click="cerrarModal" class="btn-cancelar">Cancelar</button>
            <button type="submit" class="btn-guardar" :disabled="enviando">
              <i class="fa-solid fa-floppy-disk"></i>
              {{ enviando ? 'Guardando...' : 'Registrar Asistencia' }}
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

const asistencias = ref([]);
const grupos = ref([]);
const docentes = ref([]);

// Para que por defecto aparezca la fecha de hoy
const fechaHoy = new Date().toISOString().split('T')[0];
const nuevaAsistencia = ref({ fecha: fechaHoy, grupo_id: '', docente_id: '', estado: 'Asistió', docente_sustituto_id: '', observaciones: '' });
const errores = ref({});
const enviando = ref(false);
const mostrarModal = ref(false);
const busqueda = ref('');

const listaFiltrada = computed(() => {
  const termino = busqueda.value.toLowerCase();
  return asistencias.value.filter(a => 
    a.grupo?.nombre.toLowerCase().includes(termino) ||
    (a.docente_titular?.nombre + ' ' + a.docente_titular?.apellidos).toLowerCase().includes(termino)
  );
});
const descargarReporte = () => {
    window.open('/api/reportes/pagos', '_blank');
};
const inicializarDatos = async () => {
    try {
        const [resAsistencias, resGrupos, resDocentes] = await Promise.all([
            axios.get('/api/asistencias'), 
            axios.get('/api/grupos'),
            axios.get('/api/docentes')
        ]);
        asistencias.value = resAsistencias.data;
        grupos.value = resGrupos.data;
        docentes.value = resDocentes.data;
    } catch (error) { console.error("Error al cargar datos:", error); }
};

const formatearFecha = (fechaString) => {
    const opciones = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(fechaString + 'T00:00:00').toLocaleDateString('es-MX', opciones);
};

const abrirModalCrear = () => {
    nuevaAsistencia.value = { fecha: fechaHoy, grupo_id: '', docente_id: '', estado: 'Asistió', docente_sustituto_id: '', observaciones: '' };
    errores.value = {};
    mostrarModal.value = true;
};

const cerrarModal = () => { mostrarModal.value = false; };

const guardarAsistencia = async () => {
    errores.value = {};
    enviando.value = true;
    
    // Si no es sustitución, limpiamos el campo por seguridad antes de enviarlo
    if (nuevaAsistencia.value.estado !== 'Sustitución') {
        nuevaAsistencia.value.docente_sustituto_id = null;
    }

    try {
        await axios.post('/api/asistencias', nuevaAsistencia.value);
        const resAsistencias = await axios.get('/api/asistencias');
        asistencias.value = resAsistencias.data;
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

const eliminarAsistencia = async (id) => {
    if (!confirm('¿Seguro que deseas eliminar este registro?')) return; 
    try {
        await axios.delete(`/api/asistencias/${id}`);
        asistencias.value = asistencias.value.filter(a => a.id !== id);
    } catch (error) {
        alert("Error al eliminar.");
    }
};

onMounted(inicializarDatos);
</script>

<style scoped>
/* Hereda tus estilos de Grupos.vue y agrega estos para las insignias de asistencia */
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.title-group h1 { color: var(--ugm-dark); font-size: 1.8rem; margin: 0; }
.badge-count { background: #f0f2f5; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; color: #666; font-weight: bold; }

.filter-bar { margin-bottom: 20px; display: flex; gap: 15px; }
.search-box { position: relative; width: 100%; max-width: 400px; }
.search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6; }
.search-box input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #e0e4e8; border-radius: 8px; font-size: 0.95rem; }

.id-cell { font-weight: bold; color: #576574; }
.item-info { display: flex; flex-direction: column; gap: 4px; }
.main-text { font-weight: 700; color: #2c3e50; font-size: 1rem; }
.text-muted { color: #bdc3c7; margin-right: 5px; }

.docente-pill { background: #f8f9fa; border: 1px solid #e9ecef; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; color: #495057; display: inline-block; }
.sustituto-pill { background: #fcf1f1; border-color: #fbdada; color: #c0392b; }

/* Insignias de Estado Dinámicas */
.estado-badge { padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: bold; display: inline-block; }
.estado-asistio { background: #e3fbed; color: #27ae60; }
.estado-falta { background: #fdeaea; color: #e74c3c; }
.estado-retardo { background: #fef5e7; color: #e67e22; }
.estado-sustitucion { background: #f4ecf8; color: #8e44ad; }

.actions-cell { text-align: center; white-space: nowrap; }
.btn-icon.delete { color: #e74c3c; background: none; border: none; cursor: pointer; font-size: 1.1rem; }

.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(4px); }
.modal-card { background: #fff; width: 100%; max-width: 600px; border-radius: 12px; padding: 30px; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
.modal-header h2 { margin: 0; font-size: 1.4rem; color: #2c3e50; }
.btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #95a5a6; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; font-size: 0.9rem;}
input, select { width: 100%; padding: 12px; border: 1px solid #dcdde1; border-radius: 6px; box-sizing: border-box;}
.input-error { border-color: #e74c3c; }
.text-error { color: #e74c3c; font-size: 0.8rem; margin-top: 5px; display: block; }

.modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;}
.btn-guardar { background-color: var(--ugm-red, #c0392b); color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
.btn-cancelar { background-color: #f1f2f6; color: #576574; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }

.btn-create { background: var(--ugm-red, #c0392b); color: #fff; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; }
.btn-excel-top { background: #27ae60; color: #fff; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; }
.btn-excel-top:hover { background: #219653; box-shadow: 0 4px 10px rgba(39, 174, 96, 0.2); }

.sin-datos { text-align: center; color: #95a5a6; padding: 40px 20px; }
.empty-icon { font-size: 3rem; margin-bottom: 15px; color: #bdc3c7; }
</style>