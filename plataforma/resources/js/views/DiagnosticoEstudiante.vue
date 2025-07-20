<template>
  <div class="diagnostico-estudiante">
    <h1>Diagnóstico de Datos - Estudiante</h1>
    
    <div class="section">
      <h2>Usuario Autenticado:</h2>
      <pre>{{ JSON.stringify(userStore.user, null, 2) }}</pre>
    </div>
    
    <div class="section">
      <h2>Estado del Store de Cursos:</h2>
      <p><strong>Loading:</strong> {{ cursosStore.loading }}</p>
      <p><strong>Error:</strong> {{ cursosStore.error }}</p>
      <p><strong>Cursos Matriculados Length:</strong> {{ cursosStore.cursosMatriculados.length }}</p>
      <pre>{{ JSON.stringify(cursosStore.cursosMatriculados, null, 2) }}</pre>
    </div>
    
    <div class="section">
      <h2>Estadísticas Computadas:</h2>
      <p><strong>Progreso Promedio:</strong> {{ cursosStore.progresoPromedio }}%</p>
      <p><strong>Cursos En Progreso:</strong> {{ cursosStore.cursosEnProgreso.length }}</p>
      <p><strong>Cursos Completos:</strong> {{ cursosStore.cursosCompletos.length }}</p>
    </div>
    
    <div class="actions">
      <button @click="cargarCursos" :disabled="loading">
        {{ loading ? 'Cargando...' : 'Recargar Cursos' }}
      </button>
      <button @click="limpiarStore">Limpiar Store</button>
    </div>
    
    <div v-if="testResponse" class="section">
      <h2>Última Respuesta de API:</h2>
      <pre>{{ JSON.stringify(testResponse, null, 2) }}</pre>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useCursosStore } from '@/stores/cursos'

const userStore = useAuthStore()
const cursosStore = useCursosStore()

const loading = ref(false)
const testResponse = ref(null)

const cargarCursos = async () => {
  if (!userStore.user?.id) {
    alert('Usuario no autenticado')
    return
  }
  
  loading.value = true
  try {
    console.log('Cargando cursos para estudiante:', userStore.user.id)
    const result = await cursosStore.fetchCursosMatriculados(userStore.user.id)
    testResponse.value = result
    console.log('Resultado:', result)
  } catch (error) {
    console.error('Error al cargar cursos:', error)
    alert('Error: ' + error.message)
  } finally {
    loading.value = false
  }
}

const limpiarStore = () => {
  cursosStore.cursosMatriculados = []
  cursosStore.error = null
  testResponse.value = null
}

onMounted(() => {
  console.log('Componente de diagnóstico montado')
  console.log('Usuario:', userStore.user)
  console.log('Store inicial:', { 
    loading: cursosStore.loading, 
    error: cursosStore.error,
    cursos: cursosStore.cursosMatriculados 
  })
})
</script>

<style scoped>
.diagnostico-estudiante {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.section {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section h2 {
  color: #333;
  margin-bottom: 1rem;
}

pre {
  background: #f5f5f5;
  padding: 1rem;
  border-radius: 4px;
  overflow: auto;
  font-size: 0.875rem;
}

.actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.actions button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  background: #0554f2;
  color: white;
  cursor: pointer;
  font-weight: 500;
}

.actions button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.actions button:hover:not(:disabled) {
  background: #0540f2;
}

.actions button:last-child {
  background: #dc2626;
}

.actions button:last-child:hover {
  background: #b91c1c;
}
</style>
