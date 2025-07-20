<template>
  <div class="explorar-view">
    <div class="page-header">
      <h1 class="page-title">Explorar Cursos</h1>
      <p class="page-subtitle">
        Descubre nuevos cursos y amplía tus conocimientos. ¡Matricúlate y comienza tu aprendizaje!
      </p>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="filters-section">
      <div class="search-bar">
        <div class="search-input-group">
          <MagnifyingGlassIcon class="search-icon" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar cursos..."
            class="search-input"
            @input="debouncedSearch"
          />
        </div>
      </div>

      <div class="filter-buttons">
        <button
          v-for="categoria in categorias"
          :key="categoria.value"
          @click="filtroActivo = categoria.value"
          :class="[
            'filter-btn',
            { 'filter-btn-active': filtroActivo === categoria.value }
          ]"
        >
          {{ categoria.label }}
        </button>
      </div>
    </div>

    <!-- Lista de cursos -->
    <div class="courses-section">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <span>Cargando cursos disponibles...</span>
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
        <h3>No se encontraron cursos</h3>
        <p v-if="searchQuery">
          No hay cursos que coincidan con "<strong>{{ searchQuery }}</strong>"
        </p>
        <p v-else>
          No hay cursos disponibles en este momento.
        </p>
        <button @click="resetFiltros" class="btn-secondary">
          Limpiar filtros
        </button>
      </div>

      <div v-else class="courses-grid">
        <div
          v-for="curso in cursosFiltrados"
          :key="curso.id"
          class="course-card"
        >
          <!-- Header del curso -->
          <div class="course-header">
            <div class="course-image">
              <BookOpenIcon class="course-icon" />
            </div>
            <div class="course-badge" v-if="curso.nivel">
              {{ curso.nivel }}
            </div>
          </div>

          <!-- Información del curso -->
          <div class="course-content">
            <h3 class="course-title">{{ curso.titulo }}</h3>
            <p class="course-description">{{ curso.descripcion || 'Sin descripción disponible' }}</p>
            
            <div class="course-instructor">
              <UserIcon class="instructor-icon" />
              <span>{{ curso.profesor?.nombreCompleto || 'Profesor no asignado' }}</span>
            </div>

            <div class="course-meta">
              <div class="meta-item">
                <ClockIcon class="w-4 h-4" />
                <span>{{ curso.duracion_estimada || 'Por definir' }}</span>
              </div>
              <div class="meta-item">
                <UserGroupIcon class="w-4 h-4" />
                <span>{{ curso.estudiantes_count || 0 }} estudiantes</span>
              </div>
              <div class="meta-item">
                <DocumentTextIcon class="w-4 h-4" />
                <span>{{ curso.modulos_count || 0 }} módulos</span>
              </div>
            </div>

            <!-- Precio (si aplica) -->
            <div v-if="curso.precio" class="course-price">
              <span class="price-label">Precio:</span>
              <span class="price-amount">${{ curso.precio }}</span>
            </div>
          </div>

          <!-- Acciones -->
          <div class="course-actions">
            <button
              @click="toggleCursoDetalle(curso.id)"
              class="btn-secondary btn-small"
            >
              <EyeIcon class="w-4 h-4" />
              Ver detalles
            </button>
            
            <button
              @click="matricularse(curso)"
              :disabled="cursosStore.loading || estaMatriculado(curso.id)"
              :class="[
                'btn-primary btn-small',
                { 'btn-disabled': estaMatriculado(curso.id) }
              ]"
            >
              <component 
                :is="cursosStore.loading ? 'ArrowPathIcon' : 'PlusIcon'"
                :class="[
                  'w-4 h-4',
                  { 'animate-spin': cursosStore.loading }
                ]"
              />
              {{ estaMatriculado(curso.id) ? 'Matriculado' : 'Matricularme' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de detalles del curso -->
    <div v-if="cursoDetalle" class="modal-overlay" @click="cerrarDetalle">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">{{ cursoDetalle.titulo }}</h2>
          <button @click="cerrarDetalle" class="modal-close">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>
        
        <div class="modal-body">
          <div class="detail-section">
            <h3>Descripción</h3>
            <p>{{ cursoDetalle.descripcion || 'Sin descripción disponible' }}</p>
          </div>

          <div class="detail-section" v-if="cursoDetalle.objetivos">
            <h3>Objetivos del curso</h3>
            <ul>
              <li v-for="objetivo in cursoDetalle.objetivos" :key="objetivo">
                {{ objetivo }}
              </li>
            </ul>
          </div>

          <div class="detail-section">
            <h3>Información del instructor</h3>
            <div class="instructor-info">
              <UserIcon class="instructor-avatar" />
              <div>
                <p class="instructor-name">{{ cursoDetalle.profesor?.nombreCompleto }}</p>
                <p class="instructor-title">Instructor</p>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3>Estadísticas</h3>
            <div class="stats-grid">
              <div class="stat-item">
                <span class="stat-number">{{ cursoDetalle.modulos_count || 0 }}</span>
                <span class="stat-label">Módulos</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">{{ cursoDetalle.estudiantes_count || 0 }}</span>
                <span class="stat-label">Estudiantes</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">{{ cursoDetalle.evaluaciones_count || 0 }}</span>
                <span class="stat-label">Evaluaciones</span>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="cerrarDetalle" class="btn-secondary">
            Cerrar
          </button>
          <button
            @click="matricularse(cursoDetalle)"
            :disabled="cursosStore.loading || estaMatriculado(cursoDetalle.id)"
            class="btn-primary"
          >
            {{ estaMatriculado(cursoDetalle.id) ? 'Ya matriculado' : 'Matricularme' }}
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
  MagnifyingGlassIcon,
  BookOpenIcon,
  ExclamationTriangleIcon,
  UserIcon,
  ClockIcon,
  UserGroupIcon,
  DocumentTextIcon,
  EyeIcon,
  PlusIcon,
  ArrowPathIcon,
  XMarkIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const cursosStore = useCursosStore()
const authStore = useAuthStore()

// Estado local
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')
const filtroActivo = ref('todos')
const cursoDetalle = ref(null)
const notification = ref({
  show: false,
  type: 'success',
  message: '',
  icon: CheckCircleIcon
})

// Categorías de filtro
const categorias = [
  { label: 'Todos', value: 'todos' },
  { label: 'Programación', value: 'programacion' },
  { label: 'Finanzas', value: 'finanzas' },
  { label: 'Diseño', value: 'diseno' },
  { label: 'Marketing', value: 'marketing' }
]

// Computed
const cursosFiltrados = computed(() => {
  let cursos = cursosStore.cursosDisponibles || []

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    cursos = cursos.filter(curso =>
      curso.titulo.toLowerCase().includes(query) ||
      curso.descripcion?.toLowerCase().includes(query) ||
      curso.profesor?.nombreCompleto?.toLowerCase().includes(query)
    )
  }

  // Filtrar por categoría
  if (filtroActivo.value !== 'todos') {
    cursos = cursos.filter(curso => 
      curso.categoria?.toLowerCase() === filtroActivo.value
    )
  }

  return cursos
})

// Métodos
const loadCursos = async () => {
  loading.value = true
  error.value = null

  try {
    await cursosStore.fetchCursosDisponibles()
    
    // También cargar cursos matriculados para verificar estado
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

const debouncedSearch = (() => {
  let timeout
  return () => {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
      // La búsqueda se hace automáticamente por el computed
    }, 300)
  }
})()

const resetFiltros = () => {
  searchQuery.value = ''
  filtroActivo.value = 'todos'
}

const estaMatriculado = (cursoId) => {
  return cursosStore.cursosMatriculados.some(curso => curso.id === cursoId)
}

const toggleCursoDetalle = async (cursoId) => {
  try {
    const curso = cursosStore.cursosDisponibles.find(c => c.id === cursoId)
    cursoDetalle.value = curso
  } catch (error) {
    showNotification('error', 'Error al cargar detalles del curso')
  }
}

const cerrarDetalle = () => {
  cursoDetalle.value = null
}

const matricularse = async (curso) => {
  if (estaMatriculado(curso.id)) {
    showNotification('info', 'Ya estás matriculado en este curso')
    return
  }

  try {
    await cursosStore.matricularseEnCurso(curso.id)
    showNotification('success', `¡Te has matriculado exitosamente en "${curso.titulo}"!`)
    cerrarDetalle()
  } catch (error) {
    showNotification('error', error.message || 'Error al matricularse en el curso')
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
.explorar-view {
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

/* Filtros y búsqueda */
.filters-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.search-bar {
  margin-bottom: 1.5rem;
}

.search-input-group {
  position: relative;
  max-width: 400px;
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

.course-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(255, 255, 255, 0.2);
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
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
  flex: 1;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
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

.course-price {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.price-label {
  font-size: 0.875rem;
  color: #6b7280;
}

.price-amount {
  font-size: 1.125rem;
  font-weight: 600;
  color: #0554f2;
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
  max-width: 600px;
  max-height: 80vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
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
  overflow-y: auto;
  flex: 1;
}

.detail-section {
  margin-bottom: 2rem;
}

.detail-section:last-child {
  margin-bottom: 0;
}

.detail-section h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.75rem 0;
}

.detail-section p {
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
}

.detail-section ul {
  margin: 0;
  padding-left: 1.25rem;
  color: #6b7280;
}

.detail-section li {
  margin-bottom: 0.25rem;
}

.instructor-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.instructor-avatar {
  width: 3rem;
  height: 3rem;
  color: #0554f2;
  background: #f0f9ff;
  border-radius: 8px;
  padding: 0.5rem;
}

.instructor-name {
  font-weight: 500;
  color: #111827;
  margin: 0;
}

.instructor-title {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.stat-item {
  text-align: center;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
}

.stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: bold;
  color: #0554f2;
}

.stat-label {
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.modal-footer {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
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

.notification-info {
  background: #3b82f6;
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

.btn-secondary:hover {
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

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Utility Classes */
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.w-6 { width: 1.5rem; }
.h-6 { height: 1.5rem; }
.w-16 { width: 4rem; }
.h-16 { height: 4rem; }

/* Responsive */
@media (max-width: 768px) {
  .courses-grid {
    grid-template-columns: 1fr;
  }
  
  .course-actions {
    flex-direction: column;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-content {
    margin: 0.5rem;
    max-height: 90vh;
  }
  
  .notification {
    right: 1rem;
    left: 1rem;
    top: 1rem;
  }
}
</style>
