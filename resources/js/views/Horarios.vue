<template>
  <div class="module-container">
    <div class="module-header">
      <div class="title-group">
        <h1>Asignación de Horarios</h1>
        <span class="badge-count">{{ horarios.length }} Registrados</span>
      </div>
      
      <div class="header-actions">
        <button @click="abrirModalCrear" class="btn-create">
          <i class="fa-solid fa-calendar-plus"></i> Asignar Horario
        </button>
        <button @click="exportarExcel" class="btn-excel-top">
          <i class="fa-solid fa-file-excel"></i> Descargar Excel
        </button>
      </div>
    </div>

    <div v-if="errorEmpalme" class="alert-conflict fade-in">
      <div class="alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <div class="alert-content">
        <strong>¡Conflicto Detectado!</strong>
        <p>{{ errorEmpalme }}</p>
      </div>
      <button class="alert-close" @click="errorEmpalme = ''"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="filter-bar">
      <div class="search-box">
        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" v-model="busqueda" placeholder="Buscar por día, grupo o docente..." />
      </div>
    </div>

    <div class="table-responsive">
      <table class="custom-table" v-if="cargando || listaFiltrada.length > 0">
        <thead>
          <tr>
            <th>Día y Hora</th>
            <th>Grupo (Actividad)</th>
            <th>Docente</th>
            <th>Espacio</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        
        <tbody v-if="cargando">
          <tr v-for="n in 5" :key="'skeleton-'+n">
            <td>
              <div class="item-info">
                <div class="skeleton-box width-medium" style="margin-bottom: 5px;"></div>
                <div class="skeleton-box width-small" style="border-radius: 4px;"></div>
              </div>
            </td>
            <td>
              <div class="item-info">
                <div class="skeleton-box width-large" style="margin-bottom: 5px;"></div>
                <div class="skeleton-box width-medium" style="border-radius: 4px;"></div>
              </div>
            </td>
            <td><div class="skeleton-box width-large" style="border-radius: 8px;"></div></td>
            <td><div class="skeleton-box width-medium" style="border-radius: 8px;"></div></td>
            <td><div class="skeleton-box width-small" style="margin: 0 auto;"></div></td>
          </tr>
        </tbody>

        <tbody v-else>
          <tr v-for="horario in listaFiltrada" :key="horario.id">
            <td>
              <div class="item-info">
                <span class="main-text">
                  <i class="fa-regular fa-calendar-days text-muted"></i> 
                  {{ horario.dia_semana }}
                </span>
                <span class="time-badge">
                  <i class="fa-regular fa-clock"></i> {{ formatearHora(horario.hora_inicio) }} - {{ formatearHora(horario.hora_fin) }}
                </span>
              </div>
            </td>
            <td>
              <div class="item-info">
                <span class="main-text">{{ horario.grupo?.nombre }}</span>
                <span class="badge-level">{{ horario.grupo?.actividad?.nombre }}</span>
              </div>
            </td>
            <td>
              <div class="docente-pill">
                <i class="fa-solid fa-user-tie text-muted"></i> 
                {{ horario.grupo?.docente?.nombre }} {{ horario.grupo?.docente?.apellidos }}
              </div>
            </td>
            <td>
              <span class="space-badge">
                <i class="fa-solid fa-door-open"></i> {{ horario.espacio?.nombre }}
              </span>
            </td>
            <td class="actions-cell">
              <button @click="eliminarHorario(horario.id)" class="btn-icon delete" title="Liberar Horario">
                <i class="fa-solid fa-calendar-xmark"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="!cargando && listaFiltrada.length === 0" class="sin-datos">
        <i class="fa-solid fa-calendar-circle-exclamation empty-icon"></i>
        <p>No se encontraron horarios con esos criterios...</p>
      </div>
    </div>

    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal-card">
        <div class="modal-header">
          <h2>
            <i class="fa-solid fa-calendar-plus"></i>
            Asignar Nuevo Horario
          </h2>
          <button @click="cerrarModal" class="btn-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form @submit.prevent="guardarHorario" class="modal-form">
          <div class="form-grid">
            <div class="form-group full-width">
              <label>Grupo a Asignar:</label>
              <select v-model="nuevoHorario.grupo_id" :class="{'input-error': errores.grupo_id}">
                <option value="" disabled>Seleccione un grupo...</option>
                <option v-for="grupo in grupos" :key="grupo.id" :value="grupo.id">
                  {{ grupo.nombre }} (Docente: {{ grupo.docente?.nombre }})
                </option>
              </select>
              <span class="text-error" v-if="errores.grupo_id">{{ errores.grupo_id[0] }}</span>
            </div>

            <div class="form-group full-width">
              <label>Espacio (Salón/Cancha):</label>
              <select v-model="nuevoHorario.espacio_id" :class="{'input-error': errores.espacio_id}">
                <option value="" disabled>Seleccione un espacio...</option>
                <option v-for="espacio in espacios" :key="espacio.id" :value="espacio.id">
                  {{ espacio.nombre }} (Capacidad: {{ espacio.capacidad }})
                </option>
              </select>
              <span class="text-error" v-if="errores.espacio_id">{{ errores.espacio_id[0] }}</span>
            </div>

            <div class="form-group" style="grid-column: span 2;">
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

          <div class="modal-footer">
            <button type="button" @click="cerrarModal" class="btn-cancelar">Cancelar</button>
            <button type="submit" class="btn-guardar" :disabled="enviando">
               <i :class="enviando ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-calendar-check'"></i>
              {{ enviando ? 'Validando...' : 'Confirmar Horario' }}
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

// Variables
const horarios = ref([]);
const grupos = ref([]);
const espacios = ref([]);
const busqueda = ref('');
const mostrarModal = ref(false);

