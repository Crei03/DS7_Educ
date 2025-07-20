<template>
  <div class="estudiantes-container">
    <div class="estudiantes-header">
      <div>
        <h1 class="estudiantes-title">Estudiantes</h1>
        <p class="estudiantes-subtitle">Visualiza el progreso de tus estudiantes</p>
      </div>
      <div class="estudiantes-stats">
        <div class="stat-item">
          <span class="stat-number">{{ totalEstudiantes }}</span>
          <span class="stat-label">Total estudiantes</span>
        </div>
      </div>
    </div>
    
    <div v-if="loading" class="estudiantes-loading">
      <p>Cargando estudiantes...</p>
    </div>
    
    <div v-else-if="estudiantesPorCurso.length === 0" class="estudiantes-empty">
      <UsersIcon class="estudiantes-icon-empty" />
      <h3 class="estudiantes-empty-title">No hay estudiantes matriculados</h3>
      <p class="estudiantes-empty-text">Los estudiantes aparecerán aquí una vez se matriculen en tus cursos.</p>
    </div>
    
    <div v-else class="cursos-section">
      <div 
        v-for="curso in estudiantesPorCurso" 
        :key="curso.curso_id"
        class="curso-estudiantes-card"
      >
        <div class="curso-header">
          <h3 class="curso-nombre">{{ curso.curso_titulo }}</h3>
          <span class="curso-total-estudiantes">{{ curso.estudiantes?.length || 0 }} estudiantes</span>
        </div>
        
        <div v-if="curso.estudiantes && curso.estudiantes.length > 0" class="estudiantes-grid">
          <div 
            v-for="estudiante in curso.estudiantes" 
            :key="estudiante.id"
            class="estudiante-card"
          >
            <div class="estudiante-avatar">
              <span class="estudiante-iniciales">
                {{ getIniciales(estudiante.nombre) }}
              </span>
            </div>
            
            <div class="estudiante-info">
              <h4 class="estudiante-nombre">
                {{ estudiante.nombre }}
              </h4>
              <p class="estudiante-matricula">Matriculado: {{ estudiante.fecha_matricula }}</p>
            </div>
            
            <div class="estudiante-stats">
              <div class="estudiante-stat">
                <span class="stat-value">{{ estudiante.progreso || 0 }}%</span>
                <span class="stat-name">Progreso</span>
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="curso-sin-estudiantes">
          <p>No hay estudiantes matriculados en este curso</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { UsersIcon } from '@heroicons/vue/24/outline'
import { useEstudiantesStore } from '@/stores/estudiantes'

const estudiantesStore = useEstudiantesStore()
const loading = ref(true)
const estudiantesPorCurso = ref([])

const totalEstudiantes = computed(() => {
  return estudiantesPorCurso.value.reduce((total, curso) => {
    return total + (curso.estudiantes?.length || 0)
  }, 0)
})

const getIniciales = (nombre) => {
  if (!nombre) return ''
  const partes = nombre.split(' ')
  return (partes[0]?.charAt(0) || '') + (partes[1]?.charAt(0) || '')
}

const cargarEstudiantes = async () => {
  try {
    loading.value = true
    // Usar el store para obtener el progreso de estudiantes del profesor (id fijo 2, puedes parametrizar)
    await estudiantesStore.fetchProgresoEstudiantes(2)
    estudiantesPorCurso.value = estudiantesStore.progresoEstudiantes
  } catch (error) {
    console.error('Error cargando progreso de estudiantes:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  cargarEstudiantes()
})
</script>

<style scoped>
.estudiantes-container {
  padding: 2rem;
}

.estudiantes-header {
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.estudiantes-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.estudiantes-subtitle {
  color: #6b7280;
  font-size: 1.1rem;
}

.estudiantes-stats {
  display: flex;
  gap: 2rem;
}

.stat-item {
  text-align: center;
  background: #fff;
  padding: 1rem 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: bold;
  color: #0554f2;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.estudiantes-loading {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1.1rem;
}

.estudiantes-empty {
  background: #fff;
  border-radius: 1rem;
  padding: 3rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  text-align: center;
}

.estudiantes-icon-empty {
  width: 64px;
  height: 64px;
  color: #e5e7eb;
  margin: 0 auto 1rem;
}

.estudiantes-empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.estudiantes-empty-text {
  color: #6b7280;
}

.cursos-section {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.curso-estudiantes-card {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.curso-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.curso-nombre {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.curso-total-estudiantes {
  background: #e0f2fe;
  color: #0c4a6e;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.875rem;
  font-weight: 500;
}

.estudiantes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
}

.estudiante-card {
  background: #f9fafb;
  border-radius: 0.75rem;
  padding: 1rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: background 0.2s;
}

.estudiante-card:hover {
  background: #f3f4f6;
}

.estudiante-avatar {
  width: 48px;
  height: 48px;
  background: #0554f2;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.estudiante-iniciales {
  color: #fff;
  font-weight: 600;
  font-size: 1rem;
}

.estudiante-info {
  flex: 1;
  min-width: 0;
}

.estudiante-nombre {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
}

.estudiante-email {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0 0 0.25rem 0;
}

.estudiante-matricula {
  font-size: 0.75rem;
  color: #9ca3af;
  margin: 0;
}

.estudiante-stats {
  display: flex;
  gap: 1rem;
}

.estudiante-stat {
  text-align: center;
}

.stat-value {
  display: block;
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
}

.stat-name {
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.curso-sin-estudiantes {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
  font-style: italic;
}
</style>
