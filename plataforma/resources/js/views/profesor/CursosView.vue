<template>
  <div class="cursos-container">
    <div class="cursos-header">
      <div>
        <h1 class="cursos-title">Mis Cursos</h1>
        <p class="cursos-subtitle">Gestiona todos tus cursos activos</p>
      </div>
      <router-link to="/profesor/crear-curso" class="btn-nuevo-curso">
        <PlusIcon class="icon" />
        Crear nuevo curso
      </router-link>
    </div>
    
    <div v-if="cursosStore.loading" class="cursos-loading">
      <p>Cargando cursos...</p>
    </div>
    
    <div v-else-if="cursosStore.cursosProfesor.length === 0" class="cursos-empty">
      <BookOpenIcon class="cursos-icon-empty" />
      <h3 class="cursos-empty-title">No tienes cursos creados</h3>
      <p class="cursos-empty-text">Comienza creando tu primer curso para la plataforma.</p>
      <router-link to="/profesor/crear-curso" class="btn-crear-primero">
        Crear primer curso
      </router-link>
    </div>
    
    <div v-else class="cursos-grid">
      <div 
        v-for="curso in cursosStore.cursosProfesor" 
        :key="curso.id"
        class="curso-card"
      >
        <div class="curso-card-header">
          <h3 class="curso-card-title">{{ curso.titulo }}</h3>
        </div>
        
        <p class="curso-card-description">{{ curso.descripcion || 'Sin descripción' }}</p>
        
        <div class="curso-card-stats">
          <div class="curso-stat">
            <UsersIcon class="curso-stat-icon" />
            <span class="curso-stat-number">{{ curso.estudiantes_count || 0 }}</span>
            <span class="curso-stat-label">Estudiantes</span>
          </div>
          <div class="curso-stat">
            <DocumentTextIcon class="curso-stat-icon" />
            <span class="curso-stat-number">{{ curso.materiales_count || 0 }}</span>
            <span class="curso-stat-label">Materiales</span>
          </div>
          <div class="curso-stat">
            <ClipboardDocumentListIcon class="curso-stat-icon" />
            <span class="curso-stat-number">{{ curso.evaluaciones_count || 0 }}</span>
            <span class="curso-stat-label">Evaluaciones</span>
          </div>
        </div>
        
        <div class="curso-card-actions">
          <button class="btn-curso-action btn-ver">Ver detalles</button>
          <button class="btn-curso-action btn-editar">Editar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { 
  BookOpenIcon, 
  PlusIcon, 
  UsersIcon,
  DocumentTextIcon,
  ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline'
import { useCursosStore } from '@/stores/cursos'
import { useAuthStore } from '@/stores/auth'

const cursosStore = useCursosStore()
const authStore = useAuthStore()

onMounted(() => {
  const profesorId = authStore.user?.id
  if (profesorId) {
    cursosStore.fetchCursosProfesor(profesorId)
  }
})
</script>

<style scoped>
.cursos-container {
  padding: 2rem;
}

.cursos-header {
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.cursos-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.cursos-subtitle {
  color: #6b7280;
  font-size: 1.1rem;
}

.btn-nuevo-curso {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background 0.2s;
}

.btn-nuevo-curso:hover {
  background: #0540f2;
}

.icon {
  width: 20px;
  height: 20px;
}

.cursos-loading {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1.1rem;
}

.cursos-empty {
  background: #fff;
  border-radius: 1rem;
  padding: 3rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  text-align: center;
}

.cursos-icon-empty {
  width: 64px;
  height: 64px;
  color: #e5e7eb;
  margin: 0 auto 1rem;
}

.cursos-empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.cursos-empty-text {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.btn-crear-primero {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 600;
  display: inline-block;
  transition: background 0.2s;
}

.btn-crear-primero:hover {
  background: #0540f2;
}

.cursos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.curso-card {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: box-shadow 0.2s;
}

.curso-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.10);
}

.curso-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.curso-card-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.curso-card-status {
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.875rem;
  font-weight: 500;
}

.curso-activo {
  background: #dcfce7;
  color: #166534;
}

.curso-inactivo {
  background: #fef2f2;
  color: #dc2626;
}

.curso-card-description {
  color: #6b7280;
  margin-bottom: 1.5rem;
  line-height: 1.5;
}

.curso-card-stats {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.curso-stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.curso-stat-icon {
  width: 20px;
  height: 20px;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.curso-stat-number {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.curso-stat-label {
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.curso-card-actions {
  display: flex;
  gap: 0.75rem;
}

.btn-curso-action {
  flex: 1;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-ver {
  background: #f3f4f6;
  color: #374151;
}

.btn-ver:hover {
  background: #e5e7eb;
}

.btn-editar {
  background: #0554f2;
  color: #fff;
}

.btn-editar:hover {
  background: #0540f2;
}
</style>
