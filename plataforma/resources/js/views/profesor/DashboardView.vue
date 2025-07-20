<template>
  <div class="dashboard-container">
    <div class="dashboard-header">
      <h1 class="dashboard-title">Dashboard</h1>
      <p class="dashboard-subtitle">Bienvenido al portal de profesores, {{ authStore.user?.nombrePrimario }}</p>
    </div>
    <div v-if="loading" class="dashboard-loading">
      <div class="spinner"></div>
      <p>Cargando datos...</p>
    </div>
    <div v-else class="dashboard-cards">
      <div class="dashboard-card">
        <div class="dashboard-card-icon dashboard-card-icon-blue">
          <BookOpenIcon class="icon" />
        </div>
        <div class="dashboard-card-info">
          <p class="dashboard-card-number">{{ stats.totalCursos }}</p>
          <p class="dashboard-card-label">Cursos activos</p>
        </div>
      </div>
      <div class="dashboard-card">
        <div class="dashboard-card-icon dashboard-card-icon-green">
          <UsersIcon class="icon" />
        </div>
        <div class="dashboard-card-info">
          <p class="dashboard-card-number">{{ stats.totalEstudiantes }}</p>
          <p class="dashboard-card-label">Estudiantes</p>
        </div>
      </div>
      <div class="dashboard-card">
        <div class="dashboard-card-icon dashboard-card-icon-purple">
          <ClipboardDocumentListIcon class="icon" />
        </div>
        <div class="dashboard-card-info">
          <p class="dashboard-card-number">{{ stats.totalEvaluaciones }}</p>
          <p class="dashboard-card-label">Evaluaciones</p>
        </div>
      </div>
      <div class="dashboard-card">
        <div class="dashboard-card-icon dashboard-card-icon-yellow">
          <DocumentTextIcon class="icon" />
        </div>
        <div class="dashboard-card-info">
          <p class="dashboard-card-number">{{ stats.totalMateriales }}</p>
          <p class="dashboard-card-label">Materiales</p>
        </div>
      </div>
    </div>
    <div class="dashboard-actions">
      <div class="dashboard-action-card">
        <h3 class="dashboard-action-title">Acciones rápidas</h3>
        <div class="dashboard-action-links">
          <router-link to="/profesor/crear-curso" class="dashboard-action-btn dashboard-action-btn-primary">
            <PlusIcon class="icon" />
            Crear nuevo curso
          </router-link>
          <router-link to="/profesor/evaluaciones" class="dashboard-action-btn dashboard-action-btn-secondary">
            <ClipboardDocumentListIcon class="icon" />
            Nueva evaluación
          </router-link>
        </div>
      </div>
      <div class="dashboard-action-card">
        <h3 class="dashboard-action-title">Actividad reciente</h3>
        <div v-if="actividadReciente.length === 0" class="dashboard-activity-empty">
          <p>No hay actividad reciente</p>
        </div>
        <div v-else class="dashboard-activity-list">
          <div 
            v-for="(actividad, index) in actividadReciente" 
            :key="index"
            :class="['dashboard-activity-item', `dashboard-activity-${actividad.tipo}`]"
          >
            <span class="dashboard-activity-dot"></span>
            <span class="dashboard-activity-text">{{ actividad.mensaje }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { 
  BookOpenIcon,
  UsersIcon,
  ClipboardDocumentListIcon,
  DocumentTextIcon,
  PlusIcon
} from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/stores/auth'
import { useCursosStore } from '@/stores/cursos'
import { useEvaluacionesStore } from '@/stores/evaluaciones'
import { useMaterialesStore } from '@/stores/materiales'

const authStore = useAuthStore()
const cursosStore = useCursosStore()
const evaluacionesStore = useEvaluacionesStore()
const materialesStore = useMaterialesStore()

const loading = ref(true)

const stats = reactive({
  totalCursos: 0,
  totalEstudiantes: 0,
  totalEvaluaciones: 0,
  totalMateriales: 0
})

const actividadReciente = ref([])

const cargarDatos = async () => {
  try {
    loading.value = true
    const profesorId = authStore.user?.id
    
    if (!profesorId) {
      console.error('No se encontró ID del profesor')
      return
    }
    
    // Cargar datos específicos del profesor
    await Promise.all([
      cursosStore.fetchCursosProfesor(profesorId),
      evaluacionesStore.fetchEvaluacionesProfesor(profesorId),
      materialesStore.fetchMaterialesProfesor(profesorId)
    ])

    // Actualizar estadísticas basadas en datos del profesor
    stats.totalCursos = cursosStore.cursosProfesor.length
    stats.totalEvaluaciones = evaluacionesStore.evaluaciones.length
    stats.totalMateriales = materialesStore.materiales.length
    
    // Calcular total de estudiantes de todos los cursos del profesor
    stats.totalEstudiantes = cursosStore.cursosProfesor.reduce((total, curso) => {
      return total + (curso.estudiantes_count || 0)
    }, 0)

    // Generar actividad reciente basada en datos reales
    generarActividadReciente()
    
  } catch (error) {
    console.error('Error cargando datos del dashboard:', error)
  } finally {
    loading.value = false
  }
}

const generarActividadReciente = () => {
  const actividades = []
  
  // Últimos cursos creados del profesor
  const cursosRecientes = cursosStore.cursosProfesor.slice(-2).reverse()
  cursosRecientes.forEach(curso => {
    actividades.push({
      tipo: 'blue',
      mensaje: `Curso "${curso.titulo}" creado`
    })
  })
  
  // Últimas evaluaciones creadas del profesor
  const evaluacionesRecientes = evaluacionesStore.evaluaciones.slice(-2).reverse()
  evaluacionesRecientes.forEach(evaluacion => {
    actividades.push({
      tipo: 'purple',
      mensaje: `Evaluación "${evaluacion.titulo}" creada`
    })
  })
  
  // Últimos materiales subidos del profesor
  const materialesRecientes = materialesStore.materiales.slice(-2).reverse()
  materialesRecientes.forEach(material => {
    actividades.push({
      tipo: 'green',
      mensaje: `Material "${material.titulo || 'Sin título'}" subido`
    })
  })
  
  // Mostrar máximo 4 actividades, ordenadas por relevancia
  actividadReciente.value = actividades.slice(0, 4)
  
  // Si no hay actividades, mostrar mensaje por defecto
  if (actividades.length === 0) {
    actividadReciente.value = [{
      tipo: 'blue',
      mensaje: 'Bienvenido al sistema. ¡Comienza creando tu primer curso!'
    }]
  }
}

onMounted(() => {
  cargarDatos()
})
</script>

<style scoped>
.dashboard-container {
  padding: 2rem;
}
.dashboard-header {
  margin-bottom: 2rem;
}
.dashboard-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}
.dashboard-subtitle {
  color: #6b7280;
  font-size: 1.1rem;
}
.dashboard-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}
.dashboard-card {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  display: flex;
  align-items: center;
  transition: box-shadow 0.2s;
}
.dashboard-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.10);
}
.dashboard-card-icon {
  padding: 0.5rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
}
.dashboard-card-icon-blue { background: #eaf1fe; }
.dashboard-card-icon-green { background: #eafbe7; }
.dashboard-card-icon-purple { background: #f3eafc; }
.dashboard-card-icon-yellow { background: #fffbe7; }
.icon {
  width: 28px;
  height: 28px;
}
.dashboard-card-info {
  margin-left: 1rem;
}
.dashboard-card-number {
  font-size: 1.5rem;
  font-weight: bold;
  color: #1f2937;
}
.dashboard-card-label {
  font-size: 1rem;
  color: #6b7280;
}
.dashboard-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}
.dashboard-action-card {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.dashboard-action-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
}
.dashboard-action-links {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.dashboard-action-btn {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.25rem;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.2s;
  margin-bottom: 0.5rem;
}
.dashboard-action-btn-primary {
  background: #0554f2;
  color: #fff;
}
.dashboard-action-btn-primary:hover {
  background: #0540f2;
}
.dashboard-action-btn-secondary {
  background: #f2f2f2;
  color: #374151;
}
.dashboard-action-btn-secondary:hover {
  background: #e5e7eb;
}
.dashboard-activity-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}
.dashboard-activity-item {
  display: flex;
  align-items: center;
  background: #f9fafb;
  border-radius: 0.5rem;
  padding: 0.75rem 1rem;
}
.dashboard-activity-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 0.75rem;
  display: inline-block;
}
.dashboard-activity-green .dashboard-activity-dot { background: #22c55e; }
.dashboard-activity-blue .dashboard-activity-dot { background: #3b82f6; }
.dashboard-activity-purple .dashboard-activity-dot { background: #a78bfa; }
.dashboard-activity-text {
  font-size: 0.95rem;
  color: #374151;
}

.dashboard-loading {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
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

.dashboard-activity-empty {
  text-align: center;
  padding: 1rem;
  color: #6b7280;
  font-style: italic;
}
</style>
