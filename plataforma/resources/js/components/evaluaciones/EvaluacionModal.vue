<template>
  <!-- Modal Backdrop -->
  <div class="modal-backdrop">
    <div class="modal-container">
      <!-- Modal Content -->
      <div class="modal-content">
        
        <!-- Header con timer -->
        <div class="modal-header">
          <div>
            <h2 class="modal-title">
              {{ evaluacionActiva?.titulo }}
            </h2>
            <p class="modal-subtitle">
              {{ evaluacionActiva?.preguntas?.length || 0 }} preguntas
            </p>
          </div>
          
          <!-- Timer -->
          <div v-if="tiempoRestante !== null" class="timer-container">
            <div class="timer-text">
              <p class="timer-label">Tiempo restante</p>
              <p 
                class="timer-value"
                :class="tiempoRestante < 300 ? 'timer-warning' : 'timer-normal'"
              >
                {{ formatearTiempo(tiempoRestante) }}
              </p>
            </div>
            
            <!-- Indicador visual del tiempo -->
            <div class="timer-visual">
              <div class="timer-circle">
                <svg class="timer-svg">
                  <circle 
                    cx="32" 
                    cy="32" 
                    r="28" 
                    stroke="#e5e7eb" 
                    stroke-width="4" 
                    fill="none"
                  />
                  <circle 
                    cx="32" 
                    cy="32" 
                    r="28" 
                    :stroke="tiempoRestante < 300 ? '#dc2626' : '#2563eb'"
                    stroke-width="4" 
                    fill="none"
                    stroke-linecap="round"
                    :stroke-dasharray="175.84"
                    :stroke-dashoffset="175.84 * (1 - progresotiempo)"
                    class="timer-progress"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Botón cerrar -->
          <button 
            @click="confirmarCerrar"
            class="modal-close-btn"
            :disabled="resolviendoEvaluacion"
          >
            <XMarkIcon class="icon-sm"/>
          </button>
        </div>

        <!-- Progreso de preguntas -->
        <div class="progress-section">
          <div class="progress-info">
            <span class="progress-current">
              Pregunta {{ preguntaActual + 1 }} de {{ evaluacionActiva?.preguntas?.length || 0 }}
            </span>
            <span class="progress-answered">
              {{ respuestasSeleccionadas.filter(r => r !== null).length }} respondidas
            </span>
          </div>
          
          <!-- Barra de progreso -->
          <div class="progress-bar-container">
            <div 
              class="progress-bar-fill"
              :style="{ width: `${progresoPreguntas}%` }"
            ></div>
          </div>
        </div>

        <!-- Contenido principal -->
        <div class="modal-body">
          <div v-if="evaluacionActiva?.preguntas && evaluacionActiva.preguntas.length > 0" class="question-container">
            
            <!-- Pregunta actual -->
            <div class="question-section">
              <div class="question-header">
                <h3 class="question-title">
                  Pregunta {{ preguntaActual + 1 }}
                </h3>
                <p class="question-text">
                  {{ preguntaActualObj.enunciado }}
                </p>
              </div>

              <!-- Opciones de respuesta -->
              <div class="options-container">
                <div
                  v-for="(opcion, index) in preguntaActualObj.opciones"
                  :key="opcion.id"
                  class="option-wrapper"
                >
                  <label 
                    class="option-label"
                    :class="respuestasSeleccionadas[preguntaActual] === opcion.id ? 'option-selected' : 'option-default'"
                  >
                    <input
                      type="radio"
                      :name="`pregunta-${preguntaActual}`"
                      :value="opcion.id"
                      v-model="respuestasSeleccionadas[preguntaActual]"
                      class="option-input"
                    />
                    
                    <!-- Radio button visual -->
                    <div class="option-radio-container">
                      <div 
                        class="option-radio"
                        :class="respuestasSeleccionadas[preguntaActual] === opcion.id ? 'radio-selected' : 'radio-default'"
                      >
                        <div 
                          v-if="respuestasSeleccionadas[preguntaActual] === opcion.id"
                          class="radio-dot"
                        ></div>
                      </div>
                    </div>
                    
                    <!-- Letra y texto de la opción -->
                    <div class="option-content">
                      <div class="option-content-flex">
                        <span class="option-letter">
                          {{ String.fromCharCode(65 + index) }}
                        </span>
                        <span class="option-text">{{ opcion.texto }}</span>
                      </div>
                    </div>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Estado de carga o error -->
          <div v-else class="error-state">
            <ExclamationTriangleIcon class="error-icon"/>
            <h3 class="error-title">Error al cargar preguntas</h3>
            <p class="error-message">No se pudieron cargar las preguntas de la evaluación.</p>
          </div>
        </div>

        <!-- Footer con navegación -->
        <div class="modal-footer">
          <!-- Navegación entre preguntas -->
          <div class="nav-buttons">
            <button
              @click="preguntaAnterior"
              :disabled="preguntaActual === 0"
              class="nav-btn nav-btn-prev"
            >
              <ChevronLeftIcon class="icon-xs"/>
              Anterior
            </button>
            
            <button
              v-if="preguntaActual < (evaluacionActiva?.preguntas?.length || 0) - 1"
              @click="preguntaSiguiente"
              class="nav-btn nav-btn-next"
            >
              Siguiente
              <ChevronRightIcon class="icon-xs"/>
            </button>
          </div>

          <!-- Indicadores de preguntas -->
          <div class="question-indicators">
            <button
              v-for="(pregunta, index) in evaluacionActiva?.preguntas || []"
              :key="pregunta.id"
              @click="irAPregunta(index)"
              class="indicator-btn"
              :class="[
                index === preguntaActual
                  ? 'indicator-current'
                  : respuestasSeleccionadas[index] !== null
                    ? 'indicator-answered'
                    : 'indicator-pending'
              ]"
            >
              {{ index + 1 }}
            </button>
          </div>

          <!-- Botón enviar -->
          <button
            @click="confirmarEnvio"
            :disabled="resolviendoEvaluacion || !tieneRespuestas"
            class="submit-btn"
          >
            <template v-if="resolviendoEvaluacion">
              <div class="loading-spinner"></div>
              Enviando...
            </template>
            <template v-else>
              <PaperAirplaneIcon class="icon-xs"/>
              Enviar Evaluación
            </template>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de confirmación para cerrar -->
  <ModalConfirmacion
    v-if="mostrarConfirmacionCerrar"
    titulo="¿Cerrar evaluación?"
    mensaje="Si cierras la evaluación ahora, perderás todo el progreso. ¿Estás seguro de que quieres continuar?"
    @confirmar="cerrarForzado"
    @cancelar="mostrarConfirmacionCerrar = false"
  />

  <!-- Modal de confirmación para enviar -->
  <ModalConfirmacion
    v-if="mostrarConfirmacionEnvio"
    titulo="¿Enviar evaluación?"
    :mensaje="`Has respondido ${respuestasCompletadas} de ${evaluacionActiva?.preguntas?.length || 0} preguntas. Una vez enviada, no podrás modificar tus respuestas.`"
    textoConfirmar="Enviar"
    @confirmar="enviarEvaluacion"
    @cancelar="mostrarConfirmacionEnvio = false"
  />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { 
  XMarkIcon, 
  ChevronLeftIcon, 
  ChevronRightIcon, 
  PaperAirplaneIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'
import { useEvaluacionesEstudianteStore } from '@/stores/evaluacionesEstudiante'
import ModalConfirmacion from '@/components/ui/ModalConfirmacion.vue'

const emit = defineEmits(['cerrar'])

const evaluacionesStore = useEvaluacionesEstudianteStore()

// Estado local
const preguntaActual = ref(0)
const respuestasSeleccionadas = ref([])
const mostrarConfirmacionCerrar = ref(false)
const mostrarConfirmacionEnvio = ref(false)

// Computed properties
const evaluacionActiva = computed(() => evaluacionesStore.evaluacionActiva)
const tiempoRestante = computed(() => evaluacionesStore.tiempoRestante)
const resolviendoEvaluacion = computed(() => evaluacionesStore.resolviendoEvaluacion)

const preguntaActualObj = computed(() => {
  if (!evaluacionActiva.value?.preguntas) return {}
  return evaluacionActiva.value.preguntas[preguntaActual.value] || {}
})

const progresoPreguntas = computed(() => {
  if (!evaluacionActiva.value?.preguntas) return 0
  return ((preguntaActual.value + 1) / evaluacionActiva.value.preguntas.length) * 100
})

const progresotiempo = computed(() => {
  if (!evaluacionActiva.value?.duracion_min || tiempoRestante.value === null) return 1
  const tiempoTotal = evaluacionActiva.value.duracion_min * 60
  return tiempoRestante.value / tiempoTotal
})

const respuestasCompletadas = computed(() => {
  return respuestasSeleccionadas.value.filter(r => r !== null).length
})

const tieneRespuestas = computed(() => {
  return respuestasCompletadas.value > 0
})

// Methods
const inicializarRespuestas = () => {
  if (evaluacionActiva.value?.preguntas) {
    respuestasSeleccionadas.value = new Array(evaluacionActiva.value.preguntas.length).fill(null)
  }
}

const formatearTiempo = (segundos) => {
  const minutos = Math.floor(segundos / 60)
  const seg = segundos % 60
  return `${minutos.toString().padStart(2, '0')}:${seg.toString().padStart(2, '0')}`
}

const preguntaAnterior = () => {
  if (preguntaActual.value > 0) {
    preguntaActual.value--
  }
}

const preguntaSiguiente = () => {
  if (preguntaActual.value < (evaluacionActiva.value?.preguntas?.length || 0) - 1) {
    preguntaActual.value++
  }
}

const irAPregunta = (index) => {
  preguntaActual.value = index
}

const confirmarCerrar = () => {
  if (respuestasCompletadas.value > 0) {
    mostrarConfirmacionCerrar.value = true
  } else {
    cerrarForzado()
  }
}

const cerrarForzado = () => {
  mostrarConfirmacionCerrar.value = false
  evaluacionesStore.limpiarEvaluacionActiva()
  emit('cerrar')
}

const confirmarEnvio = () => {
  mostrarConfirmacionEnvio.value = true
}

const enviarEvaluacion = async () => {
  mostrarConfirmacionEnvio.value = false
  
  try {
    // Crear objeto de respuestas para el backend
    const respuestas = {}
    respuestasSeleccionadas.value.forEach((respuesta, index) => {
      if (respuesta !== null) {
        const preguntaId = evaluacionActiva.value.preguntas[index].id
        respuestas[preguntaId] = respuesta
      }
    })

    await evaluacionesStore.resolverEvaluacion(
      evaluacionActiva.value.id, 
      respuestas
    )
    
    // Cerrar modal después del envío exitoso
    emit('cerrar')
    
  } catch (error) {
    console.error('Error al enviar evaluación:', error)
    // Mostrar notificación de error
  }
}

// Manejar teclas de navegación
const manejarTeclado = (event) => {
  if (event.key === 'ArrowLeft') {
    event.preventDefault()
    preguntaAnterior()
  } else if (event.key === 'ArrowRight') {
    event.preventDefault()
    preguntaSiguiente()
  } else if (event.key >= '1' && event.key <= '9') {
    const index = parseInt(event.key) - 1
    if (index < (evaluacionActiva.value?.preguntas?.length || 0)) {
      irAPregunta(index)
    }
  }
}

// Lifecycle
onMounted(() => {
  inicializarRespuestas()
  window.addEventListener('keydown', manejarTeclado)
})

onUnmounted(() => {
  window.removeEventListener('keydown', manejarTeclado)
})
</script>

<style scoped>
/* Modal Styles */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  overflow-y: auto;
  height: 100%;
  width: 100%;
  z-index: 50;
}

