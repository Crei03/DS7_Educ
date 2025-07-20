<template>
  <div v-if="isOpen" class="modal-overlay" @click="cerrarModal">
    <div class="modal-container" @click.stop>
      <div class="modal-header">
        <h2 class="modal-title">Detalles de Evaluación</h2>
        <button class="btn-cerrar" @click="cerrarModal">
          <XMarkIcon class="icon" />
        </button>
      </div>

      <div v-if="loading" class="modal-loading">
        <div class="spinner"></div>
        <p>Cargando evaluación...</p>
      </div>

      <div v-else-if="error" class="modal-error">
        <ExclamationTriangleIcon class="icon-error" />
        <p>{{ error }}</p>
      </div>

      <div v-else-if="evaluacion" class="modal-content">
        <!-- Información general de la evaluación -->
        <div class="evaluacion-header">
          <div class="evaluacion-info">
            <h3>{{ evaluacion.titulo }}</h3>
            <div class="evaluacion-meta">
              <div class="meta-item">
                <ClockIcon class="meta-icon" />
                <span>{{ evaluacion.duracion_min || 'Sin límite' }} minutos</span>
              </div>
              <div class="meta-item">
                <BookOpenIcon class="meta-icon" />
                <span>{{ evaluacion.curso?.titulo || 'Sin curso' }}</span>
              </div>
              <div class="meta-item">
                <ClipboardDocumentListIcon class="meta-icon" />
                <span>{{ evaluacion.preguntas?.length || 0 }} preguntas</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Lista de preguntas -->
        <div class="preguntas-container">
          <h4 class="preguntas-title">Preguntas de la Evaluación</h4>
          
          <div v-if="evaluacion.preguntas?.length === 0" class="sin-preguntas">
            <QuestionMarkCircleIcon class="icon-empty" />
            <p>Esta evaluación no tiene preguntas</p>
            <small>Edita la evaluación para agregar preguntas.</small>
          </div>

          <div v-else class="preguntas-lista">
            <div 
              v-for="(pregunta, index) in evaluacion.preguntas" 
              :key="pregunta.id" 
              class="pregunta-card"
            >
              <div class="pregunta-header">
                <span class="pregunta-numero">Pregunta {{ index + 1 }}</span>
              </div>
              
              <div class="pregunta-enunciado">
                {{ pregunta.enunciado }}
              </div>
              
              <div class="opciones-container">
                <div 
                  v-for="(opcion, opcionIndex) in pregunta.opciones" 
                  :key="opcion.id"
                  :class="['opcion-item', { 'opcion-correcta': opcion.es_correcta }]"
                >
                  <div class="opcion-indicator">
                    <span class="opcion-letra">{{ String.fromCharCode(65 + opcionIndex) }}</span>
                    <CheckIcon v-if="opcion.es_correcta" class="check-icon" />
                  </div>
                  <span class="opcion-texto">{{ opcion.texto }}</span>
                </div>

                <div v-if="!pregunta.opciones?.length" class="sin-opciones">
                  <span class="text-gray-500 text-sm">Sin opciones de respuesta</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn-modal btn-secondary" @click="cerrarModal">
          Cerrar
        </button>
        <button class="btn-modal btn-primary" @click="editarEvaluacion">
          <PencilIcon class="btn-icon" />
          Editar Evaluación
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import {
  XMarkIcon,
  ExclamationTriangleIcon,
  ClipboardDocumentListIcon,
  QuestionMarkCircleIcon,
  ClockIcon,
  BookOpenIcon,
  CheckIcon,
  PencilIcon
} from '@heroicons/vue/24/outline'
import { useEvaluacionesStore } from '@/stores/evaluaciones'
import api from '@/services/api'

const props = defineProps({
  isOpen: Boolean,
  evaluacionId: Number
})

const emit = defineEmits(['close', 'editar'])

const evaluacionesStore = useEvaluacionesStore()

const loading = ref(false)
const error = ref(null)
const evaluacion = ref(null)

const cerrarModal = () => {
  emit('close')
  // Limpiar datos al cerrar
  evaluacion.value = null
  error.value = null
}

const editarEvaluacion = () => {
  emit('editar', evaluacion.value)
  cerrarModal()
}

const cargarEvaluacion = async () => {
  if (!props.evaluacionId) return

  loading.value = true
  error.value = null

  try {
    const response = await api.get(`/evaluaciones/${props.evaluacionId}`)
    evaluacion.value = response.data
  } catch (e) {
    error.value = 'Error al cargar la evaluación'
    console.error('Error:', e)
  } finally {
    loading.value = false
  }
}

// Cargar evaluación cuando se abre el modal
watch(() => props.isOpen, (isOpen) => {
  if (isOpen && props.evaluacionId) {
    cargarEvaluacion()
  }
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 1rem;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  flex-shrink: 0;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.btn-cerrar {
  background: none;
  border: none;
  padding: 0.5rem;
  cursor: pointer;
  border-radius: 0.375rem;
  transition: background-color 0.2s;
}

.btn-cerrar:hover {
  background: #f3f4f6;
}

.icon {
  width: 24px;
  height: 24px;
  color: #6b7280;
}

.modal-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #0554f2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.modal-error {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 3rem;
  color: #dc2626;
}

.icon-error {
  width: 48px;
  height: 48px;
  margin-bottom: 1rem;
}

.modal-content {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
}

.evaluacion-header {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.evaluacion-info h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
}

.evaluacion-meta {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.meta-icon {
  width: 16px;
  height: 16px;
}

.preguntas-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.sin-preguntas {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.icon-empty {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  color: #d1d5db;
}

.preguntas-lista .pregunta-card:not(:last-child) {
  margin-bottom: 1.5rem;
}

.pregunta-card {
  background: #f9fafb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid #e5e7eb;
}

.pregunta-header {
  margin-bottom: 1rem;
}

.pregunta-numero {
  display: inline-block;
  background: #0554f2;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 600;
}

.pregunta-enunciado {
  font-size: 1rem;
  font-weight: 500;
  color: #1f2937;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.opciones-container .opcion-item:not(:last-child) {
  margin-bottom: 0.75rem;
}

.opcion-item {
  display: flex;
  align-items: center;
  padding: 0.75rem;
  background: white;
  border-radius: 0.5rem;
  border: 1px solid #e5e7eb;
  transition: all 0.2s;
}

.opcion-item.opcion-correcta {
  background: #dcfce7;
  border-color: #22c55e;
  box-shadow: 0 1px 3px rgba(34, 197, 94, 0.1);
}

.opcion-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: #f3f4f6;
  border-radius: 50%;
  margin-right: 0.75rem;
  flex-shrink: 0;
  position: relative;
}

.opcion-correcta .opcion-indicator {
  background: #22c55e;
}

.opcion-letra {
  font-weight: 600;
  font-size: 0.875rem;
  color: #6b7280;
}

.opcion-correcta .opcion-letra {
  color: white;
}

.check-icon {
  position: absolute;
  top: -4px;
  right: -4px;
  width: 16px;
  height: 16px;
  background: #16a34a;
  color: white;
  border-radius: 50%;
  padding: 2px;
}

.opcion-texto {
  flex: 1;
  font-size: 0.9rem;
  color: #1f2937;
  line-height: 1.4;
}

.sin-opciones {
  padding: 1rem;
  text-align: center;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  flex-shrink: 0;
}

.btn-modal {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.btn-primary {
  background: #0554f2;
  color: white;
}

.btn-primary:hover {
  background: #0540f2;
}

.btn-icon {
  width: 16px;
  height: 16px;
}
</style>
