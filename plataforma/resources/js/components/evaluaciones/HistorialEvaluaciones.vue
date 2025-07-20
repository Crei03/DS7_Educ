<template>
  <div class="historial-evaluaciones">
    <!-- Loading State -->
    <div v-if="cargandoHistorial" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando historial...</p>
    </div>

    <!-- Sin historial -->
    <div v-else-if="historialEvaluaciones.length === 0" class="empty-state">
      <ClipboardDocumentListIcon class="empty-icon"/>
      <h3>Sin evaluaciones completadas</h3>
      <p>Completa tu primera evaluación para ver tu historial aquí.</p>
    </div>

    <!-- Historial de evaluaciones -->
    <div v-else class="historial-container">
      <!-- Header con filtros -->
      <div class="historial-header">
        <div class="historial-title-section">
          <h3 class="historial-title">
            Historial de Evaluaciones ({{ historialEvaluaciones.length }})
          </h3>
        </div>
        
        <!-- Filtros -->
        <div class="filtros-container">
          <select 
            v-model="filtroOrden"
            class="filtro-select"
          >
            <option value="fecha_desc">Más reciente</option>
            <option value="fecha_asc">Más antiguo</option>
            <option value="puntaje_desc">Mayor puntaje</option>
            <option value="puntaje_asc">Menor puntaje</option>
          </select>
          
          <select 
            v-model="filtroCurso"
            class="filtro-select"
          >
            <option value="">Todos los cursos</option>
            <option v-for="curso in cursosUnicos" :key="curso" :value="curso">
              {{ curso }}
            </option>
          </select>
        </div>
      </div>

      <!-- Lista de evaluaciones -->
      <div class="evaluaciones-list">
        <div 
          v-for="resultado in historialFiltrado" 
          :key="`${resultado.evaluacion_id}-${resultado.fecha}`"
          class="evaluacion-item"
        >
          <div class="evaluacion-content">
            <div class="evaluacion-header">
              <h4 class="evaluacion-titulo">
                {{ resultado.evaluacion_titulo }}
              </h4>
              
              <!-- Badge de puntaje -->
              <span 
                class="puntaje-badge"
                :class="badgeColorPorPuntaje(resultado.puntaje)"
              >
                {{ resultado.puntaje }}%
              </span>
            </div>

            <div class="evaluacion-meta">
              <span class="meta-item">
                <BookOpenIcon class="meta-icon"/>
                {{ resultado.curso_titulo }}
              </span>
              
              <span class="meta-item">
                <CalendarDaysIcon class="meta-icon"/>
                {{ formatearFecha(resultado.fecha) }}
              </span>
              
              <span v-if="resultado.duracion_min" class="meta-item">
                <ClockIcon class="meta-icon"/>
                {{ resultado.duracion_min }} min
              </span>
            </div>

            <!-- Barra de progreso visual -->
            <div class="rendimiento-container">
              <span class="rendimiento-label">Rendimiento:</span>
              <div class="progress-container">
                <div class="progress-bar">
                  <div 
                    class="progress-fill"
                    :class="barraColorPorPuntaje(resultado.puntaje)"
                    :style="{ width: `${resultado.puntaje}%` }"
                  ></div>
                </div>
                <span class="clasificacion" :class="textoColorPorPuntaje(resultado.puntaje)">
                  {{ clasificacionPorPuntaje(resultado.puntaje) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="evaluacion-actions">
            <button
              @click="verDetalleResultado(resultado)"
              class="btn-outline"
            >
              <DocumentTextIcon class="btn-icon"/>
              Ver Detalle
            </button>
          </div>
        </div>
      </div>

      <!-- Paginación simple si es necesaria -->
      <div v-if="historialFiltrado.length > 10" class="pagination-info">
        <div class="pagination-text">
          Mostrando <span class="pagination-highlight">1</span> a <span class="pagination-highlight">{{ Math.min(10, historialFiltrado.length) }}</span> de
          <span class="pagination-highlight">{{ historialFiltrado.length }}</span> resultados
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { 
  ClipboardDocumentListIcon, 
  BookOpenIcon, 
  CalendarDaysIcon, 
  ClockIcon, 
  DocumentTextIcon 
} from '@heroicons/vue/24/outline'
import { useEvaluacionesEstudianteStore } from '@/stores/evaluacionesEstudiante'

const evaluacionesStore = useEvaluacionesEstudianteStore()

// Filtros
const filtroOrden = ref('fecha_desc')
const filtroCurso = ref('')

// Computed properties
const historialEvaluaciones = computed(() => evaluacionesStore.historialEvaluaciones)
const cargandoHistorial = computed(() => evaluacionesStore.cargandoHistorial)

const cursosUnicos = computed(() => {
  const cursos = new Set()
  historialEvaluaciones.value.forEach(resultado => {
    cursos.add(resultado.curso_titulo)
  })
  return Array.from(cursos).sort()
})

const historialFiltrado = computed(() => {
  let resultado = [...historialEvaluaciones.value]
  
  // Filtrar por curso
  if (filtroCurso.value) {
    resultado = resultado.filter(item => item.curso_titulo === filtroCurso.value)
  }
  
  // Ordenar
  resultado.sort((a, b) => {
    switch (filtroOrden.value) {
      case 'fecha_desc':
        return new Date(b.fecha) - new Date(a.fecha)
      case 'fecha_asc':
        return new Date(a.fecha) - new Date(b.fecha)
      case 'puntaje_desc':
        return b.puntaje - a.puntaje
      case 'puntaje_asc':
        return a.puntaje - b.puntaje
      default:
        return 0
    }
  })
  
  return resultado
})

// Methods
const formatearFecha = (fecha) => {
  return new Date(fecha).toLocaleDateString('es-PA', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const badgeColorPorPuntaje = (puntaje) => {
  if (puntaje >= 90) return 'badge-excellent'
  if (puntaje >= 80) return 'badge-good'
  if (puntaje >= 70) return 'badge-regular'
  return 'badge-poor'
}

const barraColorPorPuntaje = (puntaje) => {
  if (puntaje >= 90) return 'progress-excellent'
  if (puntaje >= 80) return 'progress-good'
  if (puntaje >= 70) return 'progress-regular'
  return 'progress-poor'
}

const textoColorPorPuntaje = (puntaje) => {
  if (puntaje >= 90) return 'text-excellent'
  if (puntaje >= 80) return 'text-good'
  if (puntaje >= 70) return 'text-regular'
  return 'text-poor'
}

const clasificacionPorPuntaje = (puntaje) => {
  if (puntaje >= 90) return 'Excelente'
  if (puntaje >= 80) return 'Muy Bueno'
  if (puntaje >= 70) return 'Bueno'
  if (puntaje >= 60) return 'Regular'
  return 'Deficiente'
}

const verDetalleResultado = (resultado) => {
  // Implementar vista detallada del resultado
  // Puede ser un modal o navegación a nueva página
  console.log('Ver detalle:', resultado)
}
</script>

<style scoped>
.loading-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
  color: #6b7280;
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.spinner {
  width: 32px;
  height: 32px;
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

.empty-icon {
  width: 4rem;
  height: 4rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.empty-state p {
  margin: 0;
}

.historial-container {
  background: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  overflow: hidden;
}

.historial-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  flex-wrap: wrap;
  gap: 1rem;
}

.historial-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.filtros-container {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-wrap: wrap;
}

.filtro-select {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  min-width: 120px;
  cursor: pointer;
}

.filtro-select:focus {
  outline: none;
  border-color: #0554f2;
  box-shadow: 0 0 0 3px rgba(5, 84, 242, 0.1);
}

.evaluaciones-list {
  border-top: 1px solid #e5e7eb;
}

.evaluacion-item {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  transition: background-color 0.2s;
  gap: 1.5rem;
}

.evaluacion-item:hover {
  background: #f9fafb;
}

.evaluacion-item:last-child {
  border-bottom: none;
}

.evaluacion-content {
  flex: 1;
}

.evaluacion-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
}

.evaluacion-titulo {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.puntaje-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
}

.badge-excellent {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.badge-good {
  background: rgba(5, 84, 242, 0.1);
  color: #0554f2;
}

.badge-regular {
  background: rgba(251, 146, 60, 0.1);
  color: #f59e0b;
}

.badge-poor {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.evaluacion-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-bottom: 0.75rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.meta-icon {
  width: 1rem;
  height: 1rem;
}

.rendimiento-container {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.rendimiento-label {
  font-size: 0.875rem;
  color: #6b7280;
  flex-shrink: 0;
}

.progress-container {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 1;
  max-width: 300px;
}

.progress-bar {
  flex: 1;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-excellent { background: #10b981; }
.progress-good { background: #0554f2; }
.progress-regular { background: #f59e0b; }
.progress-poor { background: #ef4444; }

.clasificacion {
  font-size: 0.875rem;
  font-weight: 500;
  flex-shrink: 0;
}

.text-excellent { color: #10b981; }
.text-good { color: #0554f2; }
.text-regular { color: #f59e0b; }
.text-poor { color: #ef4444; }

.evaluacion-actions {
  flex-shrink: 0;
}

.btn-icon {
  width: 1rem;
  height: 1rem;
}

.pagination-info {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
}

.pagination-text {
  font-size: 0.875rem;
  color: #6b7280;
}

.pagination-highlight {
  font-weight: 600;
  color: #374151;
}
</style>
