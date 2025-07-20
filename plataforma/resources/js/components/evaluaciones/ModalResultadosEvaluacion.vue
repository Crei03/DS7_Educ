<template>
  <div v-if="isOpen" class="modal-overlay" @click="cerrarModal">
    <div class="modal-container" @click.stop>
      <div class="modal-header">
        <h2 class="modal-title">Resultados de Evaluación</h2>
        <button class="btn-cerrar" @click="cerrarModal">
          <XMarkIcon class="icon" />
        </button>
      </div>

      <div v-if="loading" class="modal-loading">
        <div class="spinner"></div>
        <p>Cargando resultados...</p>
      </div>

      <div v-else-if="error" class="modal-error">
        <ExclamationTriangleIcon class="icon-error" />
        <p>{{ error }}</p>
      </div>

      <div v-else-if="resultados" class="modal-content">
        <!-- Información de la evaluación -->
        <div class="evaluacion-info">
          <h3>{{ resultados.evaluacion.titulo }}</h3>
          <div class="evaluacion-meta">
            <span class="meta-item">
              <ClipboardDocumentCheckIcon class="meta-icon" />
              {{ resultados.evaluacion.total_preguntas }} preguntas
            </span>
          </div>
        </div>

        <!-- Estadísticas generales -->
        <div class="estadisticas-card">
          <h4>Estadísticas Generales</h4>
          <div class="stats-grid">
            <div class="stat-item">
              <span class="stat-value">{{ resultados.estadisticas.total_participantes }}</span>
              <span class="stat-label">Participantes</span>
            </div>
            <div class="stat-item">
              <span class="stat-value">{{ resultados.estadisticas.promedio }}%</span>
              <span class="stat-label">Promedio</span>
            </div>
            <div class="stat-item">
              <span class="stat-value">{{ resultados.estadisticas.puntaje_maximo }}%</span>
              <span class="stat-label">Mejor puntaje</span>
            </div>
            <div class="stat-item">
              <span class="stat-value">{{ resultados.estadisticas.puntaje_minimo }}%</span>
              <span class="stat-label">Menor puntaje</span>
            </div>
          </div>
        </div>

        <!-- Tabla de resultados -->
        <div class="resultados-tabla">
          <h4>Detalle de Resultados</h4>
          <div class="tabla-container">
            <table class="tabla-resultados">
              <thead>
                <tr>
                  <th>Estudiante</th>
                  <th>Correo</th>
                  <th>Puntaje</th>
                  <th>Porcentaje</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="resultado in resultados.resultados" :key="resultado.estudiante.id">
                  <td class="estudiante-nombre">{{ resultado.estudiante.nombre }}</td>
                  <td class="estudiante-correo">{{ resultado.estudiante.correo }}</td>
                  <td class="puntaje">{{ resultado.puntaje }}</td>
                  <td>
                    <div class="porcentaje-container">
                      <span 
                        :class="['porcentaje-badge', getPorcentajeClass(resultado.porcentaje)]"
                      >
                        {{ resultado.porcentaje }}%
                      </span>
                    </div>
                  </td>
                  <td class="fecha">{{ formatearFecha(resultado.fecha) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="resultados.resultados.length === 0" class="sin-resultados">
            <ClipboardDocumentListIcon class="icon-empty" />
            <p>No hay resultados para mostrar</p>
            <small>Esta evaluación aún no ha sido resuelta por ningún estudiante.</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import {
  XMarkIcon,
  ExclamationTriangleIcon,
  ClipboardDocumentCheckIcon,
  ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline'
import { useEvaluacionesStore } from '@/stores/evaluaciones'

const props = defineProps({
  isOpen: Boolean,
  evaluacionId: Number
})

const emit = defineEmits(['close'])

const evaluacionesStore = useEvaluacionesStore()

const loading = ref(false)
const error = ref(null)
const resultados = ref(null)

const cerrarModal = () => {
  emit('close')
  // Limpiar datos al cerrar
  resultados.value = null
  error.value = null
}

const getPorcentajeClass = (porcentaje) => {
  if (porcentaje >= 90) return 'excelente'
  if (porcentaje >= 80) return 'bueno'
  if (porcentaje >= 70) return 'regular'
  return 'malo'
}

const formatearFecha = (fecha) => {
  if (!fecha) return 'N/A'
  
  try {
    const date = new Date(fecha)
    return new Intl.DateTimeFormat('es-PA', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(date)
  } catch (e) {
    return fecha.toString().substring(0, 10)
  }
}

const cargarResultados = async () => {
  if (!props.evaluacionId) return

  loading.value = true
  error.value = null

  try {
    const data = await evaluacionesStore.fetchResultadosEvaluacion(props.evaluacionId)
    resultados.value = data
  } catch (e) {
    error.value = 'Error al cargar los resultados'
    console.error('Error:', e)
  } finally {
    loading.value = false
  }
}

// Cargar resultados cuando se abre el modal
watch(() => props.isOpen, (isOpen) => {
  if (isOpen && props.evaluacionId) {
    cargarResultados()
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
  max-width: 900px;
  max-height: 90vh;
  overflow: auto;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
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
  padding: 1.5rem;
}

.evaluacion-info {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.evaluacion-info h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.evaluacion-meta {
  display: flex;
  gap: 1rem;
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

.estadisticas-card {
  background: #f9fafb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.estadisticas-card h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 1rem;
}

.stat-item {
  text-align: center;
  background: white;
  padding: 1rem;
  border-radius: 0.5rem;
}

.stat-value {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #0554f2;
}

.stat-label {
  display: block;
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.resultados-tabla h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
}

.tabla-container {
  overflow-x: auto;
  border-radius: 0.5rem;
  border: 1px solid #e5e7eb;
}

.tabla-resultados {
  width: 100%;
  border-collapse: collapse;
}

.tabla-resultados th {
  background: #f9fafb;
  padding: 0.75rem 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
  font-size: 0.875rem;
}

.tabla-resultados td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #f3f4f6;
  font-size: 0.875rem;
}

.tabla-resultados tbody tr:hover {
  background: #f9fafb;
}

.estudiante-nombre {
  font-weight: 500;
  color: #1f2937;
}

.estudiante-correo {
  color: #6b7280;
}

.puntaje {
  font-weight: 600;
  color: #1f2937;
}

.porcentaje-container {
  display: flex;
  align-items: center;
}

.porcentaje-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 600;
}

.porcentaje-badge.excelente {
  background: #dcfce7;
  color: #166534;
}

.porcentaje-badge.bueno {
  background: #dbeafe;
  color: #1e40af;
}

.porcentaje-badge.regular {
  background: #fef3c7;
  color: #d97706;
}

.porcentaje-badge.malo {
  background: #fee2e2;
  color: #dc2626;
}

.fecha {
  color: #6b7280;
  font-size: 0.8rem;
}

.sin-resultados {
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
</style>
