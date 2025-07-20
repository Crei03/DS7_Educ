<template>
  <div class="evaluaciones-container">
    <div class="evaluaciones-header">
      <div>
        <h1 class="evaluaciones-title">Evaluaciones</h1>
        <p class="evaluaciones-subtitle">Crea y gestiona evaluaciones para tus estudiantes</p>
      </div>
      <button class="btn-nueva-evaluacion" @click="crearNuevaEvaluacion">
        <PlusIcon class="icon" />
        Nueva evaluación
      </button>
    </div>
    
    <div v-if="evaluacionesStore.loading" class="evaluaciones-loading">
      <div class="spinner"></div>
      <p>Cargando evaluaciones...</p>
    </div>
    
    <div v-else-if="evaluacionesStore.evaluaciones.length === 0" class="evaluaciones-empty">
      <ClipboardDocumentListIcon class="evaluaciones-icon-empty" />
      <h3 class="evaluaciones-empty-title">No tienes evaluaciones creadas</h3>
      <p class="evaluaciones-empty-text">Comienza creando tu primera evaluación para evaluar a tus estudiantes.</p>
      <button class="btn-crear-primera" @click="crearNuevaEvaluacion">
        Crear primera evaluación
      </button>
    </div>
    
    <div v-else class="evaluaciones-grid">
      <div 
        v-for="evaluacion in evaluacionesStore.evaluaciones" 
        :key="evaluacion.id"
        class="evaluacion-card"
      >
        <div class="evaluacion-card-header">
          <div class="evaluacion-card-icon">
            <ClipboardDocumentListIcon class="evaluacion-icon" />
          </div>
          <span 
            :class="['evaluacion-card-status', getStatusClass(evaluacion.estado)]"
          >
            {{ evaluacion.estado || 'Borrador' }}
          </span>
        </div>
        
        <h3 class="evaluacion-card-title">{{ evaluacion.titulo }}</h3>
        <p class="evaluacion-card-description">{{ evaluacion.descripcion || 'Sin descripción' }}</p>
        
        <div class="evaluacion-card-meta">
          <div class="evaluacion-meta-item">
            <span class="evaluacion-meta-label">Curso:</span>
            <span class="evaluacion-meta-value">{{ evaluacion.curso?.titulo || 'N/A' }}</span>
          </div>
          <div class="evaluacion-meta-item">
            <span class="evaluacion-meta-label">Duración:</span>
            <span class="evaluacion-meta-value">{{ evaluacion.duracion_min || 0 }} min</span>
          </div>
          <div class="evaluacion-meta-item">
            <span class="evaluacion-meta-label">Preguntas:</span>
            <span class="evaluacion-meta-value">{{ evaluacion.preguntas_count || 0 }}</span>
          </div>
          <div class="evaluacion-meta-item">
            <span class="evaluacion-meta-label">Respuestas:</span>
            <span class="evaluacion-meta-value">{{ evaluacion.respuestas_count || 0 }}</span>
          </div>
        </div>
        
        <div class="evaluacion-card-actions">
          <button class="btn-evaluacion-action btn-editar" @click="editarEvaluacion(evaluacion)">
            <PencilIcon class="btn-icon" />
            Editar
          </button>
          <button class="btn-evaluacion-action btn-ver-detalles" @click="verDetalles(evaluacion)">
            <EyeIcon class="btn-icon" />
            Ver Detalles
          </button>
          <button class="btn-evaluacion-action btn-ver-resultados" @click="verResultados(evaluacion)">
            <ChartBarIcon class="btn-icon" />
            Resultados
          </button>
        </div>
      </div>
    </div>

    <!-- Modales -->
    <ModalResultadosEvaluacion
      :is-open="modalResultados.isOpen"
      :evaluacion-id="modalResultados.evaluacionId"
      @close="cerrarModalResultados"
    />
    
    <ModalDetalleEvaluacion
      :is-open="modalDetalle.isOpen"
      :evaluacion-id="modalDetalle.evaluacionId"
      @close="cerrarModalDetalle"
      @editar="editarEvaluacion"
    />
    
    <ModalFormularioEvaluacion
      :is-open="modalFormulario.isOpen"
      :evaluacion-id="modalFormulario.evaluacionId"
      @close="cerrarModalFormulario"
      @success="onEvaluacionGuardada"
    />
  </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue'
import { 
  ClipboardDocumentListIcon, 
  PlusIcon,
  PencilIcon,
  EyeIcon,
  ChartBarIcon
} from '@heroicons/vue/24/outline'
import { useEvaluacionesStore } from '@/stores/evaluaciones'
import { useAuthStore } from '@/stores/auth'
import ModalResultadosEvaluacion from '@/components/evaluaciones/ModalResultadosEvaluacion.vue'
import ModalDetalleEvaluacion from '@/components/evaluaciones/ModalDetalleEvaluacion.vue'
import ModalFormularioEvaluacion from '@/components/evaluaciones/ModalFormularioEvaluacion.vue'

