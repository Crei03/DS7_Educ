<template>
  <div class="modulos-container">
    <div class="modulos-header">
      <div>
        <h1 class="modulos-title">Mis Módulos</h1>
        <p class="modulos-subtitle">Gestiona los módulos de todos tus cursos</p>
      </div>
      <button @click="mostrarModalCrear = true" class="btn-nuevo-modulo">
        <PlusIcon class="icon" />
        Crear nuevo módulo
      </button>
    </div>

    <!-- Filtros -->
    <div class="modulos-filtros">
      <div class="filtro-grupo">
        <label class="filtro-label">Filtrar por curso:</label>
        <select v-model="filtrosCurso" class="filtro-select">
          <option value="">Todos los cursos</option>
          <option v-for="curso in cursosStore.cursosProfesor" :key="curso.id" :value="curso.id">
            {{ curso.titulo }}
          </option>
        </select>
      </div>
    </div>
    
    <div v-if="modulosStore.loading" class="modulos-loading">
      <div class="spinner">
        <div class="loader"></div>
      </div>
      <p>Cargando módulos...</p>
    </div>
    
    <div v-else-if="modulosFiltrados.length === 0" class="modulos-empty">
      <BookOpenIcon class="modulos-icon-empty" />
      <h3 class="modulos-empty-title">No hay módulos disponibles</h3>
      <p class="modulos-empty-text">Comienza creando módulos para organizar el contenido de tus cursos.</p>
      <button @click="mostrarModalCrear = true" class="btn-crear-primero">
        Crear primer módulo
      </button>
    </div>
    
    <div v-else class="modulos-grid">
      <div 
        v-for="modulo in modulosFiltrados" 
        :key="modulo.id"
        class="modulo-card"
      >
        <div class="modulo-card-header">
          <div>
            <h3 class="modulo-card-title">{{ modulo.titulo }}</h3>
            <p class="modulo-card-curso">{{ modulo.curso?.titulo || 'Sin curso' }}</p>
          </div>
          <div class="modulo-orden">
            <span class="orden-badge">Orden: {{ modulo.orden }}</span>
          </div>
        </div>
        
        <div class="modulo-card-stats">
          <div class="modulo-stat">
            <DocumentTextIcon class="modulo-stat-icon" />
            <span class="modulo-stat-number">{{ modulo.materiales_count || 0 }}</span>
            <span class="modulo-stat-label">Materiales</span>
          </div>
          <div class="modulo-stat">
            <ClockIcon class="modulo-stat-icon" />
            <span class="modulo-stat-number">{{ formatearFecha(modulo.created_at) }}</span>
            <span class="modulo-stat-label">Creado</span>
          </div>
        </div>
        
        <div class="modulo-card-actions">
          <button @click="verDetalle(modulo)" class="btn-modulo-action btn-ver">
            <EyeIcon class="btn-icon" />
            Ver detalle
          </button>
          <button @click="editarModulo(modulo)" class="btn-modulo-action btn-editar">
            <PencilIcon class="btn-icon" />
            Editar
          </button>
          <button @click="eliminarModuloConfirm(modulo)" class="btn-modulo-action btn-eliminar">
            <TrashIcon class="btn-icon" />
            Eliminar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Detalle Módulo -->
    <div v-if="mostrarModalDetalle" class="modal-overlay" @click="mostrarModalDetalle = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">Detalle del Módulo</h2>
          <button @click="mostrarModalDetalle = false" class="btn-cerrar-modal">
            <XMarkIcon class="icon" />
          </button>
        </div>
        <div class="modal-body">
          <div class="detalle-grupo">
            <label class="detalle-label">Título:</label>
            <span class="detalle-valor">{{ moduloDetalle?.titulo }}</span>
          </div>
          <div class="detalle-grupo">
            <label class="detalle-label">Curso:</label>
            <span class="detalle-valor">{{ moduloDetalle?.curso?.titulo || 'Sin curso' }}</span>
          </div>
          <div class="detalle-grupo">
            <label class="detalle-label">Orden:</label>
            <span class="detalle-valor">{{ moduloDetalle?.orden }}</span>
          </div>
          <div class="detalle-grupo">
            <label class="detalle-label">ID:</label>
            <span class="detalle-valor">{{ moduloDetalle?.id }}</span>
          </div>
        </div>
        <div class="modal-actions">
          <button @click="mostrarModalDetalle = false" class="btn-cancelar">Cerrar</button>
        </div>
      </div>
    </div>

    <!-- Modal Crear/Editar Módulo -->
    <div v-if="mostrarModalCrear || mostrarModalEditar" class="modal-overlay" @click="cerrarModales">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">
            {{ mostrarModalEditar ? 'Editar Módulo' : 'Crear Nuevo Módulo' }}
          </h2>
          <button @click="cerrarModales" class="btn-cerrar-modal">
            <XMarkIcon class="icon" />
          </button>
        </div>
        
        <form @submit.prevent="guardarModulo" class="modal-form">
          <div class="form-grupo">
            <label class="form-label">Título del módulo *</label>
            <input 
              v-model="formularioModulo.titulo" 
              type="text" 
              class="form-input"
              placeholder="Ej: Introducción a Laravel"
              required
            />
          </div>
          
          <div class="form-grupo">
            <label class="form-label">Curso *</label>
            <select v-model="formularioModulo.curso_id" class="form-select" required>
              <option value="">Selecciona un curso</option>
              <option v-for="curso in cursosStore.cursosProfesor" :key="curso.id" :value="curso.id">
                {{ curso.titulo }}
              </option>
            </select>
          </div>
          
          <div class="form-grupo">
            <label class="form-label">Orden</label>
            <input 
              v-model.number="formularioModulo.orden" 
              type="number" 
              class="form-input"
              min="1"
              placeholder="1"
            />
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="cerrarModales" class="btn-cancelar">
              Cancelar
            </button>
            <button type="submit" class="btn-guardar" :disabled="modulosStore.loading">
              {{ modulosStore.loading ? 'Guardando...' : (mostrarModalEditar ? 'Actualizar' : 'Crear módulo') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Confirmar Eliminar -->
    <div v-if="mostrarModalEliminar" class="modal-overlay" @click="mostrarModalEliminar = false">
      <div class="modal-content modal-confirm" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">Confirmar eliminación</h2>
        </div>
        
        <div class="modal-body">
          <p>¿Estás seguro de que deseas eliminar el módulo <strong>"{{ moduloAEliminar?.titulo }}"</strong>?</p>
          <p class="text-warning">Esta acción no se puede deshacer y eliminará todos los materiales asociados.</p>
        </div>
        
        <div class="modal-actions">
          <button @click="mostrarModalEliminar = false" class="btn-cancelar">
            Cancelar
          </button>
          <button @click="eliminarModulo" class="btn-eliminar" :disabled="modulosStore.loading">
            {{ modulosStore.loading ? 'Eliminando...' : 'Eliminar módulo' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  BookOpenIcon, 
  PlusIcon,
  DocumentTextIcon,
  ClockIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'
import { useModulosStore } from '@/stores/modulos'
import { useCursosStore } from '@/stores/cursos'
import { useAuthStore } from '@/stores/auth'

const modulosStore = useModulosStore()
const cursosStore = useCursosStore()
const authStore = useAuthStore()

// Estados reactivos
const mostrarModalCrear = ref(false)
const mostrarModalEditar = ref(false)
const mostrarModalEliminar = ref(false)
const mostrarModalDetalle = ref(false)
const moduloAEliminar = ref(null)
const moduloAEditar = ref(null)
const moduloDetalle = ref(null)
const filtrosCurso = ref('')

// Formulario
const formularioModulo = ref({
  titulo: '',
  curso_id: '',
  orden: 1
})

// Computed
const modulosFiltrados = computed(() => {
  let modulos = Array.isArray(modulosStore.modulos) ? modulosStore.modulos : []

  if (filtrosCurso.value) {
    modulos = modulos.filter(m => m.curso_id == filtrosCurso.value)
  }

  return modulos.length > 0
    ? modulos.sort((a, b) => {
        // Ordenar por curso y luego por orden dentro del curso
        if (a.curso_id !== b.curso_id) {
          return a.curso?.titulo?.localeCompare(b.curso?.titulo || '') || 0
        }
        return (a.orden || 0) - (b.orden || 0)
      })
    : []
})

// Métodos
const formatearFecha = (fecha) => {
  if (!fecha) return 'N/A'
  return new Date(fecha).toLocaleDateString('es-PA', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const verDetalle = (modulo) => {
  moduloDetalle.value = modulo
  mostrarModalDetalle.value = true
}

const editarModulo = (modulo) => {
  moduloAEditar.value = modulo
  formularioModulo.value = {
    titulo: modulo.titulo,
    curso_id: modulo.curso_id,
    orden: modulo.orden || 1
  }
  mostrarModalEditar.value = true
}

const eliminarModuloConfirm = (modulo) => {
  moduloAEliminar.value = modulo
  mostrarModalEliminar.value = true
}

const eliminarModulo = async () => {
  if (!moduloAEliminar.value) return
  
  try {
    await modulosStore.eliminarModulo(moduloAEliminar.value.id)
    mostrarModalEliminar.value = false
    moduloAEliminar.value = null
  } catch (error) {
    console.error('Error al eliminar módulo:', error)
    // TODO: Mostrar notificación de error
  }
}

const guardarModulo = async () => {
  try {
    if (mostrarModalEditar.value && moduloAEditar.value) {
      await modulosStore.actualizarModulo(moduloAEditar.value.id, formularioModulo.value)
    } else {
      await modulosStore.crearModulo(formularioModulo.value)
    }
    cerrarModales()
    limpiarFormulario()
  } catch (error) {
    console.error('Error al guardar módulo:', error)
    // TODO: Mostrar notificación de error
    cerrarModales()
    limpiarFormulario()
  }
}

const cerrarModales = () => {
  mostrarModalCrear.value = false
  mostrarModalEditar.value = false
  mostrarModalDetalle.value = false
  moduloAEditar.value = null
  limpiarFormulario()
}

const limpiarFormulario = () => {
  formularioModulo.value = {
    titulo: '',
    curso_id: '',
    orden: 1
  }
}

// Lifecycle
onMounted(async () => {
  const profesorId = authStore.user?.id
  if (profesorId) {
    // Cargar cursos del profesor primero
    await cursosStore.fetchCursosProfesor(profesorId)
    // Luego cargar los módulos filtrados por profesor
    await modulosStore.fetchModulos(profesorId)
  }
})
</script>

<style scoped>
.modulos-container {
  padding: 2rem;
}

.modulos-header {
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.modulos-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.modulos-subtitle {
  color: #6b7280;
  font-size: 1.1rem;
}

.btn-nuevo-modulo {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-nuevo-modulo:hover {
  background: #0540f2;
}

.icon {
  width: 20px;
  height: 20px;
}

.modulos-filtros {
  background: #fff;
  padding: 1.5rem;
  border-radius: 1rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  margin-bottom: 2rem;
  display: flex;
  gap: 2rem;
  align-items: center;
}

.filtro-grupo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.filtro-label {
  font-weight: 500;
  color: #374151;
  white-space: nowrap;
}

.filtro-select {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  min-width: 200px;
}

.modulos-loading {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1.1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.spinner {
  display: flex;
  justify-content: center;
}

.loader {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #0554f2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.modulos-empty {
  background: #fff;
  border-radius: 1rem;
  padding: 3rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  text-align: center;
}

.modulos-icon-empty {
  width: 64px;
  height: 64px;
  color: #e5e7eb;
  margin: 0 auto 1rem;
}

.modulos-empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.modulos-empty-text {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.btn-crear-primero {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-crear-primero:hover {
  background: #0540f2;
}

.modulos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 1.5rem;
}

.modulo-card {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: box-shadow 0.2s;
}

.modulo-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.10);
}

.modulo-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.modulo-card-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
}

.modulo-card-curso {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.modulo-orden {
  flex-shrink: 0;
}

.orden-badge {
  background: #f3f4f6;
  color: #374151;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.modulo-card-stats {
  display: flex;
  justify-content: space-around;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.modulo-stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.modulo-stat-icon {
  width: 20px;
  height: 20px;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.modulo-stat-number {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
}

.modulo-stat-label {
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.modulo-card-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-modulo-action {
  flex: 1;
  padding: 0.5rem 0.75rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
  font-size: 0.875rem;
}

.btn-icon {
  width: 16px;
  height: 16px;
}

.btn-ver {
  background: #f3f4f6;
  color: #374151;
}

.btn-ver:hover {
  background: #e5e7eb;
}

.btn-editar {
  background: #0554f2;
  color: #fff;
}

.btn-editar:hover {
  background: #0540f2;
}

.btn-eliminar {
  background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
  color: #fff;
  border: none;
  border-radius: 0.5rem;
  box-shadow: 0 2px 8px rgba(239,68,68,0.10);
  font-weight: 600;
  padding: 0.75rem 2rem;
  transition: background 0.2s, box-shadow 0.2s;
}

.btn-eliminar:hover {
  background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
  box-shadow: 0 4px 16px rgba(239,68,68,0.18);
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  border-radius: 1rem;
  padding: 2rem;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-confirm {
  max-width: 400px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.btn-cerrar-modal {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.25rem;
  color: #6b7280;
}

.btn-cerrar-modal:hover {
  color: #374151;
}

.modal-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-grupo {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-weight: 500;
  color: #374151;
}

.form-input,
.form-select {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #0554f2;
  box-shadow: 0 0 0 3px rgba(5, 84, 242, 0.1);
}

.modal-body {
  margin-bottom: 1.5rem;
}

.text-warning {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.btn-cancelar {
  background: #f3f4f6;
  color: #374151;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-cancelar:hover {
  background: #e5e7eb;
}

.btn-guardar {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-guardar:hover:not(:disabled) {
  background: #0540f2;
}

.btn-guardar:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

/* Detalle modal styles */
.detalle-grupo {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  align-items: center;
}
.detalle-label {
  font-weight: 500;
  color: #374151;
  min-width: 80px;
}
.detalle-valor {
  color: #0554f2;
  font-weight: 600;
  font-size: 1.05rem;
}
</style>