.modal-container {
  position: relative;
  top: 0;
  margin: 0 auto;
  padding: 20px;
  width: 100%;
  max-width: 896px;
  height: 100%;
}

.modal-content {
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
  height: 100%;
  display: flex;
  flex-direction: column;
}

/* Header */
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  font-size: 24px;
  font-weight: bold;
  color: #111827;
  margin: 0;
}

.modal-subtitle {
  color: #6b7280;
  margin: 4px 0 0 0;
  font-size: 14px;
}

.modal-close-btn {
  color: #9ca3af;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  transition: color 0.2s;
}

.modal-close-btn:hover {
  color: #6b7280;
}

.modal-close-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Timer */
.timer-container {
  display: flex;
  align-items: center;
  gap: 16px;
}

.timer-text {
  text-align: right;
}

.timer-label {
  font-size: 14px;
  color: #6b7280;
  margin: 0 0 2px 0;
}

.timer-value {
  font-size: 24px;
  font-weight: bold;
  margin: 0;
}

.timer-normal {
  color: #2563eb;
}

.timer-warning {
  color: #dc2626;
}

.timer-visual {
  position: relative;
}

.timer-circle {
  width: 64px;
  height: 64px;
}

.timer-svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}

.timer-progress {
  transition: all 1s ease;
}

