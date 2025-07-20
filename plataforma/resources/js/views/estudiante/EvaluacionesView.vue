<template>
  <div class="evaluaciones-view">
    <!-- Header -->
    <div class="page-header">
      <h1 class="page-title">Evaluaciones</h1>
      <p class="page-subtitle">
        Completa las evaluaciones de tus cursos y revisa tu rendimiento académico
      </p>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-icon">
          <AcademicCapIcon class="w-8 h-8"/>
        </div>
        <div class="stat-info">
          <div class="stat-number">{{ puntajePromedio }}%</div>
          <div class="stat-label">Promedio General</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <ClockIcon class="w-8 h-8"/>
        </div>
        <div class="stat-info">
          <div class="stat-number">{{ totalPendientes }}</div>
          <div class="stat-label">Pendientes</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <CheckCircleIcon class="w-8 h-8"/>
        </div>
        <div class="stat-info">
          <div class="stat-number">{{ totalCompletadas }}</div>
          <div class="stat-label">Completadas</div>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="tabs-section">
      <div class="tab-buttons">
        <button
          @click="tabActiva = 'cursos'"
          :class="['tab-btn', { 'tab-btn-active': tabActiva === 'cursos' }]"
        >
          Por Curso
        </button>
        <button
          @click="tabActiva = 'historial'"
          :class="['tab-btn', { 'tab-btn-active': tabActiva === 'historial' }]"
        >
          Historial
        </button>
      </div>
    </div>

    <!-- Contenido de Tabs -->
    <div v-if="tabActiva === 'cursos'">
      <EvaluacionesPorCurso />
    </div>

    <div v-if="tabActiva === 'historial'">
      <HistorialEvaluaciones />
    </div>

    <!-- Modal de Evaluación Activa -->
    <EvaluacionModal 
      v-if="evaluacionEnProgreso"
      @cerrar="cerrarEvaluacion"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { AcademicCapIcon, ClockIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'
import { useEvaluacionesEstudianteStore } from '@/stores/evaluacionesEstudiante'
import { useCursosStore } from '@/stores/cursos'
import EvaluacionesPorCurso from '@/components/evaluaciones/EvaluacionesPorCurso.vue'
import HistorialEvaluaciones from '@/components/evaluaciones/HistorialEvaluaciones.vue'
import EvaluacionModal from '@/components/evaluaciones/EvaluacionModal.vue'

const evaluacionesStore = useEvaluacionesEstudianteStore()
const cursosStore = useCursosStore()

const tabActiva = ref('cursos')

// Computed properties
const puntajePromedio = computed(() => {
  return evaluacionesStore.puntajePromedio
})

const totalPendientes = computed(() => {
  // Contar evaluaciones pendientes de todos los cursos
  let total = 0
  cursosStore.cursosMatriculados.forEach(curso => {
    if (curso.evaluaciones) {
      total += curso.evaluaciones.filter(evaluacion => !evaluacion.resultado_estudiante?.completada).length
    }
  })
  return total
})

const totalCompletadas = computed(() => {
  let total = 0
  cursosStore.cursosMatriculados.forEach(curso => {
    if (curso.evaluaciones) {
      total += curso.evaluaciones.filter(evaluacion => evaluacion.resultado_estudiante?.completada).length
    }
  })
  return total
})

const evaluacionEnProgreso = computed(() => {
  return evaluacionesStore.evaluacionEnProgreso
})

// Methods
const cerrarEvaluacion = () => {
  evaluacionesStore.limpiarEvaluacionActiva()
}

// Lifecycle
onMounted(async () => {
  try {
    console.log('EvaluacionesView montado, iniciando carga de datos...')
    
    // Cargar cursos matriculados si no están cargados
    if (cursosStore.cursosMatriculados.length === 0) {
      console.log('Cargando cursos matriculados...')
      await cursosStore.cargarCursosMatriculados()
    }
    
    // Cargar historial de evaluaciones
    console.log('Cargando historial de evaluaciones...')
    await evaluacionesStore.cargarHistorialEvaluaciones()
    
    console.log('Todos los datos cargados correctamente')
  } catch (error) {
    console.error('Error al cargar datos iniciales:', error)
    
    // Si hay error 401, es problema de autenticación
    if (error.response?.status === 401) {
      console.error('Error de autenticación detectado')
      // Opcional: redirigir a login o mostrar mensaje
    }
  }
})
</script>

<style scoped>
.evaluaciones-view {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: bold;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.page-subtitle {
  font-size: 1.1rem;
  color: #6b7280;
  margin: 0;
}

/* Estadísticas */
.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  color: #0554f2;
}

.stat-info {
  flex: 1;
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  color: #111827;
  line-height: 1;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

/* Tabs */
.tabs-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.tab-buttons {
  display: flex;
  gap: 0.5rem;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 0;
}

.tab-btn {
  padding: 0.75rem 1.25rem;
  border: none;
  border-bottom: 3px solid transparent;
  background: transparent;
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border-radius: 6px 6px 0 0;
}

.tab-btn:hover {
  color: #374151;
  background: #f9fafb;
}

.tab-btn-active {
  color: #0554f2;
  border-bottom-color: #0554f2;
  background: #f0f7ff;
}

.w-8 { width: 2rem; }
.h-8 { height: 2rem; }
</style>
