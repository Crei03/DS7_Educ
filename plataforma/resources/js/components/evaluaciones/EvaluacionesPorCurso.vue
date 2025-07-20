<template>
  <div class="evaluaciones-por-curso">
    <!-- Loading State -->
    <div v-if="cargandoCursos" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando cursos...</p>
    </div>

    <!-- Sin cursos matriculados -->
    <div v-else-if="cursosMatriculados.length === 0" class="empty-state">
      <BookOpenIcon class="empty-icon"/>
      <h3>No tienes cursos matriculados</h3>
      <p>Regístrate en un curso para acceder a las evaluaciones</p>
      <router-link to="/estudiante/cursos" class="btn-primary">
        Explorar Cursos
      </router-link>
    </div>

    <!-- Lista de cursos con evaluaciones -->
    <div v-else class="cursos-list">
      <div 
        v-for="curso in cursosConEvaluaciones" 
        :key="curso.id"
        class="curso-card"
      >
        <!-- Header del curso -->
        <div class="curso-header">
          <div class="curso-info">
            <h3 class="curso-titulo">{{ curso.titulo }}</h3>
            <p class="curso-evaluaciones-count">
              {{ curso.evaluaciones?.length || 0 }} evaluaciones disponibles
            </p>
          </div>
          
          <div class="curso-actions">
            <!-- Promedio del curso -->
            <div v-if="promedioDelCurso(curso) > 0" class="promedio-curso">
              <p class="promedio-label">Promedio</p>
              <p class="promedio-valor" :class="colorPorPuntaje(promedioDelCurso(curso))">
                {{ promedioDelCurso(curso) }}%
              </p>
            </div>
            
            <!-- Botón para cargar evaluaciones -->
            <button
              @click="toggleEvaluacionesCurso(curso)"
              :class="['btn-outline', { 'loading': cargandoEvaluaciones }]"
              :disabled="cargandoEvaluaciones"
            >
              <template v-if="!curso.evaluacionesCargadas && !cargandoEvaluaciones">
                <EyeIcon class="btn-icon"/>
                Ver Evaluaciones
              </template>
              <template v-else-if="cargandoEvaluaciones">
                <div class="spinner-small"></div>
                Cargando...
              </template>
              <template v-else>
                <EyeSlashIcon class="btn-icon"/>
                Ocultar
              </template>
            </button>
          </div>
        </div>

        <!-- Lista de evaluaciones -->
        <div v-if="curso.evaluacionesCargadas" class="evaluaciones-list">
          <div 
            v-for="evaluacion in curso.evaluaciones" 
            :key="evaluacion.id"
            class="evaluacion-item"
          >
            <div class="evaluacion-content">
              <div class="evaluacion-header">
                <h4 class="evaluacion-titulo">{{ evaluacion.titulo }}</h4>
                
                <!-- Badge de estado -->
                <span
                  v-if="evaluacion.resultado_estudiante?.completada"
                  class="status-badge status-completed"
                >
                  <CheckCircleIcon class="status-icon"/>
                  Completada
                </span>
                <span
                  v-else
                  class="status-badge status-pending"
                >
                  <ClockIcon class="status-icon"/>
                  Pendiente
                </span>
              </div>

              <div class="evaluacion-meta">
                <span class="meta-item">
                  <QuestionMarkCircleIcon class="meta-icon"/>
                  {{ evaluacion.total_preguntas }} preguntas
                </span>
                
                <span v-if="evaluacion.duracion_min" class="meta-item">
                  <ClockIcon class="meta-icon"/>
                  {{ evaluacion.duracion_min }} minutos
                </span>
              </div>

              <!-- Resultado si está completada -->
              <div v-if="evaluacion.resultado_estudiante?.completada" class="evaluacion-resultado">
                <div class="resultado-info">
                  <span class="resultado-label">Puntaje:</span>
                  <span class="resultado-puntaje" :class="colorPorPuntaje(evaluacion.resultado_estudiante.puntaje)">
                    {{ evaluacion.resultado_estudiante.puntaje }}%
                  </span>
                </div>
                <div class="resultado-fecha">
                  Completada el {{ formatearFecha(evaluacion.resultado_estudiante.fecha) }}
                </div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="evaluacion-actions">
              <button
                v-if="!evaluacion.resultado_estudiante?.completada"
                @click="iniciarEvaluacion(evaluacion)"
                class="btn-primary"
              >
                <PlayIcon class="btn-icon"/>
                Iniciar Evaluación
              </button>
              
              <button
                v-else
                @click="verResultadoDetallado(evaluacion)"
                class="btn-outline"
              >
                <DocumentTextIcon class="btn-icon"/>
                Ver Resultado
              </button>
            </div>
          </div>
        </div>

        <!-- Estado vacío si no hay evaluaciones -->
        <div v-else-if="curso.evaluacionesCargadas && (!curso.evaluaciones || curso.evaluaciones.length === 0)" class="empty-evaluaciones">
          <ClipboardDocumentListIcon class="empty-evaluaciones-icon"/>
          <h4>Sin evaluaciones</h4>
          <p>Este curso aún no tiene evaluaciones disponibles.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { 
  BookOpenIcon, 
  EyeIcon, 
  EyeSlashIcon, 
  CheckCircleIcon, 
  ClockIcon, 
  QuestionMarkCircleIcon,
  PlayIcon,
  DocumentTextIcon,
  ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline'
import { useEvaluacionesEstudianteStore } from '@/stores/evaluacionesEstudiante'
import { useCursosStore } from '@/stores/cursos'

const evaluacionesStore = useEvaluacionesEstudianteStore()
const cursosStore = useCursosStore()

// Estado local para manejar las evaluaciones por curso
const cursosEvaluaciones = reactive({})

// Computed properties
const cursosMatriculados = computed(() => cursosStore.cursosMatriculados)
const cargandoCursos = computed(() => cursosStore.loading)
const cargandoEvaluaciones = computed(() => evaluacionesStore.cargandoEvaluaciones)

const cursosConEvaluaciones = computed(() => {
  return cursosMatriculados.value.map(curso => ({
    ...curso,
    evaluacionesCargadas: cursosEvaluaciones[curso.id]?.cargadas || false,
    evaluaciones: cursosEvaluaciones[curso.id]?.evaluaciones || []
  }))
})

// Methods
const toggleEvaluacionesCurso = async (curso) => {
  // Si ya están cargadas, ocultar
  if (cursosEvaluaciones[curso.id]?.cargadas) {
    cursosEvaluaciones[curso.id].cargadas = false
    return
  }

  try {
    console.log(`Cargando evaluaciones para curso ${curso.id}...`)
    const evaluaciones = await evaluacionesStore.cargarEvaluacionesCurso(curso.id)
    
    // Inicializar si no existe
    if (!cursosEvaluaciones[curso.id]) {
      cursosEvaluaciones[curso.id] = {}
    }
    
    cursosEvaluaciones[curso.id].evaluaciones = evaluaciones
    cursosEvaluaciones[curso.id].cargadas = true
    
    console.log(`Evaluaciones cargadas para curso ${curso.id}:`, evaluaciones)
  } catch (error) {
    console.error('Error al cargar evaluaciones del curso:', error)
    
    // Mostrar mensaje de error específico para problemas de autenticación
    if (error.response?.status === 401) {
      console.error('Error de autenticación al cargar evaluaciones')
    }
  }
}

const iniciarEvaluacion = async (evaluacion) => {
  try {
    await evaluacionesStore.iniciarEvaluacion(evaluacion.id)
    // El modal se abrirá automáticamente por el computed evaluacionEnProgreso
  } catch (error) {
    console.error('Error al iniciar evaluación:', error)
    // Mostrar notificación de error aquí
  }
}

const verResultadoDetallado = (evaluacion) => {
  // Navegar a vista detallada del resultado
  // O abrir modal con detalles
  console.log('Ver resultado detallado:', evaluacion)
}

const promedioDelCurso = (curso) => {
  if (!curso.evaluaciones || curso.evaluaciones.length === 0) return 0
  
  const completadas = curso.evaluaciones.filter(evaluacion => evaluacion.resultado_estudiante?.completada)
  if (completadas.length === 0) return 0
  
  const suma = completadas.reduce((acc, evaluacion) => acc + evaluacion.resultado_estudiante.puntaje, 0)
  return Math.round(suma / completadas.length)
}

const colorPorPuntaje = (puntaje) => {
  if (puntaje >= 90) return 'color-excellent'
  if (puntaje >= 80) return 'color-good'
  if (puntaje >= 70) return 'color-regular'
  return 'color-poor'
}

const formatearFecha = (fecha) => {
  return new Date(fecha).toLocaleDateString('es-PA', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
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

.spinner-small {
  width: 16px;
  height: 16px;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #0554f2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
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
  margin: 0 0 1.5rem 0;
}

.cursos-list {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.curso-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.curso-header {
  background: #f9fafb;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}

.curso-info {
  flex: 1;
}

.curso-titulo {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.curso-evaluaciones-count {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.curso-actions {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.promedio-curso {
  text-align: right;
}

.promedio-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0 0 0.25rem 0;
}

.promedio-valor {
  font-size: 1.25rem;
  font-weight: bold;
  margin: 0;
}

.evaluaciones-list {
  border-top: 1px solid #e5e7eb;
}

.evaluacion-item {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1.5rem;
  transition: background-color 0.2s;
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
  gap: 1rem;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
}

.evaluacion-titulo {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.status-badge {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-completed {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.status-pending {
  background: rgba(251, 146, 60, 0.1);
  color: #f59e0b;
}

.status-icon {
  width: 0.75rem;
  height: 0.75rem;
}

.evaluacion-meta {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
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

.evaluacion-resultado {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.resultado-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.resultado-label {
  font-size: 0.875rem;
  color: #6b7280;
}

.resultado-puntaje {
  font-weight: bold;
  font-size: 1.125rem;
}

.resultado-fecha {
  font-size: 0.875rem;
  color: #6b7280;
}

.evaluacion-actions {
  flex-shrink: 0;
}

.empty-evaluaciones {
  padding: 3rem;
  text-align: center;
  color: #6b7280;
}

.empty-evaluaciones-icon {
  width: 3rem;
  height: 3rem;
  color: #d1d5db;
  margin: 0 auto 1rem;
}

.empty-evaluaciones h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.empty-evaluaciones p {
  margin: 0;
}

.btn-icon {
  width: 1rem;
  height: 1rem;
}

/* Color classes for scores */
.color-excellent { color: #10b981; }
.color-good { color: #0554f2; }
.color-regular { color: #f59e0b; }
.color-poor { color: #ef4444; }
</style>
