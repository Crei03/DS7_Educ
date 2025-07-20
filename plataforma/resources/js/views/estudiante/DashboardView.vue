<template>
  <div class="estudiante-dashboard">
    <!-- Header de bienvenida -->
    <div class="welcome-header">
      <div class="welcome-content">
        <h1 class="welcome-title">
          ¡Hola, {{ userStore.user?.nombrePrimario || 'Estudiante' }}! 👋
        </h1>
        <p class="welcome-subtitle">
          Bienvenido a tu panel de aprendizaje. Aquí puedes ver tu progreso y continuar con tus estudios.
        </p>
      </div>
      <div class="welcome-stats">
        <div class="stat-card">
          <div class="stat-icon">
            <BookOpenIcon class="w-8 h-8" />
          </div>
          <div class="stat-info">
            <div class="stat-number">{{ dashboardData.cursosMatriculados }}</div>
            <div class="stat-label">Cursos Activos</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <ChartBarIcon class="w-8 h-8" />
          </div>
          <div class="stat-info">
            <div class="stat-number">{{ dashboardData.progresoPromedio }}%</div>
            <div class="stat-label">Progreso Promedio</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <TrophyIcon class="w-8 h-8" />
          </div>
          <div class="stat-info">
            <div class="stat-number">{{ dashboardData.certificados }}</div>
            <div class="stat-label">Certificados</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenido principal -->
    <div class="dashboard-content">
      <!-- Cursos en progreso -->
      <section class="dashboard-section">
        <div class="section-header">
          <h2 class="section-title">Mis Cursos en Progreso</h2>
          <router-link to="/estudiante/cursos" class="section-link">
            Ver todos
            <ChevronRightIcon class="w-4 h-4" />
          </router-link>
        </div>

        <div v-if="loading.cursos" class="loading-state">
          <div class="spinner"></div>
          <span>Cargando cursos...</span>
        </div>

        <div v-else-if="cursosEnProgreso.length === 0" class="empty-state">
          <BookOpenIcon class="empty-icon" />
          <h3>¡Comienza tu aprendizaje!</h3>
          <p>Aún no tienes cursos matriculados. Explora nuestro catálogo y encuentra el curso perfecto para ti.</p>
          <router-link to="/estudiante/explorar" class="btn-primary">
            Explorar Cursos
          </router-link>
        </div>

        <div v-else class="courses-grid">
          <div 
            v-for="curso in cursosEnProgreso" 
            :key="curso.id"
            class="course-card"
            @click="navigateToCourse(curso.id)"
          >
            <div class="course-header">
              <div class="course-image">
                <BookOpenIcon class="course-icon" />
              </div>
              <div class="course-info">
                <h3 class="course-title">{{ curso.titulo }}</h3>
                <p class="course-instructor">{{ curso.profesor?.nombreCompleto }}</p>
              </div>
            </div>
            
            <div class="course-progress">
              <div class="progress-header">
                <span class="progress-label">Progreso</span>
                <span class="progress-percentage">{{ curso.progreso }}%</span>
              </div>
              <div class="progress-bar">
                <div 
                  class="progress-fill" 
                  :style="{ width: `${curso.progreso}%` }"
                ></div>
              </div>
            </div>

            <div class="course-meta">
              <div class="meta-item">
                <ClockIcon class="w-4 h-4" />
                <span>{{ curso.tiempoEstimado || 'N/A' }}</span>
              </div>
              <div class="meta-item">
                <UserGroupIcon class="w-4 h-4" />
                <span>{{ curso.totalEstudiantes }} estudiantes</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Próximas evaluaciones -->
      <section class="dashboard-section">
        <div class="section-header">
          <h2 class="section-title">Próximas Evaluaciones</h2>
          <router-link to="/estudiante/evaluaciones" class="section-link">
            Ver todas
            <ChevronRightIcon class="w-4 h-4" />
          </router-link>
        </div>

        <div v-if="loading.evaluaciones" class="loading-state">
          <div class="spinner"></div>
          <span>Cargando evaluaciones...</span>
        </div>

        <div v-else-if="proximasEvaluaciones.length === 0" class="empty-state">
          <ClipboardDocumentListIcon class="empty-icon" />
          <h3>No tienes evaluaciones pendientes</h3>
          <p>¡Excelente! Estás al día con tus evaluaciones.</p>
        </div>

        <div v-else class="evaluations-list">
          <div 
            v-for="evaluacion in proximasEvaluaciones" 
            :key="evaluacion.id"
            class="evaluation-card"
          >
            <div class="evaluation-header">
              <div class="evaluation-icon">
                <ClipboardDocumentListIcon class="w-6 h-6" />
              </div>
              <div class="evaluation-info">
                <h3 class="evaluation-title">{{ evaluacion.titulo }}</h3>
                <p class="evaluation-course">{{ evaluacion.curso?.titulo }}</p>
              </div>
              <div class="evaluation-status">
                <span class="status-badge status-pending">Pendiente</span>
              </div>
            </div>
            
            <div class="evaluation-meta">
              <div class="meta-item">
                <ClockIcon class="w-4 h-4" />
                <span>{{ evaluacion.duracion_min }} minutos</span>
              </div>
              <div class="meta-item">
                <QuestionMarkCircleIcon class="w-4 h-4" />
                <span>{{ evaluacion.total_preguntas }} preguntas</span>
              </div>
            </div>

            <div class="evaluation-actions">
              <button 
                @click="startEvaluation(evaluacion.id)"
                class="btn-primary btn-small"
              >
                Iniciar Evaluación
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Actividad reciente -->
      <section class="dashboard-section">
        <div class="section-header">
          <h2 class="section-title">Actividad Reciente</h2>
        </div>

        <div v-if="loading.actividad" class="loading-state">
          <div class="spinner"></div>
          <span>Cargando actividad...</span>
        </div>

        <div v-else-if="actividadReciente.length === 0" class="empty-state">
          <ChartBarIcon class="empty-icon" />
          <h3>Sin actividad reciente</h3>
          <p>Tu actividad de aprendizaje aparecerá aquí.</p>
        </div>

        <div v-else class="activity-list">
          <div 
            v-for="actividad in actividadReciente" 
            :key="actividad.id"
            class="activity-item"
          >
            <div class="activity-icon">
              <component :is="getActivityIcon(actividad.tipo)" class="w-5 h-5" />
            </div>
            <div class="activity-content">
              <p class="activity-description">{{ actividad.descripcion }}</p>
              <p class="activity-time">{{ formatTime(actividad.fecha) }}</p>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCursosStore } from '@/stores/cursos'