/* Progress */
.progress-section {
  padding: 12px 24px;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.progress-info {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.progress-current {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.progress-answered {
  font-size: 14px;
  color: #6b7280;
}

.progress-bar-container {
  width: 100%;
  background: #e5e7eb;
  border-radius: 9999px;
  height: 8px;
}

.progress-bar-fill {
  background: #2563eb;
  height: 8px;
  border-radius: 9999px;
  transition: width 0.3s ease;
}

/* Body */
.modal-body {
  flex: 1;
  overflow-y: auto;
}

.question-container {
  padding: 24px;
}

.question-section {
  margin-bottom: 32px;
}

.question-header {
  background: #eff6ff;
  border-radius: 8px;
  padding: 24px;
  margin-bottom: 24px;
}

.question-title {
  font-size: 18px;
  font-weight: 600;
  color: #111827;
  margin: 0 0 8px 0;
}

.question-text {
  color: #1f2937;
  line-height: 1.6;
  margin: 0;
}

/* Options */
.options-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.option-wrapper {
  position: relative;
}

.option-label {
  display: flex;
  align-items: flex-start;
  padding: 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: white;
}

.option-label:hover {
  background: #f9fafb;
}

.option-default {
  border-color: #e5e7eb;
}

.option-selected {
  border-color: #3b82f6;
  background: #eff6ff;
}

.option-input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.option-radio-container {
  flex-shrink: 0;
  margin: 4px 12px 0 0;
}

.option-radio {
  width: 16px;
  height: 16px;
  border: 2px solid #d1d5db;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.radio-selected {
  border-color: #3b82f6;
  background: #3b82f6;
}

.radio-default {
  border-color: #d1d5db;
}

.radio-dot {
  width: 8px;
  height: 8px;
  background: white;
  border-radius: 50%;
}

.option-content {
  flex: 1;
}

.option-content-flex {
  display: flex;
  align-items: flex-start;
}

.option-letter {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  background: #f3f4f6;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  border-radius: 4px;
  margin-right: 12px;
  flex-shrink: 0;
}

.option-text {
  color: #111827;
}

/* Error State */
.error-state {
  padding: 24px;
  text-align: center;
}

.error-icon {
  width: 64px;
  height: 64px;
  color: #9ca3af;
  margin: 0 auto 16px auto;
}

.error-title {
  font-size: 18px;
  font-weight: 500;
  color: #111827;
  margin: 0 0 8px 0;
}

.error-message {
  color: #6b7280;
  margin: 0;
}

/* Footer */
.modal-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}

.nav-buttons {
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-btn {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  cursor: pointer;
  transition: all 0.2s ease;
}

.nav-btn:hover {
  background: #f9fafb;
}

.nav-btn:focus {
  outline: none;
  box-shadow: 0 0 0 2px #3b82f6;
}

.nav-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.question-indicators {
  display: flex;
  align-items: center;
  gap: 4px;
}

.indicator-btn {
  width: 32px;
  height: 32px;
  font-size: 12px;
  font-weight: 500;
  border-radius: 50%;
  border: 1px solid;
  cursor: pointer;
  transition: all 0.2s ease;
}

.indicator-current {
  background: #2563eb;
  color: white;
  border-color: #2563eb;
}

.indicator-answered {
  background: #dcfce7;
  color: #166534;
  border-color: #bbf7d0;
}

.indicator-answered:hover {
  background: #bbf7d0;
}

.indicator-pending {
  background: #f3f4f6;
  color: #6b7280;
  border-color: #d1d5db;
}

.indicator-pending:hover {
  background: #e5e7eb;
}

.submit-btn {
  display: flex;
  align-items: center;
  padding: 8px 24px;
  font-size: 14px;
  font-weight: 500;
  color: white;
  background: #059669;
  border: 1px solid transparent;
  border-radius: 6px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  cursor: pointer;
  transition: all 0.2s ease;
}

.submit-btn:hover {
  background: #047857;
}

.submit-btn:focus {
  outline: none;
  box-shadow: 0 0 0 2px #10b981;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 8px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Icon utilities */
.icon-xs {
  width: 16px;
  height: 16px;
}

.icon-sm {
  width: 24px;
  height: 24px;
}
</style>