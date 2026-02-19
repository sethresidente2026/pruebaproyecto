<template>
  <div class="module-container">
    <div class="module-header">
      <div class="title-group">
        <h1>Gestión de Grupos</h1>
        <span class="badge-count">{{ grupos.length }} Registrados</span>
      </div>
      
      <div class="header-actions">
        <button @click="abrirModalCrear" class="btn-create">
          <i class="fa-solid fa-plus"></i> Nuevo Grupo
        </button>
        <button @click="exportarExcel" class="btn-excel-top">
          <i class="fa-solid fa-file-excel"></i> Descargar Excel
        </button>
      </div>
    </div>

    <div class="filter-bar">
      <div class="search-box">
        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" v-model="busqueda" placeholder="Buscar por nombre de grupo o docente..." />
      </div>
    </div>

    <div class="table-responsive">
      <table class="custom-table" v-if="listaFiltrada.length > 0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Información del Grupo</th>
            <th>Actividad / Nivel</th>
            <th>Docente Asignado</th>
            <th>Cupo</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="grupo in listaFiltrada" :key="grupo.id">
            <td class="id-cell">#{{ grupo.id }}</td>
            <td>
              <div class="item-info">
                <span class="main-text">
                  <i class="fa-solid fa-users-rectangle text-muted"></i> 
                  {{ grupo.nombre }}
                </span>
                <span class="sub-text">{{ grupo.ciclo?.nombre }}</span>
              </div>
            </td>
            <td>
              <div class="item-info">
                <span class="main-text">{{ grupo.actividad?.nombre }}</span>
                <span class="badge-level">{{ grupo.nivel?.nombre }}</span>
              </div>
            </td>
            <td>
              <div class="docente-pill">
                <i class="fa-solid fa-user-tie text-muted"></i> 
                {{ grupo.docente?.nombre }} {{ grupo.docente?.apellidos }}
              </div>
            </td>
            <td>
              <span class="capacidad-badge">
                <i class="fa-solid fa-chair"></i> {{ grupo.cupo_maximo }} lugares
              </span>
            </td>
            <td class="actions-cell">
              <button @click="cargarParaEditar(grupo)" class="btn-icon edit" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button @click="eliminarGrupo(grupo.id)" class="btn-icon delete" title="Eliminar">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="sin-datos">
        <i class="fa-solid fa-folder-open empty-icon"></i>
        <p>No se encontraron grupos registrados...</p>
      </div>
    </div>

    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal-card">
        <div class="modal-header">
          <h2>
            <i :class="editandoId ? 'fa-solid fa-pen-nib' : 'fa-solid fa-users-viewfinder'"></i>
            {{ editandoId ? 'Editar Grupo' : 'Nuevo Grupo' }}
          </h2>
          <button @click="cerrarModal" class="btn-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form @submit.prevent="guardarGrupo" class="modal-form">
          <div class="form-grid">
            <div class="form-group">
              <label>Nombre del Grupo:</label>
              <input type="text" v-model="nuevoGrupo.nombre" placeholder="Ej. Selectivo Varonil" :class="{'input-error': errores.nombre}">
              <span class="text-error" v-if="errores.nombre">{{ errores.nombre[0] }}</span>
            </div>

            <div class="form-group">
              <label>Cupo Máximo:</label>
              <input type="number" v-model="nuevoGrupo.cupo_maximo" placeholder="Ej. 20" :class="{'input-error': errores.cupo_maximo}">
              <span class="text-error" v-if="errores.cupo_maximo">{{ errores.cupo_maximo[0] }}</span>
            </div>

            <div class="form-group">
              <label>Actividad:</label>
              <select v-model="nuevoGrupo.actividad_id" :class="{'input-error': errores.actividad_id}">
                <option value="" disabled>Seleccione...</option>
                <option v-for="act in actividades" :key="act.id" :value="act.id">{{ act.nombre }}</option>
              </select>
              <span class="text-error" v-if="errores.actividad_id">{{ errores.actividad_id[0] }}</span>
            </div>

            <div class="form-group">
              <label>Docente Asignado:</label>
              <select v-model="nuevoGrupo.docente_id" :class="{'input-error': errores.docente_id}">
                <option value="" disabled>Seleccione...</option>
                <option v-for="doc in docentes" :key="doc.id" :value="doc.id">{{ doc.nombre }} {{ doc.apellidos }}</option>
              </select>
              <span class="text-error" v-if="errores.docente_id">{{ errores.docente_id[0] }}</span>
            </div>

            <div class="form-group">
              <label>Ciclo Escolar:</label>
              <select v-model="nuevoGrupo.ciclo_id" :class="{'input-error': errores.ciclo_id}">
                <option value="" disabled>Seleccione...</option>
                <option v-for="ciclo in ciclos" :key="ciclo.id" :value="ciclo.id">{{ ciclo.nombre }}</option>
              </select>
              <span class="text-error" v-if="errores.ciclo_id">{{ errores.ciclo_id[0] }}</span>
            </div>

            <div class="form-group">
              <label>Nivel:</label>
              <select v-model="nuevoGrupo.nivel_id" :class="{'input-error': errores.nivel_id}">
                <option value="" disabled>Seleccione...</option>
                <option v-for="nivel in niveles" :key="nivel.id" :value="nivel.id">{{ nivel.nombre }}</option>
              </select>
              <span class="text-error" v-if="errores.nivel_id">{{ errores.nivel_id[0] }}</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" @click="cerrarModal" class="btn-cancelar">Cancelar</button>
            <button type="submit" class="btn-guardar" :disabled="enviando">
              <i class="fa-solid fa-floppy-disk"></i>
              {{ enviando ? 'Procesando...' : (editandoId ? 'Guardar Cambios' : 'Registrar Grupo') }}
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