import {
  BookOpenIcon,
  ChartBarIcon,
  TrophyIcon,
  ChevronRightIcon,
  ClockIcon,
  UserGroupIcon,
  ClipboardDocumentListIcon,
  QuestionMarkCircleIcon,
  CheckCircleIcon,
  PlayIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const userStore = useAuthStore()
const cursosStore = useCursosStore()

// Estado de carga
const loading = ref({
  cursos: true,
  evaluaciones: true,
  actividad: true
})

// Datos del dashboard
const dashboardData = computed(() => ({
  cursosMatriculados: cursosStore.cursosMatriculados.length,
  progresoPromedio: cursosStore.progresoPromedio,
  certificados: cursosStore.cursosCompletos.length
}))

const cursosEnProgreso = computed(() => cursosStore.cursosEnProgreso.slice(0, 4)) // Solo los primeros 4
const proximasEvaluaciones = ref([])
const actividadReciente = ref([])

// Métodos
const loadDashboardData = async () => {
  try {
    loading.value.cursos = true
    
    // Cargar cursos matriculados del estudiante
    if (userStore.user?.id) {
      await cursosStore.fetchCursosMatriculados(userStore.user.id)
    }

    // Simular datos de evaluaciones y actividad (para después conectar con el backend real)
    setTimeout(() => {
      proximasEvaluaciones.value = [
        {
          id: 1,
          titulo: 'Evaluación Final PHP',
          curso: { titulo: 'PHP Básico' },
          duracion_min: 45,
          total_preguntas: 20
        }
      ]

      actividadReciente.value = [
        {
          id: 1,
          tipo: 'material_completado',
          descripcion: 'Completaste el módulo "Variables en PHP"',
          fecha: new Date(Date.now() - 2 * 60 * 60 * 1000) // 2 horas atrás
        },
        {
          id: 2,
          tipo: 'evaluacion_completada',
          descripcion: 'Completaste la evaluación "Conceptos Básicos" con 85%',
          fecha: new Date(Date.now() - 24 * 60 * 60 * 1000) // 1 día atrás
        }
      ]

      loading.value = {
        cursos: false,
        evaluaciones: false,
        actividad: false
      }
    }, 1000)
  } catch (error) {
    console.error('Error cargando datos del dashboard:', error)
    loading.value = {
      cursos: false,
      evaluaciones: false,
      actividad: false
    }
  }
}

const navigateToCourse = (courseId) => {
  router.push(`/estudiante/cursos/${courseId}`)
}

const startEvaluation = (evaluationId) => {
  router.push(`/estudiante/evaluaciones/${evaluationId}`)
}

const getActivityIcon = (tipo) => {
  switch (tipo) {
    case 'material_completado':
      return CheckCircleIcon
    case 'evaluacion_completada':
      return ClipboardDocumentListIcon
    case 'video_visto':
      return PlayIcon
    default:
      return BookOpenIcon
  }
}

const formatTime = (fecha) => {
  const now = new Date()
  const diff = now - new Date(fecha)
  const hours = Math.floor(diff / (1000 * 60 * 60))
  const days = Math.floor(hours / 24)
  
  if (days > 0) {
    return `Hace ${days} día${days > 1 ? 's' : ''}`
  } else if (hours > 0) {
    return `Hace ${hours} hora${hours > 1 ? 's' : ''}`
  } else {
    return 'Hace unos minutos'
  }
}

// Lifecycle
onMounted(() => {
  loadDashboardData()
})
</script>

<style scoped>
.estudiante-dashboard {
  max-width: 1200px;
  margin: 0 auto;
}

/* Welcome Header */
.welcome-header {
  background: linear-gradient(135deg, #0554f2 0%, #0540f2 100%);
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
  color: white;
}

.welcome-content {
  margin-bottom: 2rem;
}

.welcome-title {
  font-size: 2rem;
  font-weight: bold;
  margin: 0 0 0.5rem 0;
}

.welcome-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  margin: 0;
}

.welcome-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.stat-card {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  color: #63a1f2;
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  line-height: 1;
}

.stat-label {
  font-size: 0.875rem;
  opacity: 0.8;
}

/* Dashboard Content */
.dashboard-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.dashboard-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.section-link {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: #0554f2;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.section-link:hover {
  color: #0540f2;
}

/* States */
.loading-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  text-align: center;
  color: #6b7280;
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
  font-size: 1.125rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.empty-state p {
  margin: 0 0 1.5rem 0;
}

/* Course Cards */
.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.course-card {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.2s;
}

.course-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.course-header {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.course-image {
  width: 3rem;
  height: 3rem;
  background: #f3f4f6;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.course-icon {
  width: 1.5rem;
  height: 1.5rem;
  color: #0554f2;
}

.course-info {
  flex: 1;
}

.course-title {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.course-instructor {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.course-progress {
  margin-bottom: 1rem;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.progress-label {
  font-size: 0.875rem;
  color: #6b7280;
}

.progress-percentage {
  font-size: 0.875rem;
  font-weight: 600;
  color: #0554f2;
}

.progress-bar {
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #0554f2, #63a1f2);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.course-meta {
  display: flex;
  gap: 1rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.875rem;
  color: #6b7280;
}

/* Evaluation Cards */
.evaluations-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.evaluation-card {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1.5rem;
}

.evaluation-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.evaluation-icon {
  width: 3rem;
  height: 3rem;
  background: #fef3c7;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #d97706;
}

.evaluation-info {
  flex: 1;
}

.evaluation-title {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.evaluation-course {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-pending {
  background: #fef3c7;
  color: #d97706;
}

.evaluation-meta {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.evaluation-actions {
  display: flex;
  justify-content: flex-end;
}

/* Activity List */
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.activity-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
}

.activity-icon {
  width: 2.5rem;
  height: 2.5rem;
  background: #f0f9ff;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0554f2;
  flex-shrink: 0;
}

.activity-content {
  flex: 1;
}

.activity-description {
  font-size: 0.875rem;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.activity-time {
  font-size: 0.75rem;
  color: #6b7280;
  margin: 0;
}

/* Buttons */
.btn-primary {
  background: #0554f2;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary:hover {
  background: #0540f2;
}

.btn-small {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

/* Utility Classes */
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
.w-6 { width: 1.5rem; }
.h-6 { height: 1.5rem; }
.w-8 { width: 2rem; }
.h-8 { height: 2rem; }

/* Responsive */
@media (max-width: 768px) {
  .welcome-header {
    padding: 1.5rem;
  }
  
  .welcome-title {
    font-size: 1.5rem;
  }
  
  .dashboard-section {
    padding: 1rem;
  }
  
  .courses-grid {
    grid-template-columns: 1fr;
  }
}
</style>
