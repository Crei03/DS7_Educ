<template>
  <div class="curso-detalle-view">
    <!-- Header del curso -->
    <div class="course-header">
      <div class="header-content">
        <button @click="$router.go(-1)" class="back-button">
          <ArrowLeftIcon class="w-5 h-5" />
          Volver
        </button>
        
        <div class="course-info" v-if="curso">
          <div class="course-image">
            <BookOpenIcon class="course-icon" />
          </div>
          
          <div class="course-details">
            <h1 class="course-title">{{ curso.titulo }}</h1>
            <p class="course-description">{{ curso.descripcion }}</p>
            
            <div class="course-meta">
              <div class="meta-item">
                <UserIcon class="meta-icon" />
                {{ curso.profesor?.nombreCompleto }}
              </div>
              <div class="meta-item">
                <ClockIcon class="meta-icon" />
                {{ curso.duracion_horas }}h
              </div>
              <div class="meta-item">
                <CalendarIcon class="meta-icon" />
                Matriculado: {{ formatDate(curso.fecha_matricula) }}
              </div>
            </div>
            
            <!-- Progreso general -->
            <div class="progress-section">
              <div class="progress-header">
                <span class="progress-label">Progreso del curso</span>
                <span class="progress-percentage">{{ Math.round(curso.progreso || 0) }}%</span>
              </div>
              <div class="progress-bar">
                <div 
                  class="progress-fill" 
                  :style="{ width: `${curso.progreso || 0}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">
      <!-- Estados de carga -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando detalles del curso...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <ExclamationTriangleIcon class="error-icon" />
        <h3>Error al cargar el curso</h3>
        <p>{{ error }}</p>
        <button @click="loadCursoDetalle" class="btn-secondary">
          <ArrowPathIcon class="w-4 h-4" />
          Reintentar
        </button>
      </div>

      <!-- Contenido del curso -->
      <div v-else-if="curso" class="course-content">
        <!-- Tabs de navegación -->
        <div class="tabs-navigation">
          <button 
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="['tab-button', { 'tab-active': activeTab === tab.id }]"
          >
            <component :is="tab.icon" class="tab-icon" />
            {{ tab.label }}
            <span v-if="tab.count !== undefined" class="tab-count">{{ tab.count }}</span>
          </button>
        </div>

        <!-- Contenido de las tabs -->
        <div class="tab-content">
          <!-- Tab de Módulos -->
          <div v-if="activeTab === 'modulos'" class="modulos-section">
            <div v-if="!curso.modulos || curso.modulos.length === 0" class="empty-state">
              <DocumentTextIcon class="empty-icon" />
              <h3>No hay módulos disponibles</h3>
              <p>Este curso aún no tiene módulos configurados.</p>
            </div>
            
            <div v-else class="modulos-grid">
              <div
                v-for="(modulo, index) in curso.modulos"
                :key="modulo.id"
                class="modulo-card"
              >
                <div class="modulo-header">
                  <div class="modulo-number">{{ index + 1 }}</div>
                  <div class="modulo-info">
                    <h3 class="modulo-title">{{ modulo.titulo }}</h3>
                    <p class="modulo-description">{{ modulo.descripcion }}</p>
                  </div>
                  <div class="modulo-progress">
                    <div class="progress-circle">
                      <span class="progress-text">{{ Math.round(modulo.progreso || 0) }}%</span>
                    </div>
                  </div>
                </div>
                
                <div class="modulo-content" v-if="modulo.materiales && modulo.materiales.length > 0">
                  <h4 class="materiales-title">Materiales ({{ modulo.materiales.length }})</h4>
                  <div class="materiales-list">
                    <div
                      v-for="material in modulo.materiales"
                      :key="material.id"
                      :class="['material-item', { 'material-completed': material.visto }]"
                      @click="verMaterial(material)"
                    >
                      <div class="material-icon">
                        <CheckCircleIcon v-if="material.visto" class="icon-completed" />
                        <PlayIcon v-else-if="material.tipo === 'video'" class="icon-video" />
                        <DocumentTextIcon v-else class="icon-document" />
                      </div>
                      <div class="material-info">
                        <h5 class="material-title">{{ material.titulo }}</h5>
                        <p class="material-type">{{ formatTipoMaterial(material.tipo) }}</p>
                      </div>
                      <div class="material-status">
                        <span v-if="material.visto" class="status-completed">Completado</span>
                        <span v-else class="status-pending">Pendiente</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div v-else class="empty-materials">
                  <p>No hay materiales en este módulo</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab de Evaluaciones -->
          <div v-if="activeTab === 'evaluaciones'" class="evaluaciones-section">
            <div v-if="loadingEvaluaciones" class="loading-evaluaciones">
              <div class="spinner-small"></div>
              <p>Cargando evaluaciones...</p>
            </div>
            
            <div v-else-if="!evaluaciones || evaluaciones.length === 0" class="empty-state">
              <ClipboardDocumentListIcon class="empty-icon" />
              <h3>No hay evaluaciones disponibles</h3>
              <p>Este curso aún no tiene evaluaciones configuradas.</p>
            </div>
            
            <div v-else class="evaluaciones-grid">
              <div
                v-for="evaluacion in evaluaciones"
                :key="evaluacion.id"
                class="evaluacion-card"
              >
                <div class="evaluacion-header">
                  <div class="evaluacion-icon">
                    <ClipboardDocumentListIcon class="icon" />
                  </div>
                  <div class="evaluacion-info">
                    <h3 class="evaluacion-title">{{ evaluacion.titulo }}</h3>
                    <p class="evaluacion-description">{{ evaluacion.descripcion }}</p>
                  </div>
                  <div class="evaluacion-status">
                    <span 
                      v-if="evaluacion.completada" 
                      class="status-badge status-completed"
                    >
                      Completada
                    </span>
                    <span 
                      v-else-if="evaluacion.disponible" 
                      class="status-badge status-available"
                    >
                      Disponible
                    </span>
                    <span 
                      v-else 
                      class="status-badge status-locked"
                    >
                      Bloqueada
                    </span>
                  </div>
                </div>
                
                <div class="evaluacion-meta">
                  <div class="meta-item">
                    <ClockIcon class="meta-icon" />
                    {{ evaluacion.duracion_minutos }} min
                  </div>
                  <div class="meta-item">
                    <DocumentTextIcon class="meta-icon" />
                    {{ evaluacion.preguntas_count }} preguntas
                  </div>
                  <div v-if="evaluacion.calificacion" class="meta-item">
                    <TrophyIcon class="meta-icon" />
                    Calificación: {{ evaluacion.calificacion }}%
                  </div>
                </div>
                
                <div class="evaluacion-actions">
                  <button
                    v-if="evaluacion.completada"
                    @click="verResultados(evaluacion)"
                    class="btn-secondary btn-small"
                  >
                    <EyeIcon class="w-4 h-4" />
                    Ver resultados
                  </button>
                  <button
                    v-else-if="evaluacion.disponible"
                    @click="iniciarEvaluacion(evaluacion)"
                    class="btn-primary btn-small"
                  >
                    <PlayIcon class="w-4 h-4" />
                    Iniciar evaluación
                  </button>
                  <button
                    v-else
                    disabled
                    class="btn-secondary btn-small btn-disabled"
                  >
                    <LockClosedIcon class="w-4 h-4" />
                    No disponible
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Notification -->
    <div v-if="notification.show" :class="['notification', `notification-${notification.type}`]">
      <component :is="notification.icon" class="notification-icon" />
      {{ notification.message }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useCursosStore } from '@/stores/cursos'
