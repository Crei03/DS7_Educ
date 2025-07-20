
<template>
  <div class="max-w-3xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm mb-8 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
      <div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-1">Mi Progreso</h1>
        <p class="text-gray-500 text-base">Seguimiento detallado de tu avance en todos los cursos</p>
      </div>
      <div class="flex gap-6">
        <div class="flex flex-col items-center">
          <div class="text-2xl font-bold text-primary">{{ estadisticas.totalCursos }}</div>
          <div class="text-xs text-gray-500">Cursos</div>
        </div>
        <div class="flex flex-col items-center">
          <div class="text-2xl font-bold text-primary">{{ estadisticas.cursosCompletados }}</div>
          <div class="text-xs text-gray-500">Completados</div>
        </div>
        <div class="flex flex-col items-center">
          <div class="text-2xl font-bold text-primary">{{ estadisticas.promedioProgreso }}%</div>
          <div class="text-xs text-gray-500">Promedio</div>
        </div>
      </div>
    </div>

    <!-- Filtros -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <div class="flex gap-2">
        <button 
          @click="filtroActivo = 'todos'"
          class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-200 bg-white hover:bg-blue-50 transition-colors"
          :class="{ 'bg-primary text-white': filtroActivo === 'todos' }"
        >
          Todos los cursos
        </button>
        <button 
          @click="filtroActivo = 'progreso'"
          class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-200 bg-white hover:bg-blue-50 transition-colors"
          :class="{ 'bg-primary text-white': filtroActivo === 'progreso' }"
        >
          En progreso
        </button>
        <button 
          @click="filtroActivo = 'completados'"
          class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-200 bg-white hover:bg-blue-50 transition-colors"
          :class="{ 'bg-primary text-white': filtroActivo === 'completados' }"
        >
          Completados
        </button>
      </div>
      <div class="flex gap-2">
        <button 
          @click="vistaActual = 'cards'"
          class="p-2 rounded-lg border border-gray-200 bg-white hover:bg-blue-50 transition-colors"
          :class="{ 'bg-primary text-white': vistaActual === 'cards' }"
          title="Vista de tarjetas"
        >
          <Squares2X2Icon class="w-5 h-5" />
        </button>
        <button 
          @click="vistaActual = 'lista'"
          class="p-2 rounded-lg border border-gray-200 bg-white hover:bg-blue-50 transition-colors"
          :class="{ 'bg-primary text-white': vistaActual === 'lista' }"
          title="Vista de lista"
        >
          <ListBulletIcon class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="cursosStore.loading" class="flex flex-col items-center justify-center py-12">
      <div class="animate-spin w-8 h-8 text-primary mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-full h-full"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
      </div>
      <p class="text-gray-500">Cargando progreso...</p>
    </div>

    <!-- Sin cursos -->
    <div v-else-if="cursosFiltrados.length === 0" class="flex flex-col items-center justify-center py-16 bg-white rounded-xl shadow">
      <BookOpenIcon class="w-16 h-16 text-primary mb-4" />
      <h3 class="text-xl font-semibold text-gray-900 mb-2">
        {{ filtroActivo === 'todos' ? 'No tienes cursos matriculados' : 'No hay cursos en esta categoría' }}
      </h3>
      <p class="text-gray-500 mb-4">
        {{ filtroActivo === 'todos' ? 'Explora nuestro catálogo y matricúlate en tu primer curso.' : 'Cambia el filtro para ver otros cursos.' }}
      </p>
      <router-link v-if="filtroActivo === 'todos'" to="/estudiante/explorar" class="inline-block bg-primary text-white px-5 py-2 rounded-lg font-medium hover:bg-primary-dk transition-colors">
        Explorar Cursos
      </router-link>
    </div>

    <!-- Vista de tarjetas -->
    <div v-else-if="vistaActual === 'cards'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <ProgresoCard
        v-for="curso in cursosFiltrados"
        :key="curso.id"
        :curso="curso"
        @ver-detalle="verDetalleCurso"
        @continuar-curso="continuarCurso"
      />
    </div>

    <!-- Vista de lista detallada -->
    <div v-else class="flex flex-col gap-6">
      <div v-for="curso in cursosFiltrados" :key="curso.id" class="bg-white rounded-xl shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
          <div>
            <h3 class="text-lg font-bold text-gray-900">{{ curso.titulo }}</h3>
            <p class="text-gray-500 text-sm">{{ curso.descripcion }}</p>
          </div>
          <div>
            <div :class="[
              'inline-flex items-center justify-center rounded-full w-14 h-14 font-bold text-lg',
              getProgressClass(curso.progreso)
            ]">
              <span>{{ curso.progreso }}%</span>
            </div>
          </div>
        </div>

        <!-- Módulos detallados -->
        <div v-if="curso.modulos && curso.modulos.length > 0" class="flex flex-col gap-2 mb-4">
          <ModuloDetalle
            v-for="modulo in curso.modulos"
            :key="modulo.id"
            :modulo="modulo"
            @material-visto="marcarMaterialVisto"
          />
        </div>

        <div class="flex gap-2">
          <button @click="verDetalleCurso(curso)" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 bg-white text-primary font-medium hover:bg-blue-50 transition-colors">
            <EyeIcon class="w-5 h-5" />
            Ver Detalle
          </button>
          <button 
            v-if="curso.progreso < 100" 
            @click="continuarCurso(curso)" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-primary-dk transition-colors"
          >
            <PlayIcon class="w-5 h-5" />
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
/* Colores de progreso circular */
.completado {
  background: #e0f2fe;
  color: #0554f2;
  border: 2px solid #0554f2;
}
.avanzado {
  background: #f3f4f6;
  color: #1d4ed8;
  border: 2px solid #1d4ed8;
}
.medio {
  background: #fef9c3;
  color: #ca8a04;
  border: 2px solid #ca8a04;
}
.inicio {
  background: #fee2e2;
  color: #dc2626;
  border: 2px solid #dc2626;
}
.text-primary {
  color: #0554f2;
}
.bg-primary {
  background: #0554f2;
}
.bg-primary-dk {
  background: #0540f2;
}
</style>