// Variables de Estado
const grupos = ref([]);
const actividades = ref([]);
const docentes = ref([]);
const ciclos = ref([]);
const niveles = ref([]);

const nuevoGrupo = ref({ nombre: '', cupo_maximo: '', actividad_id: '', docente_id: '', ciclo_id: '', nivel_id: '' });
const errores = ref({});
const enviando = ref(false);
const editandoId = ref(null);
const mostrarModal = ref(false);
const busqueda = ref('');

// Filtro Inteligente
const listaFiltrada = computed(() => {
  const termino = busqueda.value.toLowerCase();
  return grupos.value.filter(g => 
    g.nombre.toLowerCase().includes(termino) ||
    (g.docente?.nombre + ' ' + g.docente?.apellidos).toLowerCase().includes(termino)
  );
});

// Lógica de Datos
const inicializarDatos = async () => {
    try {
        const [resGrupos, resAct, resDoc, resCiclos, resNiveles] = await Promise.all([
            axios.get('/api/grupos'), axios.get('/api/actividades'),
            axios.get('/api/docentes'), axios.get('/api/ciclos'), axios.get('/api/niveles')
        ]);
        
        grupos.value = resGrupos.data;
        actividades.value = resAct.data;
        docentes.value = resDoc.data;
        ciclos.value = resCiclos.data;
        niveles.value = resNiveles.data;
    } catch (error) { console.error("Error al cargar datos:", error); }
};

const abrirModalCrear = () => {
    editandoId.value = null;
    nuevoGrupo.value = { nombre: '', cupo_maximo: '', actividad_id: '', docente_id: '', ciclo_id: '', nivel_id: '' };
    errores.value = {};
    mostrarModal.value = true;
};

const cargarParaEditar = (grupo) => {
    nuevoGrupo.value = { ...grupo };
    editandoId.value = grupo.id;
    errores.value = {};
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
};

const guardarGrupo = async () => {
    errores.value = {};
    enviando.value = true;
    try {
        if (editandoId.value) {
            await axios.put(`/api/grupos/${editandoId.value}`, nuevoGrupo.value);
        } else {
            await axios.post('/api/grupos', nuevoGrupo.value);
        }
        
        const resGrupos = await axios.get('/api/grupos');
        grupos.value = resGrupos.data;
        
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

const eliminarGrupo = async (id) => {
    if (!confirm('¿Seguro que deseas eliminar este grupo?')) return; 
    try {
        await axios.delete(`/api/grupos/${id}`);
        grupos.value = grupos.value.filter(g => g.id !== id);
    } catch (error) {
        alert("Error al eliminar. Revisa si tiene horarios asignados.");
    }
};

const exportarExcel = () => {
    window.open('/api/reportes/grupos', '_blank');
};

onMounted(inicializarDatos);
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

.badge-level { background: #ebf5ff; color: #007bff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; width: fit-content; margin-top: 2px;}
.docente-pill { background: #f8f9fa; border: 1px solid #e9ecef; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; color: #495057; display: inline-block; }
.capacidad-badge { background: #eef2f5; border: 1px solid #dcdde1; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; color: #34495e; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }

.actions-cell { text-align: center; white-space: nowrap; }
.btn-icon.edit { color: #3498db; }
.btn-icon.delete { color: #e74c3c; }

/* Modal y Formulario */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(4px); }
.modal-card { background: #fff; width: 100%; max-width: 600px; border-radius: 12px; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease; }
@keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
.modal-header h2 { margin: 0; font-size: 1.4rem; color: var(--ugm-dark); display: flex; align-items: center; gap: 10px; }
.btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #95a5a6; transition: color 0.2s; }
.btn-close:hover { color: #e74c3c; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
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