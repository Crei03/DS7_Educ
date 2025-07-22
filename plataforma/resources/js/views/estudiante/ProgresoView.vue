<template>
  <div class="progreso-view">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1 class="page-title">Mi Progreso</h1>
        <p class="page-subtitle">Seguimiento detallado de tu avance en todos los cursos</p>
      </div>
      <div class="stats-summary">
        <div class="stat-card">
          <div class="stat-number">{{ estadisticas.totalCursos }}</div>
          <div class="stat-label">Cursos</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">{{ estadisticas.cursosCompletados }}</div>
          <div class="stat-label">Completados</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">{{ estadisticas.promedioProgreso }}%</div>
          <div class="stat-label">Promedio</div>
        </div>
      </div>
    </div>

    <!-- Filtros -->
    <div class="filtros-container">
      <div class="filtros">
        <button 
          @click="filtroActivo = 'todos'"
          class="filtro-btn"
          :class="{ active: filtroActivo === 'todos' }"
        >
          Todos los cursos
        </button>
        <button 
          @click="filtroActivo = 'progreso'"
          class="filtro-btn"
          :class="{ active: filtroActivo === 'progreso' }"
        >
          En progreso
        </button>
        <button 
          @click="filtroActivo = 'completados'"
          class="filtro-btn"
          :class="{ active: filtroActivo === 'completados' }"
        >
          Completados
        </button>
      </div>
      <div class="vista-opciones">
        <button 
          @click="vistaActual = 'cards'"
          class="vista-btn"
          :class="{ active: vistaActual === 'cards' }"
          title="Vista de tarjetas"
        >
          <Squares2X2Icon class="vista-icon" />
        </button>
        <button 
          @click="vistaActual = 'lista'"
          class="vista-btn"
          :class="{ active: vistaActual === 'lista' }"
          title="Vista de lista"
        >
          <ListBulletIcon class="vista-icon" />
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="cursosStore.loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Cargando progreso...</p>
    </div>

    <!-- Sin cursos -->
    <div v-else-if="cursosFiltrados.length === 0" class="empty-state">
      <BookOpenIcon class="empty-icon" />
      <h3 class="empty-title">
        {{ filtroActivo === 'todos' ? 'No tienes cursos matriculados' : 'No hay cursos en esta categoría' }}
      </h3>
      <p class="empty-description">
        {{ filtroActivo === 'todos' ? 'Explora nuestro catálogo y matricúlate en tu primer curso.' : 'Cambia el filtro para ver otros cursos.' }}
      </p>
      <router-link v-if="filtroActivo === 'todos'" to="/estudiante/explorar" class="cta-button">
        Explorar Cursos
      </router-link>
    </div>

    <!-- Vista de tarjetas -->
    <div v-else-if="vistaActual === 'cards'" class="cursos-grid">
      <ProgresoCard
        v-for="curso in cursosFiltrados"
        :key="curso.id"
        :curso="curso"
        @ver-detalle="verDetalleCurso"
        @continuar-curso="continuarCurso"
      />
    </div>

    <!-- Vista de lista detallada -->
    <div v-else class="cursos-lista">
      <div v-for="curso in cursosFiltrados" :key="curso.id" class="curso-detallado">
        <div class="curso-header">
          <div class="curso-info">
            <h3 class="curso-titulo">{{ curso.titulo }}</h3>
            <p class="curso-descripcion">{{ curso.descripcion }}</p>
          </div>
          <div class="progreso-info">
            <div class="progreso-circular" :class="getProgressClass(curso.progreso)">
              <span class="progreso-porcentaje">{{ curso.progreso }}%</span>
            </div>
          </div>
        </div>

        <!-- Módulos detallados -->
        <div v-if="curso.modulos && curso.modulos.length > 0" class="modulos-container">
          <ModuloDetalle
            v-for="modulo in curso.modulos"
            :key="modulo.id"
            :modulo="modulo"
            @material-visto="marcarMaterialVisto"
          />
        </div>

        <div class="curso-actions">
          <button @click="verDetalleCurso(curso)" class="btn-secondary">
            <EyeIcon class="btn-icon" />
            Ver Detalle
          </button>
          <button 
            v-if="curso.progreso < 100" 
            @click="continuarCurso(curso)" 
            class="btn-primary"
          >
            <PlayIcon class="btn-icon" />
            Continuar Curso
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCursosStore } from '@/stores/cursos'
import { useAuthStore } from '@/stores/auth'
import ProgresoCard from '@/components/estudiante/ProgresoCard.vue'
import ModuloDetalle from '@/components/estudiante/ModuloDetalle.vue'
import { 
  BookOpenIcon,
  Squares2X2Icon,
  ListBulletIcon,
  EyeIcon,
  PlayIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const cursosStore = useCursosStore()
const authStore = useAuthStore()

const filtroActivo = ref('todos')
const vistaActual = ref('cards')

const estadisticas = computed(() => cursosStore.estadisticasEstudiante)

const cursosFiltrados = computed(() => {
  const cursos = cursosStore.cursosMatriculados
  
  switch (filtroActivo.value) {
    case 'progreso':
      return cursos.filter(curso => curso.progreso > 0 && curso.progreso < 100)
    case 'completados':
      return cursos.filter(curso => curso.progreso >= 100)
    default:
      return cursos
  }
})

const getProgressClass = (progreso) => {
  if (progreso >= 100) return 'completado'
  if (progreso >= 70) return 'avanzado'
  if (progreso >= 30) return 'medio'
  return 'inicio'
}

const verDetalleCurso = async (curso) => {
  try {
    // Cargar progreso detallado si no lo tiene
    if (!curso.modulos || curso.modulos.length === 0) {
      await cursosStore.fetchProgresoDetallado(curso.id)
    }
    
    // Navegar a vista de detalle del curso
    router.push(`/estudiante/cursos/${curso.id}`)
  } catch (error) {
    console.error('Error al cargar detalle del curso:', error)
  }
}

const continuarCurso = (curso) => {
  // Encontrar el primer material no visto
  if (curso.modulos && curso.modulos.length > 0) {
    for (const modulo of curso.modulos) {
      if (modulo.materiales && modulo.materiales.length > 0) {
        const materialPendiente = modulo.materiales.find(m => !m.visto)
        if (materialPendiente) {
          // Abrir el material
          window.open(materialPendiente.url, '_blank')
          return
        }
      }
    }
  }
  
  // Si no hay material específico, ir al detalle del curso
  verDetalleCurso(curso)
}

const marcarMaterialVisto = async (materialId) => {
  try {
    await cursosStore.marcarMaterialVisto(materialId)
  } catch (error) {
    console.error('Error al marcar material como visto:', error)
  }
}

onMounted(async () => {
  try {
    const userId = authStore.user?.id
    if (userId) {
      await cursosStore.fetchCursosMatriculados(userId)
    }
  } catch (error) {
    console.error('Error al cargar cursos:', error)
  }
})
</script>

<style scoped>
.progreso-view {
  max-width: 800px;
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

.placeholder-content {
  background: white;
  border-radius: 12px;
  padding: 3rem;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.placeholder-icon {
  color: #0554f2;
  margin-bottom: 1.5rem;
}

.placeholder-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 1rem 0;
}

.placeholder-text {
  color: #6b7280;
  margin: 0 0 2rem 0;
}

.btn-primary {
  background: #0554f2;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary:hover {
  background: #0540f2;
}

.w-16 { width: 4rem; }
.h-16 { height: 4rem; }
</style>