const nuevoHorario = ref({ grupo_id: '', espacio_id: '', dia_semana: '', hora_inicio: '', hora_fin: '' });
const errores = ref({});
const errorEmpalme = ref(''); 
const enviando = ref(false);
const cargando = ref(true); // 🔴 Variable para el Skeleton Loader

// Filtro Inteligente
const listaFiltrada = computed(() => {
  const termino = busqueda.value.toLowerCase();
  return horarios.value.filter(h => 
    h.dia_semana.toLowerCase().includes(termino) ||
    h.grupo?.nombre.toLowerCase().includes(termino) ||
    (h.grupo?.docente?.nombre + ' ' + h.grupo?.docente?.apellidos).toLowerCase().includes(termino)
  );
});

// Función útil para mostrar la hora sin segundos (ej: 08:00 en lugar de 08:00:00)
const formatearHora = (hora) => {
  if (!hora) return '';
  return hora.substring(0, 5);
};

const inicializarDatos = async () => {
    cargando.value = true;
    try {
        const [resHorarios, resGrupos, resEspacios] = await Promise.all([
            axios.get('/api/horarios'),
            axios.get('/api/grupos'),
            axios.get('/api/espacios')
        ]);
        
        horarios.value = resHorarios.data;
        grupos.value = resGrupos.data;
        espacios.value = resEspacios.data;
    } catch (error) { 
        console.error("Error al cargar datos:", error); 
    } finally {
        cargando.value = false;
    }
};

const abrirModalCrear = () => {
    nuevoHorario.value = { grupo_id: '', espacio_id: '', dia_semana: '', hora_inicio: '', hora_fin: '' };
    errores.value = {};
    errorEmpalme.value = '';
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
};

const guardarHorario = async () => {
    errores.value = {};
    errorEmpalme.value = ''; 
    enviando.value = true;

    try {
        await axios.post('/api/horarios', nuevoHorario.value);
        
        const resHorarios = await axios.get('/api/horarios');
        horarios.value = resHorarios.data;
        
        cerrarModal();

    } catch (error) {
        if (error.response) {
            if (error.response.status === 422) {
                errores.value = error.response.data.errors;
            } 
            else if (error.response.status === 409) {
                errorEmpalme.value = error.response.data.mensaje;
                cerrarModal(); // Cerramos el modal para que vea la alerta gigante
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

const exportarExcel = () => {
    window.location.href = '/api/reportes/horarios'; // 🔴 Descarga directa corregida
};

onMounted(inicializarDatos);
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

/* Alerta de Empalme */
.alert-conflict { background: #fff5f5; border-left: 5px solid #e74c3c; padding: 15px 20px; margin-bottom: 25px; border-radius: 6px; display: flex; align-items: flex-start; gap: 15px; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.1); position: relative; }
.alert-icon { font-size: 1.5rem; color: #e74c3c; margin-top: 2px; }
.alert-content strong { display: block; color: #c0392b; font-size: 1.05rem; margin-bottom: 4px; }
.alert-content p { margin: 0; color: #e74c3c; font-size: 0.95rem; }
.alert-close { position: absolute; top: 15px; right: 15px; background: none; border: none; color: #e74c3c; font-size: 1.2rem; cursor: pointer; opacity: 0.6; transition: 0.2s; }
.alert-close:hover { opacity: 1; transform: scale(1.1); }
.fade-in { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

/* Contenedor Responsivo para la Tabla */
.table-responsive { width: 100%; overflow-x: auto; background: #fff; border-radius: 8px; }
.custom-table { width: 100%; border-collapse: collapse; min-width: 800px; }

/* Tabla y Textos */
.item-info { display: flex; flex-direction: column; gap: 4px; }
.main-text { font-weight: 700; color: #2c3e50; font-size: 1rem; }
.text-muted { color: #bdc3c7; margin-right: 5px; }
.time-badge { background: #fdf2e9; color: #d35400; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; width: fit-content; margin-top: 2px;}
.badge-level { background: #ebf5ff; color: #007bff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; width: fit-content; margin-top: 2px;}
.docente-pill { background: #f8f9fa; border: 1px solid #e9ecef; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; color: #495057; display: inline-block; }
.space-badge { background: #eef2f5; border: 1px solid #dcdde1; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; color: #34495e; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }

.actions-cell { text-align: center; white-space: nowrap; }
.btn-icon { background: none; border: none; font-size: 1.1rem; cursor: pointer; padding: 5px 10px; transition: 0.2s; }
.btn-icon.delete { color: #e74c3c; }
.btn-icon:hover { transform: scale(1.2); }

/* Modal y Formulario */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(4px); padding: 15px;}
.modal-card { background: #fff; width: 100%; max-width: 550px; border-radius: 12px; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease; }
@keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
.modal-header h2 { margin: 0; font-size: 1.4rem; color: var(--ugm-dark); display: flex; align-items: center; gap: 10px; }
.btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #95a5a6; transition: color 0.2s; }
.btn-close:hover { color: #e74c3c; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.form-group.full-width { grid-column: span 2; }
label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; font-size: 0.9rem;}
input, select { width: 100%; padding: 12px; border: 1px solid #dcdde1; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem; background: #fafbfc;}
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
.btn-excel-top { background-color: #27ae60; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
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
.width-small { width: 50px; }
.width-medium { width: 100px; }
.width-large { width: 160px; }

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
    flex-direction: column;
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
  
  /* Formulario a 1 columna en móvil */
  .form-grid {
    grid-template-columns: 1fr;
  }
  .form-group[style*="grid-column: span 2"] {
    grid-column: span 1 !important;
  }
}
</style>