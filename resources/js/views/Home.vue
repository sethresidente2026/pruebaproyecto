<template>
  <div class="dashboard-container">
    
    <header class="dashboard-header">
      <div class="header-text">
        <h1>Bienvenido, Coordinador</h1>
        <p>Sistema de Gestión Académica - Rectoría Centro UGM</p>
      </div>
    </header>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="icon-box bg-blue"><i class="fa-solid fa-user-tie"></i></div>
        <div class="info">
          <h3>{{ totales.docentes }} Docentes</h3>
          <p>Plantilla Activa</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="icon-box bg-green"><i class="fa-solid fa-building"></i></div>
        <div class="info">
          <h3>{{ totales.espacios }} Espacios</h3>
          <p>Aulas y Canchas</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="icon-box bg-purple"><i class="fa-solid fa-users-rectangle"></i></div>
        <div class="info">
          <h3>{{ totales.grupos }} Grupos</h3>
          <p>Ciclo Actual</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="icon-box bg-orange"><i class="fa-solid fa-calendar-check"></i></div>
        <div class="info">
          <h3>{{ totales.horarios }} Horarios</h3>
          <p>Asignaciones Totales</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="icon-box bg-ugm-red"><i class="fa-solid fa-clipboard-check"></i></div>
        <div class="info">
          <h3>{{ totales.asistencias }} Asistencias</h3>
          <p>Registradas Hoy</p>
        </div>
      </div>
    </div>

    <div class="report-section">
      <div class="section-header">
        <h2><i class="fa-solid fa-chart-pie"></i> Reportes Operativos</h2>
        <p>Descarga la información consolidada del ciclo escolar para auditoría y toma de decisiones.</p>
      </div>
      
      <div class="action-buttons">
        <button @click="descargarTodo" class="btn-main-excel">
          <i class="fa-solid fa-file-excel"></i> Generar Libro Excel Consolidado
        </button>
        
        <button @click="prepararPDF" class="btn-main-pdf">
          <i class="fa-solid fa-file-pdf"></i> Generar Reporte de Operación (PDF)
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Agregamos 'asistencias' al objeto de totales
const totales = ref({ docentes: 0, espacios: 0, grupos: 0, horarios: 0, asistencias: 0 });

const cargarTotales = async () => {
    try {
        const [resDoc, resEsp, resGru, resHor, resAsis] = await Promise.all([
            axios.get('/api/docentes'),
            axios.get('/api/espacios'),
            axios.get('/api/grupos'),
            axios.get('/api/horarios'),
            axios.get('/api/asistencias') 
        ]);
        
        // Filtramos las asistencias para contar solo las que se hicieron el día de hoy
        const hoy = new Date().toISOString().split('T')[0];
        const asistenciasHoy = resAsis.data.filter(a => a.fecha === hoy).length;

        totales.value = {
            docentes: resDoc.data.length,
            espacios: resEsp.data.length,
            grupos: resGru.data.length,
            horarios: resHor.data.length,
            asistencias: asistenciasHoy
        };
    } catch (error) {
        console.error("Error al cargar estadísticas:", error);
    }
};

const descargarTodo = () => { 
    // Asegúrate de que esta ruta apunte a tu ConsolidadoExport que incluye los Pagos
    window.open('/api/reporte-general', '_blank'); 
};

const prepararPDF = () => {
    window.open('/api/reporte-pdf', '_blank'); 
};

onMounted(cargarTotales);
</script>

<style scoped>
/* Contenedor Principal */
.dashboard-container { padding: 10px; }

/* Encabezado */
.dashboard-header { margin-bottom: 30px; border-bottom: 2px solid #e0e4e8; padding-bottom: 20px; }
.dashboard-header h1 { color: var(--ugm-dark); font-size: 2.2rem; margin: 0 0 5px 0; }
.dashboard-header p { color: #7f8c8d; font-size: 1.1rem; margin: 0; }

/* Grid de Tarjetas Estadísticas */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 40px; }
.stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 20px; transition: transform 0.3s ease; }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.06); }

/* Íconos de colores con degradado */
.icon-box { width: 60px; height: 60px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 1.8rem; color: white; }
.bg-blue { background: linear-gradient(135deg, #3498db, #2980b9); }
.bg-green { background: linear-gradient(135deg, #2ecc71, #27ae60); }
.bg-purple { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
.bg-orange { background: linear-gradient(135deg, #f39c12, #d35400); }

/* Texto de la tarjeta */
.info h3 { margin: 0; font-size: 1.8rem; color: #2c3e50; }
.info p { margin: 0; color: #95a5a6; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;}

/* Sección de Reportes */
.report-section { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
.section-header { margin-bottom: 25px; }
.section-header h2 { margin: 0 0 10px 0; color: var(--ugm-dark); font-size: 1.5rem; display: flex; align-items: center; gap: 10px;}
.section-header p { margin: 0; color: #7f8c8d; }

/* Botones Grandes */
.action-buttons { display: flex; gap: 20px; flex-wrap: wrap; }
.btn-main-excel, .btn-main-pdf { flex: 1; padding: 18px 25px; border: none; border-radius: 10px; font-size: 1.1rem; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px; transition: all 0.3s ease; color: white; min-width: 300px; }

.btn-main-excel { background-color: #27ae60; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3); }
.btn-main-excel:hover { background-color: #219150; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4); }

.btn-main-pdf { background-color: var(--ugm-red); box-shadow: 0 4px 15px rgba(209, 16, 26, 0.3); }
.btn-main-pdf:hover { background-color: #b00d15; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(209, 16, 26, 0.4); }
.bg-ugm-red { background: linear-gradient(135deg, #d1101a, #b00d15); }

/* Ajuste opcional para que 5 tarjetas se acomoden mejor */
.stats-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
  gap: 15px; 
  margin-bottom: 40px; 
}
</style>