const evaluacionesStore = useEvaluacionesStore()
const authStore = useAuthStore()

// Estados de los modales
const modalResultados = reactive({
  isOpen: false,
  evaluacionId: null
})

const modalDetalle = reactive({
  isOpen: false,
  evaluacionId: null
})

const modalFormulario = reactive({
  isOpen: false,
  evaluacionId: null
})

const getStatusClass = (estado) => {
  switch (estado?.toLowerCase()) {
    case 'activa':
    case 'publicada':
      return 'evaluacion-activa'
    case 'finalizada':
      return 'evaluacion-finalizada'
    case 'borrador':
    default:
      return 'evaluacion-borrador'
  }
}

const crearNuevaEvaluacion = () => {
  modalFormulario.evaluacionId = null
  modalFormulario.isOpen = true
}

const editarEvaluacion = (evaluacion) => {
  modalFormulario.evaluacionId = evaluacion.id
  modalFormulario.isOpen = true
}

const verDetalles = (evaluacion) => {
  modalDetalle.evaluacionId = evaluacion.id
  modalDetalle.isOpen = true
}

const verResultados = (evaluacion) => {
  modalResultados.evaluacionId = evaluacion.id
  modalResultados.isOpen = true
}

const cerrarModalResultados = () => {
  modalResultados.isOpen = false
  modalResultados.evaluacionId = null
}

const cerrarModalDetalle = () => {
  modalDetalle.isOpen = false
  modalDetalle.evaluacionId = null
}

const cerrarModalFormulario = () => {
  modalFormulario.isOpen = false
  modalFormulario.evaluacionId = null
}

const onEvaluacionGuardada = (evaluacion) => {
  // La lista se refresca automáticamente en el modal
  console.log('Evaluación guardada:', evaluacion)
}

onMounted(() => {
  const profesorId = authStore.user?.id
  if (profesorId) {
    evaluacionesStore.fetchEvaluacionesProfesor(profesorId)
  }
})
</script>

<style scoped>
.evaluaciones-container {
  padding: 2rem;
}

.evaluaciones-header {
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.evaluaciones-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.evaluaciones-subtitle {
  color: #6b7280;
  font-size: 1.1rem;
}

.btn-nueva-evaluacion {
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

.btn-nueva-evaluacion:hover {
  background: #0540f2;
}

.icon {
  width: 20px;
  height: 20px;
}

.evaluaciones-loading {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1.1rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #0554f2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.evaluaciones-empty {
  background: #fff;
  border-radius: 1rem;
  padding: 3rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  text-align: center;
}

.evaluaciones-icon-empty {
  width: 64px;
  height: 64px;
  color: #e5e7eb;
  margin: 0 auto 1rem;
}

.evaluaciones-empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.evaluaciones-empty-text {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.btn-crear-primera {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-crear-primera:hover {
  background: #0540f2;
}

.evaluaciones-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.evaluacion-card {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: box-shadow 0.2s;
}

.evaluacion-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.10);
}

.evaluacion-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.evaluacion-card-icon {
  background: #fef3c7;
  padding: 0.5rem;
  border-radius: 0.5rem;
}

.evaluacion-icon {
  width: 24px;
  height: 24px;
  color: #d97706;
}

.evaluacion-card-status {
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.evaluacion-activa {
  background: #dcfce7;
  color: #166534;
}

.evaluacion-finalizada {
  background: #e0e7ff;
  color: #3730a3;
}

.evaluacion-borrador {
  background: #f3f4f6;
  color: #374151;
}

.evaluacion-card-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.evaluacion-card-description {
  color: #6b7280;
  margin-bottom: 1rem;
  line-height: 1.5;
  font-size: 0.9rem;
}

.evaluacion-card-meta {
  background: #f9fafb;
  border-radius: 0.5rem;
  padding: 0.75rem;
  margin-bottom: 1rem;
}

.evaluacion-meta-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.25rem;
}

.evaluacion-meta-item:last-child {
  margin-bottom: 0;
}

.evaluacion-meta-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.evaluacion-meta-value {
  font-size: 0.875rem;
  color: #1f2937;
  font-weight: 600;
}

.evaluacion-card-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-evaluacion-action {
  flex: 1;
  padding: 0.5rem 0.75rem;
  border: none;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
}

.btn-icon {
  width: 14px;
  height: 14px;
}

.btn-editar {
  background: #0554f2;
  color: #fff;
}

.btn-editar:hover {
  background: #0540f2;
}

.btn-ver-detalles {
  background: #f59e0b;
  color: #fff;
}

.btn-ver-detalles:hover {
  background: #d97706;
}

.btn-ver-resultados {
  background: #10b981;
  color: #fff;
}

.btn-ver-resultados:hover {
  background: #059669;
}

</style>
