<template>
  <div class="module-container">
    <div class="module-header">
      <div class="title-group">
        <h1>Asignación de Horarios</h1>
        <span class="badge-count" v-if="horarios">
          <i class="fa-solid fa-layer-group"></i> {{ horarios.length }} Registrados
        </span>
      </div>
      
      <div class="header-actions">
        <div class="segmented-control">
          <button @click="vistaActual = 'tabla'" :class="{ active: vistaActual === 'tabla' }">
            <i class="fa-solid fa-list-ul"></i> <span>Lista  </span>
          </button>
          
          <button @click="vistaActual = 'calendario'" :class="{ active: vistaActual === 'calendario' }">
            <i class="fa-solid fa-calendar-days"></i> <span>    Calendario</span>
          </button>
        </div>

        <button @click="abrirModalCrear" class="btn-primary">
          <i class="fa-solid fa-plus"></i> Asignar Horario
        </button>
        <button @click="exportarExcel" class="btn-excel">
          <i class="fa-solid fa-file-excel"></i> Excel
        </button>
      </div>
    </div>

    <div v-if="errorEmpalme" class="alert-conflict-card fade-in">
      <div class="alert-icon-wrapper"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <div class="alert-body">
        <strong>¡Conflicto de Horario Detectado!</strong>
        <p>{{ errorEmpalme }}</p>
      </div>
      <button class="alert-close-btn" @click="errorEmpalme = ''"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="filter-bar-enhanced">
      <div class="search-box-enhanced" v-if="vistaActual === 'tabla'">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" v-model="busqueda" placeholder="Buscar día, grupo o docente..." />
      </div>

      <div class="calendar-space-picker" v-else>
        <label><i class="fa-solid fa-door-open"></i> Mostrando espacio:</label>
        <select v-model="espacioSeleccionado">
          <option value="">-- Todos los espacios (Vista General) --</option>
          <option v-for="e in espacios" :key="e.id" :value="e.id">
       {{ e.nombre }}
 </option>
        </select>
      </div>
    </div>

    <div class="view-viewport">
      
      <div v-if="vistaActual === 'tabla'" class="table-container-modern fade-in">
        <table class="modern-table" v-if="cargando || (listaFiltrada && listaFiltrada.length > 0)">
          <thead>
            <tr>
              <th><i class="fa-solid fa-calendar-day"></i> Día y Hora</th>
              <th><i class="fa-solid fa-users-rectangle"></i> Grupo / Actividad</th>
              <th><i class="fa-solid fa-chalkboard-user"></i> Docente Asignado</th>
              <th><i class="fa-solid fa-door-open"></i> Ubicación</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody v-if="cargando">
            <tr v-for="n in 5" :key="'skeleton-'+n">
              <td colspan="5"><div class="skeleton-shimmer width-full"></div></td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr v-for="horario in listaFiltrada" :key="horario.id">
              <td>
                <div class="data-cell-datetime">
                  <span class="day-text">{{ horario.dia_semana }}</span>
                  <span class="time-pill-enhanced"><i class="fa-regular fa-clock"></i> {{ formatearHoraVista(horario.hora_inicio) }} - {{ formatearHoraVista(horario.hora_fin) }}</span>
                </div>
              </td>
              <td>
                <div class="data-cell-group">
                  <span class="group-name">{{ horario.grupo?.nombre }}</span>
                  <span class="act-badge">{{ horario.grupo?.actividad?.nombre }}</span>
                </div>
              </td>
              <td>
                <div class="docente-pill-enhanced">
                  <i class="fa-solid fa-user-tie icon-muted"></i> {{ horario.grupo?.docente?.nombre }} {{ horario.grupo?.docente?.apellidos }}
                </div>
              </td>
              <td><span class="space-badge-enhanced"><i class="fa-solid fa-location-dot"></i> {{ horario.espacio?.nombre }}</span></td>
              <td class="actions-cell">
                <button @click="eliminarHorario(horario.id)" class="btn-icon-danger" title="Eliminar este Horario">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="!cargando && listaFiltrada.length === 0" class="empty-state-card">
          <i class="fa-solid fa-calendar-circle-exclamation empty-icon-enhanced"></i>
          <h3>No hay horarios que mostrar</h3>
          <p>Usa el buscador o cambia los criterios para encontrar registros.</p>
        </div>
      </div>

      <div v-else class="calendar-viewport-modern fade-in">
        <div class="calendar-grid-modern">
          <div class="time-col">
            <div class="grid-cell header-cell">Hora</div>
            <div v-for="hora in horasGrid" :key="hora" class="grid-cell time-label-cell">{{ hora }}</div>
          </div>
          
          <div v-for="dia in diasSemana" :key="dia" class="day-col">
            <div class="grid-cell header-cell">{{ dia }}</div>
            <div v-for="hora in horasGrid" :key="dia+hora" class="grid-cell slot-cell">
              <div v-for="evento in obtenerEventosCelda(dia, hora)" :key="evento.id" 
                   class="event-card-modern" :title="evento.grupo?.nombre">
                <div class="event-card-inner">
                  <span class="ev-title">{{ evento.grupo?.nombre }}</span>
                  <span class="ev-subtitle" v-if="!espacioSeleccionado"><i class="fa-solid fa-location-dot"></i> {{ evento.espacio?.nombre }}</span>
                  <span class="ev-meta"><i class="fa-regular fa-clock"></i> {{ formatearHoraVista(evento.hora_inicio) }} - {{ formatearHoraVista(evento.hora_fin) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div v-if="mostrarModal" class="modal-overlay" @click.self="cerrarModal">
      <div class="modal-card">
        <div class="modal-header">
          <h2><i class="fa-solid fa-calendar-check title-icon"></i> Asignar Nuevo Horario</h2>
          <button @click="cerrarModal" class="btn-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form @submit.prevent="guardarHorario" class="modal-form">
          <div class="form-grid">
            
            <div class="form-group">
              <label>Grupo a Asignar:</label>
              <select v-model="nuevoHorario.grupo_id" :class="{'input-error': errores.grupo_id}">
                <option value="" disabled>Seleccione el grupo...</option>
                <option v-for="grupo in grupos" :key="grupo.id" :value="grupo.id">
                  {{ grupo.nombre }}
                </option>
              </select>
              <span class="text-error" v-if="errores.grupo_id">{{ errores.grupo_id[0] }}</span>
            </div>

            <div class="form-group">
              <label>Espacio (Salón/Cancha):</label>
              <select v-model="nuevoHorario.espacio_id" :class="{'input-error': errores.espacio_id}">
                <option value="" disabled>Seleccione el espacio...</option>
                <option v-for="espacio in espacios" :key="espacio.id" :value="espacio.id">
                  {{ espacio.nombre }}
                </option>
              </select>
              <span class="text-error" v-if="errores.espacio_id">{{ errores.espacio_id[0] }}</span>
            </div>

            <div class="form-group full-width">
              <label>Días de la semana:</label>
              <div class="dias-grid">
                <label v-for="dia in diasSemana" :key="dia" class="dia-pill" :class="{ 'activo': nuevoHorario.dias.includes(dia) }">
                  <input type="checkbox" :value="dia" v-model="nuevoHorario.dias" class="hidden-checkbox"> {{ dia }}
                </label>
              </div>
              <span class="text-error" v-if="errores.dias">{{ errores.dias[0] }}</span>
            </div>

            <div class="form-group">
              <label>Hora de Inicio:</label>
              <flat-pickr v-model="nuevoHorario.hora_inicio" :config="configHora" class="modern-time-input" :class="{'input-error': errores.hora_inicio}" placeholder="¿A qué hora empieza?"></flat-pickr>
            </div>

            <div class="form-group">
              <label>Hora de Fin:</label>
              <flat-pickr v-model="nuevoHorario.hora_fin" :config="configHora" class="modern-time-input" :class="{'input-error': errores.hora_fin}" placeholder="¿A qué hora termina?"></flat-pickr>
            </div>
            
          </div>

          <div class="modal-footer">
            <button type="button" @click="cerrarModal" class="btn-cancelar">Cancelar</button>
            <button type="submit" class="btn-guardar" :disabled="enviando">
               <i :class="enviando ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-calendar-check'"></i>
              {{ enviando ? 'Validando...' : 'Asignar Horario' }}
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
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import { Spanish } from 'flatpickr/dist/l10n/es.js';
import Swal from 'sweetalert2';

// VARIABLES DE ESTADO
const vistaActual = ref('tabla'); // 'tabla' o 'calendario'
const espacioSeleccionado = ref('');
const horarios = ref([]);
const grupos = ref([]);
const espacios = ref([]);
const busqueda = ref('');
const mostrarModal = ref(false);
const errores = ref({});
const errorEmpalme = ref(''); 
const enviando = ref(false);
const cargando = ref(true); 

// CONSTANTES PARA EL GRID
const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
// Grid de horas (cada bloque representa una hora)
const horasGrid = ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00','21:00','22:00'];

const nuevoHorario = ref({ grupo_id: '', espacio_id: '', dias: [], hora_inicio: '', hora_fin: '' });

const configHora = ref({
  enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true, locale: Spanish, minuteIncrement: 15
});

// LÓGICA DEL CALENDARIO: Obtiene eventos que inician en un bloque de hora específico
const obtenerEventosCelda = (dia, hora) => {
  if (!horarios.value) return [];
  
  return horarios.value.filter(h => {
    // 1. Validar que sea el mismo día
    const mismoDia = h.dia_semana === dia;
    
    // 2. Lógica de Rango (Convertimos a minutos para precisión exacta)
    const bloqueH = parseInt(hora.substring(0, 2));
    const bloqueMinInicio = bloqueH * 60;
    const bloqueMinFin = bloqueMinInicio + 59; // El bloque de celda dura 59 minutos (ej. 07:00 a 07:59)
    
    // Extraemos los minutos de inicio de la clase
    const inicioH = parseInt(h.hora_inicio.substring(0, 2));
    const inicioM = parseInt(h.hora_inicio.substring(3, 5));
    const eventoMinInicio = (inicioH * 60) + inicioM;
    
    // Extraemos los minutos de fin de la clase
    const finH = parseInt(h.hora_fin.substring(0, 2));
    const finM = parseInt(h.hora_fin.substring(3, 5));
    const eventoMinFin = (finH * 60) + finM;
    
    // Validamos si la clase "cruza" o "pisa" los minutos de este bloque del calendario
    const enEsteBloque = eventoMinInicio <= bloqueMinFin && eventoMinFin > bloqueMinInicio;

    // 3. Si hay filtro de espacio, aplicarlo
    const coincideEspacio = espacioSeleccionado.value === '' || h.espacio_id == espacioSeleccionado.value;
    
    return mismoDia && enEsteBloque && coincideEspacio;
  });
};

// LISTA FILTRADA PARA LA TABLA
const listaFiltrada = computed(() => {
  if (!horarios.value) return [];
  const termino = busqueda.value.toLowerCase();
  return horarios.value.filter(h => 
    h.dia_semana?.toLowerCase().includes(termino) ||
    h.grupo?.nombre?.toLowerCase().includes(termino) ||
    h.espacio?.nombre?.toLowerCase().includes(termino) ||
    (h.grupo?.docente?.nombre + ' ' + h.grupo?.docente?.apellidos).toLowerCase().includes(termino)
  );
});

const formatearHoraVista = (hora) => hora ? hora.substring(0, 5) : '';

const inicializarDatos = async () => {
  cargando.value = true;
  try {
    const [resHorarios, resGrupos, resEspacios] = await Promise.all([
      axios.get('/api/horarios'),
      axios.get('/api/grupos'),
      axios.get('/api/espacios')
    ]);
    horarios.value = Array.isArray(resHorarios.data) ? resHorarios.data : [];
    grupos.value = resGrupos.data;
    espacios.value = resEspacios.data;
  } catch (error) { 
    console.error("Error cargando datos:", error);
  } finally { cargando.value = false; }
};

const abrirModalCrear = () => {
  nuevoHorario.value = { grupo_id: '', espacio_id: '', dias: [], hora_inicio: '', hora_fin: '' };
  errores.value = {}; errorEmpalme.value = ''; mostrarModal.value = true;
};

const cerrarModal = () => mostrarModal.value = false;

const guardarHorario = async () => {
  errores.value = {}; errorEmpalme.value = ''; enviando.value = true;
  try {
    await axios.post('/api/horarios', nuevoHorario.value);
    const res = await axios.get('/api/horarios');
    horarios.value = res.data;
    cerrarModal();
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Horario guardado correctamente', timer: 2500, showConfirmButton: false });
  } catch (error) {
    if (error.response?.status === 422) errores.value = error.response.data.errors;
    if (error.response?.status === 409) {
      errorEmpalme.value = error.response.data.mensaje;
      Swal.fire({ icon: 'warning', title: '¡Conflicto!', text: error.response.data.mensaje, confirmButtonColor: '#e67e22' });
    }
  } finally { enviando.value = false; }
};

const eliminarHorario = async (id) => {
  const result = await Swal.fire({ 
    title: '¿Liberar este horario?', 
    text: "Esta acción no se puede deshacer.",
    icon: 'warning', 
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#c0392b',
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/horarios/${id}`);
      horarios.value = horarios.value.filter(h => h.id !== id);
      Swal.fire('Eliminado', 'El horario ha sido liberado.', 'success');
    } catch (error) { console.error(error); Swal.fire('Error', 'No se pudo eliminar el horario.', 'error'); }
  }
};

const exportarExcel = async () => {
    try {
        // Pedimos el archivo a la ruta protegida asegurando que Axios envíe las credenciales
        const response = await axios.get('/api/reportes/horarios', {
            responseType: 'blob' // Fundamental para que no lo lea como texto/JSON
        });

        // Creamos un enlace temporal en memoria para forzar la descarga
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        
        // Le damos un nombre al archivo
        link.setAttribute('download', 'Reporte_Horarios.xlsx'); 
        document.body.appendChild(link);
        link.click();
        
        // Limpiamos la memoria
        link.remove();
        window.URL.revokeObjectURL(url);

    } catch (error) {
        console.error("Error descargando el archivo:", error);
        Swal.fire('Error', 'No tienes permisos o la sesión expiró', 'error');
    }
};

onMounted(inicializarDatos);
</script>

<style scoped>
/* =========================================
   ESTILOS RENOVADOS PROFESIONALES (UI UGM)
   ========================================= */

/* --- Variables de Diseño --- */
:root {
  --ugm-primary: #D1101A; /* Rojo Institucional */
  --ugm-dark: #2c3e50;
  --bg-main: #f8fabb;
  --bg-card: #ffffff;
  --text-muted: #95a5a6;
  --border-subtle: #ebedf0;
  --shadow-subtle: 0 4px 12px rgba(0,0,0,0.05);
  --shadow-card: 0 10px 25px rgba(0,0,0,0.1);
  --radius-md: 10px;
  --radius-sm: 6px;
}

/* --- Contenedor General --- */
.module-container {
  padding: 30px;
  background-color: var(--bg-main);
  min-height: 100vh;
  font-family: 'Inter', -apple-system, sans-serif;
}

/* --- Encabezado Pulido --- */
.module-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border-subtle);
}

.title-group h1 {
  color: var(--ugm-dark);
  font-size: 2.2rem;
  font-weight: 800;
  margin: 0;
  letter-spacing: -1px;
}

.badge-count {
  background: var(--bg-card);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.9rem;
  color: #7f8c8d;
  font-weight: 600;
  margin-top: 8px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  box-shadow: var(--shadow-subtle);
}

.header-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.segmented-control {
  display: flex;
  background-color: #e2e8f0;
  padding: 4px;
  border-radius: 30px;
  gap: 5px; /* Separación interna entre los botones de Lista y Calendario */
  margin-right: 15px; /* Separación externa respecto al botón "Asignar Horario" */
}

.btn-toggle {
  background: none;
  border: none;
  padding: 10px 20px;
  border-radius: 25px;
  font-weight: 600;
  color: #7f8c8d;
  cursor: pointer;
  transition: 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
}

.btn-toggle:hover {
  color: var(--ugm-dark);
}

.btn-toggle.active {
  background-color: var(--bg-card);
  color: var(--ugm-primary);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* --- Botones --- */
.btn-primary {
  background-color: #D1101A !important; /* Rojo UGM forzado */
  color: #ffffff !important;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 6px rgba(209, 16, 26, 0.2);
  visibility: visible !important; /* Asegura visibilidad */
  opacity: 1 !important;
}

.btn-primary:hover {
  background-color: #b00d15 !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(209, 16, 26, 0.3);
}

.btn-primary:active {
  transform: translateY(0);
}

/* Estilo para los iconos dentro del botón */
.btn-primary i {
  font-size: 1.1rem;
}
.btn-excel {
  background-color: #27ae60;
  color: white;
  border: none;
  padding: 12px 22px;
  border-radius: var(--radius-sm);
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-excel:hover {
  background-color: #219653;
  transform: translateY(-2px);
}

/* --- Alerta Conflicto Rediseñada --- */
.alert-conflict-card {
  background: #fff;
  border-radius: var(--radius-md);
  padding: 20px;
  margin-bottom: 25px;
  display: flex;
  align-items: center;
  gap: 18px;
  box-shadow: 0 6px 20px rgba(231, 76, 60, 0.1);
  border-left: 6px solid #e74c3c;
  position: relative;
}

.alert-icon-wrapper {
  font-size: 1.8rem;
  color: #e74c3c;
}

.alert-body strong {
  display: block;
  color: #c0392b;
  font-size: 1.1rem;
  margin-bottom: 4px;
}

.alert-body p {
  margin: 0;
  color: #7f8c8d;
  font-size: 0.95rem;
}

.alert-close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: none;
  border: none;
  color: #bdc3c7;
  font-size: 1.3rem;
  cursor: pointer;
  transition: 0.2s;
}

.alert-close-btn:hover {
  color: #e74c3c;
}

/* --- Barra de Filtros Pulida --- */
.filter-bar-enhanced {
  background-color: var(--bg-card);
  padding: 20px;
  border-radius: var(--radius-md);
  margin-bottom: 25px;
  box-shadow: var(--shadow-subtle);
  display: flex;
  align-items: center;
}

.search-box-enhanced {
  position: relative;
  width: 100%;
  max-width: 450px;
}

.search-icon {
  position: absolute;
  left: 18px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.search-box-enhanced input {
  width: 100%;
  padding: 14px 14px 14px 50px;
  border: 1px solid #e0e6ed;
  border-radius: 30px;
  font-size: 1rem;
  background: #f8fafc;
  transition: 0.3s;
}

.search-box-enhanced input:focus {
  outline: none;
  border-color: var(--ugm-primary);
  background: #fff;
  box-shadow: 0 0 0 4px rgba(209, 16, 26, 0.1);
}

.calendar-space-picker {
  display: flex;
  align-items: center;
  gap: 15px;
  width: 100%;
}

.calendar-space-picker label {
  font-weight: 600;
  color: var(--ugm-dark);
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  gap: 8px;
}

.calendar-space-picker select {
  flex-grow: 1;
  max-width: 400px;
  padding: 12px;
  border-radius: var(--radius-sm);
  border: 1px solid #e0e6ed;
  background: #f8fafc;
  cursor: pointer;
}

/* --- Tabla Profesional --- */
.table-container-modern {
  background: var(--bg-card);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-subtle);
  overflow: hidden;
  width: 100%;
  overflow-x: auto;
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 1000px;
}

.modern-table thead tr {
  background-color: #f8fafc;
  border-bottom: 2px solid var(--border-subtle);
}

.modern-table th {
  padding: 18px;
  text-align: left;
  color: #7f8c8d;
  font-weight: 700;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.modern-table th i {
  margin-right: 6px;
  color: #bdc3c7;
}

.modern-table tbody tr {
  border-bottom: 1px solid var(--border-subtle);
  transition: 0.2s;
}

.modern-table tbody tr:hover {
  background-color: #fdfdfd;
}

.modern-table td {
  padding: 18px;
  vertical-align: middle;
}

/* Celdas de Datos */
.data-cell-datetime {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.day-text {
  font-weight: 700;
  color: var(--ugm-dark);
  font-size: 1.05rem;
}

.time-pill-enhanced {
  background: #fff9f1;
  color: #e67e22;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: bold;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  border: 1px solid #ffebcc;
  width: fit-content;
}

.data-cell-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.group-name {
  font-weight: 600;
  color: var(--ugm-dark);
}

.act-badge {
  background: #ebf5ff;
  color: #3182ce;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: bold;
  width: fit-content;
  border: 1px solid #cce5ff;
}

.docente-pill-enhanced {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  padding: 8px 14px;
  border-radius: 30px;
  font-size: 0.9rem;
  color: var(--ugm-dark);
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
}

.icon-muted {
  color: #94a3b8;
}

.space-badge-enhanced {
  background: #fdf2f2;
  border: 1px solid #f9d5d5;
  padding: 8px 14px;
  border-radius: var(--radius-sm);
  font-size: 0.9rem;
  color: var(--ugm-primary);
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-icon-danger {
  background: none;
  border: none;
  color: #e74c3c;
  font-size: 1.1rem;
  cursor: pointer;
  padding: 10px;
  border-radius: 50%;
  transition: 0.2s;
}

.btn-icon-danger:hover {
  background-color: #fff0f0;
  transform: scale(1.15);
}

/* --- Calendario Visual Moderno --- */
.calendar-viewport-modern {
  background: var(--bg-card);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-subtle);
  overflow: hidden;
  padding: 15px;
}

.calendar-grid-modern {
  display: grid;
  grid-template-columns: 80px repeat(6, 1fr);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-sm);
  background: #fff;
  overflow: hidden;
}

/* Columnas */
.time-col, .day-col {
  display: flex;
  flex-direction: column;
}

.day-col {
  border-left: 1px solid var(--border-subtle);
}

/* Celdas del Grid */
.grid-cell {
  height: 80px; /* Altura para cada bloque de hora */
  border-bottom: 1px solid #f1f3f5;
  padding: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.header-cell {
  height: 60px; /* Cabeceras más cortas */
  background: var(--ugm-primary);
  color: white;
  font-weight: 800;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 1px;
  border-bottom: none;
}

.time-label-cell {
  background: #f8fafc;
  color: #7f8c8d;
  font-weight: 700;
  font-size: 0.9rem;
  border-right: 1px solid var(--border-subtle);
}

.slot-cell {
  flex-direction: column;
  justify-content: flex-start;
  gap: 4px;
  overflow-y: auto; /* Por si se enciman muchos eventos */
  background: #fff;
}

.slot-cell:hover {
  background-color: #fcfcfd;
}

/* Tarjeta de Evento Moderno */
.event-card-modern {
  background: rgba(209, 16, 26, 0.06);
  border-left: 4px solid var(--ugm-primary);
  border-radius: 4px;
  width: 100%;
  padding: 6px 8px;
  box-sizing: border-box;
  cursor: pointer;
  transition: 0.2s;
}

.event-card-modern:hover {
  transform: translateY(-2px);
  background: rgba(209, 16, 26, 0.1);
  box-shadow: 0 4px 8px rgba(209, 16, 26, 0.1);
}

.event-card-inner {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.ev-title {
  font-weight: 700;
  color: var(--ugm-primary);
  font-size: 0.8rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.ev-subtitle {
  color: var(--ugm-dark);
  font-size: 0.7rem;
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: 500;
}

.ev-subtitle i {
  font-size: 0.65rem;
  opacity: 0.7;
}

.ev-time {
  background: #fff;
  color: #e67e22;
  font-size: 0.65rem;
  font-weight: bold;
  padding: 1px 4px;
  border-radius: 3px;
  width: fit-content;
  margin-top: 2px;
}

/* --- Estados Vacíos --- */
.empty-state-card {
  text-align: center;
  color: #95a5a6;
  padding: 80px 30px;
}

.empty-icon-enhanced {
  font-size: 4rem;
  margin-bottom: 20px;
  color: #e2e8f0;
}

.empty-state-card h3 {
  color: var(--ugm-dark);
  margin-bottom: 8px;
}

/* --- SKELETON PULIDO --- */
.skeleton-shimmer {
  height: 25px;
  background: linear-gradient(90deg, #f1f5f9 25%, #f8fafc 50%, #f1f5f9 75%);
  background-size: 200% 100%;
  animation: shimmer-animation 1.5s infinite;
  border-radius: 4px;
}

@keyframes shimmer-animation {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* --- ANIMACIONES --- */
.fade-in {
  animation: modalFadeIn 0.4s ease forwards;
}

@keyframes modalFadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* --- MODAL PULIDO (Mantenido funcional, estilos mejorados) --- */
.modal-overlay {
    position: fixed; /* Fundamental: se posiciona respecto a la ventana, no al div padre */
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(15, 23, 42, 0.7); /* Fondo oscuro semitransparente */
    backdrop-filter: blur(4px); /* Efecto de desenfoque muy moderno */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999 !important; /* El "Rey" de la página */
    padding: 20px;
}
.modal-card {
    background: #ffffff;
    width: 100%;
    max-width: 650px; /* Un poco más ancho para las 2 columnas */
    border-radius: 12px;
    padding: 35px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    position: relative;
    z-index: 10000; 
    animation: modalSlideUp 0.3s ease-out;
}

@keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ebedf0;
    padding-bottom: 20px;
    margin-bottom: 25px;
}
.modal-header h2 {
    font-size: 1.6rem;
    color: #2c3e50;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
}
.title-icon { color: #2c3e50; font-size: 1.4rem; }

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #95a5a6;
    cursor: pointer;
    transition: 0.2s;
}
.btn-close:hover { color: #e74c3c; }

/* FORM GRID (2 Columnas exactas) */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group.full-width { grid-column: span 2; }

label {
    display: block;
    color: #2c3e50; /* Color oscuro/azulado de la UGM */
    font-weight: 700; /* Negrita como en la foto */
    font-size: 0.95rem;
    margin-bottom: 8px;
}

select, .modern-time-input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 6px;
    border: 1px solid #dcdde1;
    background: #ffffff;
    font-size: 0.95rem;
    color: #34495e;
    transition: all 0.2s;
}

select:focus, .modern-time-input:focus {
    border-color: #D1101A;
    outline: none;
    box-shadow: 0 0 0 3px rgba(209,16,26,0.1);
}

.input-error { border-color: #e74c3c; background-color: #fff6f6; }
.text-error { color: #e74c3c; font-weight: 500; font-size: 0.8rem; margin-top: 5px; display: block; }

/* DIAS PILLS */
.dias-grid { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 10px; 
    margin-top: 5px; 
}
.dia-pill { 
    padding: 8px 18px; 
    border: 1px solid #dcdde1; 
    border-radius: 20px; 
    cursor: pointer; 
    transition: all 0.2s ease; 
    font-weight: 600; 
    color: #2c3e50; 
    background-color: #ffffff; 
    user-select: none; /* Evita que el texto se sombree de azul al hacer doble clic */
}
dia-pill.activo { 
    background-color: #D1101A !important; /* Fuerza el fondo rojo */
    color: #ffffff !important;            /* Fuerza el texto blanco */
    border-color: #D1101A !important;     /* Borde rojo para que no se vea gris */
    box-shadow: 0 4px 10px rgba(209, 16, 26, 0.25); 
}

.hidden-checkbox { 
    display: none; 
}

/* FOOTER BUTTONS (Como en "Pase de Lista") */
.modal-footer {
    margin-top: 30px;
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    border-top: 1px solid #ebedf0;
    padding-top: 25px;
}

.btn-cancelar {
    background-color: #f1f2f6;
    color: #576574;
    padding: 12px 25px;
    border-radius: 6px;
    border: none;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s;
}

.btn-cancelar:hover { background-color: #dfe4ea; }

.btn-guardar {
    background-color: #D1101A;
    color: #fff;
    padding: 12px 30px;
    border-radius: 6px;
    border: none;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s;
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-guardar:hover:not(:disabled) {
    background-color: #b00d15;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(209,16,26,0.2);
}

.btn-guardar:disabled { opacity: 0.7; cursor: not-allowed; }
label {
  color: #475569;
  font-weight: 600;
  font-size: 0.9rem;
}

input, select {
  padding: 14px;
  border-radius: var(--radius-sm);
  background: #fdfdfd;
}

input:focus, select:focus {
  border-color: var(--ugm-primary);
  box-shadow: 0 0 0 4px rgba(209, 16, 26, 0.1);
}

.input-error {
  border-color: #ef4444;
}

.text-error {
  color: #ef4444;
  font-weight: 500;
}

.dia-pill.activo {
  background-color: var(--ugm-primary);
  border-color: var(--ugm-primary);
}

/* --- RESPONSIVE PULIDO --- */
@media (max-width: 900px) {
  .title-group h1 { font-size: 1.8rem; }
  .header-actions { flex-wrap: wrap; justify-content: flex-start; width: 100%; }
  .segmented-control { width: 100%; justify-content: center; order: 3; }
  
  .filter-bar-enhanced { flex-direction: column; align-items: flex-start; gap: 15px; }
  .search-box-enhanced { max-width: 100%; }

  .calendar-grid-modern { grid-template-columns: 70px repeat(6, 120px); overflow-x: auto; }
}
</style>