import api from '@/services/api'
import {
  ArrowLeftIcon,
  BookOpenIcon,
  UserIcon,
  ClockIcon,
  CalendarDaysIcon,
  ExclamationTriangleIcon,
  ArrowPathIcon,
  DocumentTextIcon,
  ClipboardDocumentListIcon,
  CheckCircleIcon,
  PlayIcon,
  TrophyIcon,
  EyeIcon,
  LockClosedIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const route = useRoute()
const cursosStore = useCursosStore()

// Estado local
const loading = ref(true)
const loadingEvaluaciones = ref(false)
const error = ref(null)
const curso = ref(null)
const evaluaciones = ref([])
const activeTab = ref('modulos')
const notification = ref({
  show: false,
  type: 'success',
  message: '',
  icon: CheckCircleIcon
})

// Computed
const tabs = computed(() => [
  {
    id: 'modulos',
    label: 'Módulos',
    icon: DocumentTextIcon,
    count: curso.value?.modulos?.length || 0
  },
  {
    id: 'evaluaciones',
    label: 'Evaluaciones',
    icon: ClipboardDocumentListIcon,
    count: evaluaciones.value?.length || 0
  }
])

// Métodos
const loadCursoDetalle = async () => {
  const cursoId = route.params.id
  if (!cursoId) {
    error.value = 'ID de curso no válido'
    return
  }

  loading.value = true
  error.value = null

  try {
    // Primero intentar obtener de cursos matriculados
    let cursoLocal = cursosStore.getCursoMatriculadoById(parseInt(cursoId))
    
    if (cursoLocal) {
      curso.value = cursoLocal
      // Si no tiene módulos cargados, cargar progreso detallado
      if (!cursoLocal.modulos) {
        await cursosStore.fetchProgresoDetallado(cursoId)
        curso.value = cursosStore.getCursoMatriculadoById(parseInt(cursoId))
      }
    } else {
      // Si no está en matriculados, cargar desde API
      await cursosStore.fetchProgresoDetallado(cursoId)
      curso.value = cursosStore.getCursoMatriculadoById(parseInt(cursoId))
    }

    // Cargar evaluaciones
    await loadEvaluaciones(cursoId)

  } catch (err) {
    error.value = err.message || 'Error al cargar detalles del curso'
    console.error('Error cargando curso detalle:', err)
  } finally {
    loading.value = false
  }
}

const loadEvaluaciones = async (cursoId) => {
  loadingEvaluaciones.value = true
  
  try {
    const response = await api.get(`/estudiante/cursos/${cursoId}/evaluaciones`)
    evaluaciones.value = response.data.evaluaciones || response.data || []
  } catch (error) {
    console.error('Error cargando evaluaciones:', error)
    evaluaciones.value = []
  } finally {
    loadingEvaluaciones.value = false
  }
}

const verMaterial = async (material) => {
  try {
    if (!material.visto) {
      await cursosStore.marcarMaterialVisto(material.id)
      showNotification('success', `Material "${material.titulo}" marcado como visto`)
    }
  } catch (error) {
    showNotification('error', 'Error al marcar material como visto')
  }
}

const iniciarEvaluacion = (evaluacion) => {
  router.push(`/estudiante/evaluaciones/${evaluacion.id}`)
}

const verResultados = (evaluacion) => {
  router.push(`/estudiante/evaluaciones/${evaluacion.id}/resultados`)
}

const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible'
  const date = new Date(dateString)
  return date.toLocaleDateString('es-PA', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatTipoMaterial = (tipo) => {
  const tipos = {
    'video': 'Video',
    'documento': 'Documento',
    'pdf': 'PDF',
    'imagen': 'Imagen',
    'enlace': 'Enlace'
  }
  return tipos[tipo] || 'Material'
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
  loadCursoDetalle()
})
</script>

<style scoped>
.curso-detalle-view {
  min-height: 100vh;
  background: #f9fafb;
}

/* Header */
.course-header {
  background: linear-gradient(135deg, #0554f2 0%, #063af5 100%);
  color: white;
  padding: 2rem 0;
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-bottom: 2rem;
}

.back-button:hover {
  background: rgba(255, 255, 255, 0.3);
}

.course-info {
  display: flex;
  gap: 2rem;
  align-items: flex-start;
}

.course-image {
  width: 6rem;
  height: 6rem;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.course-icon {
  width: 3rem;
  height: 3rem;
  color: white;
}

.course-details {
  flex: 1;
}

.course-title {
  font-size: 2.5rem;
  font-weight: bold;
  margin: 0 0 1rem 0;
  line-height: 1.2;
}

.course-description {
  font-size: 1.125rem;
  opacity: 0.9;
  margin: 0 0 1.5rem 0;
  line-height: 1.5;
}

.course-meta {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1rem;
}

.meta-icon {
  width: 1.25rem;
  height: 1.25rem;
  opacity: 0.8;
}

.progress-section {
  max-width: 400px;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.progress-label {
  font-size: 1rem;
  font-weight: 500;
}

.progress-percentage {
  font-size: 1.25rem;
  font-weight: bold;
}

.progress-bar {
  height: 12px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 6px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 6px;
  transition: width 0.3s ease;
}

/* Contenido principal */
.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

/* Estados */
.loading-state,
.error-state {
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

.spinner-small {
  width: 24px;
  height: 24px;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #0554f2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 0.5rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-icon {
  width: 4rem;
  height: 4rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.error-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.error-state p {
  margin: 0 0 1.5rem 0;
}

/* Tabs */
.tabs-navigation {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 2rem;
  border-bottom: 1px solid #e5e7eb;
  background: white;
  border-radius: 12px 12px 0 0;
  padding: 0.5rem;
}

.tab-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  background: transparent;
  color: #6b7280;
  font-weight: 500;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
}

.tab-button:hover {
  color: #374151;
  background: #f9fafb;
}

.tab-active {
  color: #0554f2;
  background: #f0f9ff;
}

.tab-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.tab-count {
  background: #e5e7eb;
  color: #6b7280;
  padding: 0.125rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.tab-active .tab-count {
  background: #0554f2;
  color: white;
}

/* Contenido de tabs */
.tab-content {
  background: white;
  border-radius: 0 0 12px 12px;
  min-height: 400px;
}

/* Módulos */
.modulos-section {
  padding: 2rem;
}

.modulos-grid {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.modulo-card {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  transition: box-shadow 0.2s;
}

.modulo-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.modulo-header {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
  padding: 2rem;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.modulo-number {
  width: 3rem;
  height: 3rem;
  background: #0554f2;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  font-weight: bold;
  flex-shrink: 0;
}

.modulo-info {
  flex: 1;
}

.modulo-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.modulo-description {
  color: #6b7280;
  line-height: 1.5;
  margin: 0;
}

.modulo-progress {
  flex-shrink: 0;
}

.progress-circle {
  width: 4rem;
  height: 4rem;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #0554f2;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.progress-text {
  font-size: 0.875rem;
  font-weight: 600;
  color: #0554f2;
}

.modulo-content {
  padding: 2rem;
}

.materiales-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 1rem 0;
}

.materiales-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.material-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.material-item:hover {
  border-color: #0554f2;
  box-shadow: 0 2px 8px rgba(5, 84, 242, 0.1);
}

.material-completed {
  background: #f0fdf4;
  border-color: #10b981;
}

.material-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-completed {
  width: 1.5rem;
  height: 1.5rem;
  color: #10b981;
}

.icon-video {
  width: 1.5rem;
  height: 1.5rem;
  color: #0554f2;
}

.icon-document {
  width: 1.5rem;
  height: 1.5rem;
  color: #6b7280;
}

.material-info {
  flex: 1;
}

.material-title {
  font-size: 1rem;
  font-weight: 500;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.material-type {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.material-status {
  flex-shrink: 0;
}

.status-completed {
  background: #10b981;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-pending {
  background: #f59e0b;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.empty-materials {
  padding: 2rem;
  text-align: center;
  color: #6b7280;
  font-style: italic;
}

/* Evaluaciones */
.evaluaciones-section {
  padding: 2rem;
}

.loading-evaluaciones {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 2rem;
  color: #6b7280;
}

.evaluaciones-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.evaluacion-card {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 2rem;
  transition: all 0.2s;
}

.evaluacion-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-color: #0554f2;
}

.evaluacion-header {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.evaluacion-icon {
  width: 3rem;
  height: 3rem;
  background: #f0f9ff;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.evaluacion-icon .icon {
  width: 1.5rem;
  height: 1.5rem;
  color: #0554f2;
}

.evaluacion-info {
  flex: 1;
}

.evaluacion-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.evaluacion-description {
  color: #6b7280;
  line-height: 1.5;
  margin: 0;
}

.evaluacion-status {
  flex-shrink: 0;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-completed {
  background: #10b981;
  color: white;
}

.status-available {
  background: #0554f2;
  color: white;
}

.status-locked {
  background: #6b7280;
  color: white;
}

.evaluacion-meta {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.evaluacion-meta .meta-item {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.evaluacion-meta .meta-icon {
  width: 1rem;
  height: 1rem;
}

.evaluacion-actions {
  display: flex;
  gap: 0.75rem;
}

/* Empty states */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
  color: #6b7280;
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

.btn-primary:hover:not(.btn-disabled) {
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

.btn-secondary:hover:not(.btn-disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-small {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.btn-disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Utility Classes */
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }

/* Responsive */
@media (max-width: 768px) {
  .header-content {
    padding: 0 1rem;
  }
  
  .course-info {
    flex-direction: column;
    gap: 1rem;
  }
  
  .course-title {
    font-size: 2rem;
  }
  
  .course-meta {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .main-content {
    padding: 1rem;
  }
  
  .tabs-navigation {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .modulo-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .evaluaciones-grid {
    grid-template-columns: 1fr;
  }
  
  .evaluacion-meta {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .notification {
    right: 1rem;
    left: 1rem;
    top: 1rem;
  }
}
</style>
