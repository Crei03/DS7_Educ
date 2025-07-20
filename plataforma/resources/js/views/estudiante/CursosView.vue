<template>
  <div class="cursos-view">
    <div class="page-header">
      <h1 class="page-title">Mis Cursos</h1>
      <p class="page-subtitle">
        Aquí puedes ver todos los cursos en los que estás matriculado y continuar con tu aprendizaje.
      </p>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-icon">
          <BookOpenIcon class="w-8 h-8" />
        </div>
        <div class="stat-info">
          <div class="stat-number">{{ cursosStore.cursosMatriculados.length }}</div>
          <div class="stat-label">Cursos Matriculados</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <ChartBarIcon class="w-8 h-8" />
        </div>
        <div class="stat-info">
          <div class="stat-number">{{ cursosStore.progresoPromedio }}%</div>
          <div class="stat-label">Progreso Promedio</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <TrophyIcon class="w-8 h-8" />
        </div>
        <div class="stat-info">
          <div class="stat-number">{{ cursosStore.cursosCompletos.length }}</div>
          <div class="stat-label">Cursos Completados</div>
        </div>
      </div>
    </div>

    <!-- Filtros de cursos -->
    <div class="filters-section">
      <div class="filter-buttons">
        <button
          @click="filtroActivo = 'todos'"
          :class="['filter-btn', { 'filter-btn-active': filtroActivo === 'todos' }]"
        >
          Todos ({{ cursosStore.cursosMatriculados.length }})
        </button>
        <button
          @click="filtroActivo = 'en_progreso'"
          :class="['filter-btn', { 'filter-btn-active': filtroActivo === 'en_progreso' }]"
        >
          En Progreso ({{ cursosStore.cursosEnProgreso.length }})
        </button>
        <button
          @click="filtroActivo = 'completados'"
          :class="['filter-btn', { 'filter-btn-active': filtroActivo === 'completados' }]"
        >
          Completados ({{ cursosStore.cursosCompletos.length }})
        </button>
      </div>

      <div class="search-bar">
        <div class="search-input-group">
          <MagnifyingGlassIcon class="search-icon" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar en mis cursos..."
            class="search-input"
          />
        </div>
      </div>
    </div>

    <!-- Lista de cursos -->
    <div class="courses-section">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <span>Cargando tus cursos...</span>
      </div>

      <div v-else-if="error" class="error-state">
        <ExclamationTriangleIcon class="error-icon" />
        <h3>Error al cargar cursos</h3>
        <p>{{ error }}</p>
        <button @click="loadCursos" class="btn-primary">
          Reintentar
        </button>
      </div>

      <div v-else-if="cursosFiltrados.length === 0" class="empty-state">
        <BookOpenIcon class="empty-icon" />
        <h3 v-if="filtroActivo === 'todos'">¡Comienza tu aprendizaje!</h3>
        <h3 v-else-if="filtroActivo === 'en_progreso'">No tienes cursos en progreso</h3>
        <h3 v-else>No tienes cursos completados</h3>
        
        <p v-if="filtroActivo === 'todos'">
          Aún no tienes cursos matriculados. Explora nuestro catálogo y encuentra el curso perfecto para ti.
        </p>
        <p v-else-if="filtroActivo === 'completados'">
          Continúa aprendiendo y completa tus cursos actuales para obtener certificados.
        </p>
        <p v-else>
          Tus cursos en progreso aparecerán aquí.
        </p>
        
        <router-link 
          v-if="filtroActivo === 'todos'"
          to="/estudiante/explorar" 
          class="btn-primary"
        >
          Explorar Cursos
        </router-link>
      </div>

      <div v-else class="courses-grid">
        <div
          v-for="curso in cursosFiltrados"
          :key="curso.id"
          class="course-card"
          @click="navigateToCourse(curso.id)"
        >
          <!-- Header del curso -->
          <div class="course-header">
            <div class="course-image">
              <BookOpenIcon class="course-icon" />
            </div>
            <div class="course-status">
              <span v-if="curso.progreso >= 100" class="status-badge status-completed">
                <CheckCircleIcon class="w-4 h-4" />
                Completado
              </span>
              <span v-else class="status-badge status-progress">
                <ClockIcon class="w-4 h-4" />
                En Progreso
              </span>
            </div>
          </div>

          <!-- Contenido del curso -->
          <div class="course-content">
            <h3 class="course-title">{{ curso.titulo }}</h3>
            <p class="course-description">{{ curso.descripcion || 'Sin descripción disponible' }}</p>
            
            <div class="course-instructor">
              <UserIcon class="instructor-icon" />
              <span>{{ curso.profesor?.nombreCompleto || 'Profesor no asignado' }}</span>
            </div>

            <!-- Progreso -->
            <div class="course-progress">
              <div class="progress-header">
                <span class="progress-label">Progreso</span>
                <span class="progress-percentage">{{ curso.progreso || 0 }}%</span>
              </div>
              <div class="progress-bar">
                <div 
                  class="progress-fill" 
                  :style="{ width: `${curso.progreso || 0}%` }"
                ></div>
              </div>
            </div>

            <!-- Metadata -->
            <div class="course-meta">
              <div class="meta-item">
                <DocumentTextIcon class="w-4 h-4" />
                <span>{{ curso.modulos_count || 0 }} módulos</span>
              </div>
              <div class="meta-item">
                <ClipboardDocumentListIcon class="w-4 h-4" />
                <span>{{ curso.evaluaciones_count || 0 }} evaluaciones</span>
              </div>
              <div class="meta-item">
                <CalendarDaysIcon class="w-4 h-4" />
                <span>{{ formatDate(curso.fecha_matricula) }}</span>
              </div>
            </div>

            <!-- Última actividad -->
            <div v-if="curso.ultima_actividad" class="last-activity">
              <span class="activity-label">Última actividad:</span>
              <span class="activity-time">{{ formatTime(curso.ultima_actividad) }}</span>
            </div>
          </div>

          <!-- Acciones -->
          <div class="course-actions">
            <button
              @click.stop="verProgresoDetallado(curso)"
              class="btn-outline btn-small"
              title="Ver progreso detallado"
            >
              <ChartBarIcon class="w-4 h-4" />
              Progreso
            </button>
            
            <button
              v-if="curso.progreso >= 100"
              @click.stop="descargarCertificado(curso.id)"
              class="btn-secondary btn-small"
            >
              <DocumentArrowDownIcon class="w-4 h-4" />
              Certificado
            </button>
            
            <button
              @click.stop="continuarCurso(curso)"
              class="btn-primary btn-small"
            >
              <component 
                :is="curso.progreso >= 100 ? 'EyeIcon' : 'PlayIcon'"
                class="w-4 h-4"
              />
              {{ curso.progreso >= 100 ? 'Revisar' : 'Continuar' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación para certificado -->
    <div v-if="showCertificateModal" class="modal-overlay" @click="closeCertificateModal">
      <div class="modal-content certificate-modal" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">¡Felicidades!</h2>
          <button @click="closeCertificateModal" class="modal-close">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>
        
        <div class="modal-body text-center">
          <div class="certificate-icon">
            <TrophyIcon class="w-16 h-16" />
          </div>
          <h3>Has completado el curso</h3>
          <p class="course-name">{{ selectedCourse?.titulo }}</p>
          <p>Tu certificado está listo para descargar</p>
        </div>

        <div class="modal-footer">
          <button @click="closeCertificateModal" class="btn-secondary">
            Cerrar
          </button>
          <button @click="downloadCertificate" class="btn-primary">
            <DocumentArrowDownIcon class="w-4 h-4" />
            Descargar Certificado
          </button>
        </div>
      </div>
    </div>

    <!-- Toast de notificación -->
    <div v-if="notification.show" :class="['notification', `notification-${notification.type}`]">
      <component :is="notification.icon" class="notification-icon" />
      <span>{{ notification.message }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCursosStore } from '@/stores/cursos'
import { useAuthStore } from '@/stores/auth'
import {
  BookOpenIcon,
  ChartBarIcon,
  TrophyIcon,
  MagnifyingGlassIcon,
  ExclamationTriangleIcon,
  UserIcon,
  ClockIcon,
  CheckCircleIcon,
  DocumentTextIcon,
  ClipboardDocumentListIcon,
  CalendarDaysIcon,
  DocumentArrowDownIcon,
  EyeIcon,
  PlayIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const cursosStore = useCursosStore()
const authStore = useAuthStore()

// Estado local
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')
const filtroActivo = ref('todos')
const showCertificateModal = ref(false)
const selectedCourse = ref(null)
const notification = ref({
  show: false,
  type: 'success',
  message: '',
  icon: CheckCircleIcon
})

// Computed
const cursosFiltrados = computed(() => {
  let cursos = cursosStore.cursosMatriculados || []

  // Filtrar por estado
  if (filtroActivo.value === 'en_progreso') {
    cursos = cursosStore.cursosEnProgreso
  } else if (filtroActivo.value === 'completados') {
    cursos = cursosStore.cursosCompletos
  }

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    cursos = cursos.filter(curso =>
      curso.titulo.toLowerCase().includes(query) ||
      curso.descripcion?.toLowerCase().includes(query) ||
      curso.profesor?.nombreCompleto?.toLowerCase().includes(query)
    )
  }

  return cursos
})

// Métodos
const loadCursos = async () => {
  loading.value = true
  error.value = null

  try {
    if (authStore.user?.id) {
      await cursosStore.fetchCursosMatriculados(authStore.user.id)
    }
  } catch (err) {
    error.value = err.message || 'Error al cargar cursos'
    console.error('Error cargando cursos:', err)
  } finally {
    loading.value = false
  }
}

const navigateToCourse = (courseId) => {
  router.push(`/estudiante/cursos/${courseId}`)
}

const verProgresoDetallado = (curso) => {
  // Navegar directamente a la vista de progreso con enfoque en este curso
  router.push({
    name: 'estudiante-progreso',
    query: { curso: curso.id }
  })
}

const continuarCurso = (curso) => {
  // Navegar al curso específico o al último módulo visto
  router.push(`/estudiante/cursos/${curso.id}/continuar`)
}

const descargarCertificado = (cursoId) => {
  const curso = cursosStore.cursosMatriculados.find(c => c.id === cursoId)
  if (curso && curso.progreso >= 100) {
    selectedCourse.value = curso
    showCertificateModal.value = true
  }
}

const closeCertificateModal = () => {
  showCertificateModal.value = false
  selectedCourse.value = null
}

const downloadCertificate = async () => {
  try {
    // Aquí iría la lógica para descargar el certificado
    showNotification('success', 'Certificado descargado exitosamente')
    closeCertificateModal()
  } catch (error) {
    showNotification('error', 'Error al descargar el certificado')
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible'
  const date = new Date(dateString)
  return date.toLocaleDateString('es-PA', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
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

const showNotification = (type, message) => {
  notification.value = {
    show: true,
    type,
    message,
    icon: type === 'success' ? CheckCircleIcon : ExclamationTriangleIcon
  }

  setTimeout(() => {
    notification.value.show = false
  }, 4000)
}

// Lifecycle
onMounted(() => {
  loadCursos()
})
</script>

<style scoped>
.cursos-view {
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

/* Filtros */
.filters-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2rem;
  flex-wrap: wrap;
}

.filter-buttons {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-btn:hover {
  background: #f9fafb;
  border-color: #0554f2;
}

.filter-btn-active {
  background: #0554f2;
  color: white;
  border-color: #0554f2;
}

.search-bar {
  flex: 1;
  max-width: 300px;
}

.search-input-group {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 1.25rem;
  height: 1.25rem;
  color: #6b7280;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 3rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #0554f2;
  box-shadow: 0 0 0 3px rgba(5, 84, 242, 0.1);
}

/* Estados */
.loading-state,
.error-state,
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

.error-icon,
.empty-icon {
  width: 4rem;
  height: 4rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.error-state h3,
.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.error-state p,
.empty-state p {
  margin: 0 0 1.5rem 0;
}

/* Grid de cursos */
.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.course-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s;
  display: flex;
  flex-direction: column;
  cursor: pointer;
}

.course-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.course-header {
  position: relative;
  background: linear-gradient(135deg, #0554f2 0%, #063af5 100%);
  padding: 2rem;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.course-image {
  width: 4rem;
  height: 4rem;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.course-icon {
  width: 2rem;
  height: 2rem;
  color: white;
}

.course-status {
  position: absolute;
  top: 1rem;
  right: 1rem;
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
  background: rgba(16, 185, 129, 0.2);
  color: #10b981;
}

.status-progress {
  background: rgba(251, 146, 60, 0.2);
  color: #f59e0b;
}

.course-content {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.course-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.75rem 0;
  line-height: 1.3;
}

.course-description {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.5;
  margin: 0 0 1rem 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.course-instructor {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  color: #374151;
  font-size: 0.875rem;
}

.instructor-icon {
  width: 1rem;
  height: 1rem;
  color: #0554f2;
}

/* Progreso */
.course-progress {
  margin-bottom: 1rem;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
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
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  color: #6b7280;
}

.last-activity {
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: auto;
}

.activity-label {
  margin-right: 0.5rem;
}

.activity-time {
  font-weight: 500;
  color: #374151;
}

.course-actions {
  display: flex;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid #e5e7eb;
}

/* Modal */
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
  z-index: 50;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.certificate-modal .modal-body {
  padding: 2rem 1.5rem;
}

.certificate-icon {
  color: #f59e0b;
  margin-bottom: 1rem;
}

.certificate-modal h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.course-name {
  font-size: 1.125rem;
  font-weight: 500;
  color: #0554f2;
  margin: 0 0 1rem 0;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: color 0.2s;
}

.modal-close:hover {
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
  flex: 1;
}

.modal-footer {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.text-center {
  text-align: center;
}

/* Notification */
.notification {
  position: fixed;
  top: 2rem;
  right: 2rem;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
  z-index: 60;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.notification-success {
  background: #10b981;
  color: white;
}

.notification-error {
  background: #ef4444;
  color: white;
}

.notification-icon {
  width: 1.25rem;
  height: 1.25rem;
}

/* Botones */
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
  gap: 0.5rem;
}

.btn-primary:hover {
  background: #0540f2;
}

.btn-secondary {
  background: white;
  color: #374151;
  padding: 0.75rem 1.5rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-outline {
  background: transparent;
  color: #0554f2;
  padding: 0.75rem 1.5rem;
  border: 1px solid #0554f2;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-outline:hover {
  background: #0554f2;
  color: white;
}

.btn-small {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

/* Utility Classes */
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.w-6 { width: 1.5rem; }
.h-6 { height: 1.5rem; }
.w-8 { width: 2rem; }
.h-8 { height: 2rem; }
.w-16 { width: 4rem; }
.h-16 { height: 4rem; }

/* Responsive */
@media (max-width: 768px) {
  .stats-cards {
    grid-template-columns: 1fr;
  }
  
  .filters-section {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }
  
  .filter-buttons {
    justify-content: center;
  }
  
  .search-bar {
    max-width: none;
  }
  
  .courses-grid {
    grid-template-columns: 1fr;
  }
  
  .course-actions {
    flex-direction: column;
  }
  
  .modal-content {
    margin: 0.5rem;
  }
  
  .notification {
    right: 1rem;
    left: 1rem;
    top: 1rem;
  }
}
</style>
