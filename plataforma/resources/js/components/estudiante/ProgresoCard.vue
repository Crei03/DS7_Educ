<template>
  <div class="progreso-card">
    <div class="card-header">
      <div class="curso-info">
        <h3 class="curso-titulo">{{ curso.titulo }}</h3>
        <p class="curso-descripcion">{{ curso.descripcion }}</p>
      </div>
      <div class="progreso-badge" :class="getBadgeClass(curso.progreso)">
        {{ curso.progreso }}%
      </div>
    </div>

    <div class="progreso-bar-container">
      <div class="progreso-bar">
        <div 
          class="progreso-fill" 
          :style="{ width: curso.progreso + '%' }"
          :class="getProgressClass(curso.progreso)"
        ></div>
      </div>
      <span class="progreso-texto">{{ getProgressText(curso.progreso) }}</span>
    </div>

    <div class="estadisticas">
      <div class="stat-item">
        <ChartBarIcon class="stat-icon" />
        <span class="stat-label">Módulos</span>
        <span class="stat-value">{{ modulosCompletados }}/{{ totalModulos }}</span>
      </div>
      <div class="stat-item">
        <DocumentTextIcon class="stat-icon" />
        <span class="stat-label">Materiales</span>
        <span class="stat-value">{{ materialesVistos }}/{{ totalMateriales }}</span>
      </div>
      <div class="stat-item">
        <ClockIcon class="stat-icon" />
        <span class="stat-label">Última actividad</span>
        <span class="stat-value">{{ ultimaActividad }}</span>
      </div>
    </div>

    <div class="card-actions">
      <button 
        @click="verDetalle" 
        class="btn-detalle"
      >
        <EyeIcon class="btn-icon" />
        Ver Detalle
      </button>
      <button 
        v-if="curso.progreso < 100" 
        @click="continuarCurso" 
        class="btn-continuar"
      >
        <PlayIcon class="btn-icon" />
        Continuar
      </button>
      <div v-else class="completado-badge">
        <CheckCircleIcon class="completado-icon" />
        Completado
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { 
  ChartBarIcon, 
  DocumentTextIcon, 
  ClockIcon, 
  EyeIcon, 
  PlayIcon, 
  CheckCircleIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
  curso: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['verDetalle', 'continuarCurso'])

const totalModulos = computed(() => {
  return props.curso.modulos?.length || 0
})

const modulosCompletados = computed(() => {
  if (!props.curso.modulos) return 0
  return props.curso.modulos.filter(modulo => modulo.progreso >= 100).length
})

const totalMateriales = computed(() => {
  if (!props.curso.modulos) return 0
  return props.curso.modulos.reduce((total, modulo) => {
    return total + (modulo.materiales?.length || 0)
  }, 0)
})

const materialesVistos = computed(() => {
  if (!props.curso.modulos) return 0
  return props.curso.modulos.reduce((total, modulo) => {
    if (!modulo.materiales) return total
    return total + modulo.materiales.filter(material => material.visto).length
  }, 0)
})

const ultimaActividad = computed(() => {
  // Simular última actividad - en producción vendría del backend
  if (props.curso.ultima_actividad) {
    const fecha = new Date(props.curso.ultima_actividad)
    return fecha.toLocaleDateString('es-ES', { 
      day: 'numeric', 
      month: 'short' 
    })
  }
  return 'Hoy'
})

const getBadgeClass = (progreso) => {
  if (progreso >= 100) return 'completado'
  if (progreso >= 70) return 'avanzado'
  if (progreso >= 30) return 'medio'
  return 'inicio'
}

const getProgressClass = (progreso) => {
  if (progreso >= 100) return 'completado'
  if (progreso >= 70) return 'avanzado'
  if (progreso >= 30) return 'medio'
  return 'inicio'
}

const getProgressText = (progreso) => {
  if (progreso >= 100) return 'Completado'
  if (progreso >= 70) return 'Casi terminado'
  if (progreso >= 30) return 'En progreso'
  return 'Comenzando'
}

const verDetalle = () => {
  emit('verDetalle', props.curso)
}

const continuarCurso = () => {
  emit('continuarCurso', props.curso)
}
</script>

<style scoped>
.progreso-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border: 1px solid #e5e7eb;
}

.progreso-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.curso-info {
  flex: 1;
  margin-right: 1rem;
}

.curso-titulo {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
  line-height: 1.4;
}

.curso-descripcion {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.5;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.progreso-badge {
  padding: 0.5rem 0.75rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  min-width: 60px;
  text-align: center;
}

.progreso-badge.inicio {
  background: #fef3c7;
  color: #92400e;
}

.progreso-badge.medio {
  background: #dbeafe;
  color: #1d4ed8;
}

.progreso-badge.avanzado {
  background: #d1fae5;
  color: #065f46;
}

.progreso-badge.completado {
  background: #d1fae5;
  color: #065f46;
}

.progreso-bar-container {
  margin-bottom: 1.5rem;
}

.progreso-bar {
  width: 100%;
  height: 8px;
  background: #f3f4f6;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.progreso-fill {
  height: 100%;
  transition: width 0.3s ease;
  border-radius: 4px;
}

.progreso-fill.inicio {
  background: #f59e0b;
}

.progreso-fill.medio {
  background: #3b82f6;
}

.progreso-fill.avanzado {
  background: #10b981;
}

.progreso-fill.completado {
  background: #059669;
}

.progreso-texto {
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
}

.estadisticas {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.stat-icon {
  width: 1.25rem;
  height: 1.25rem;
  color: #0554f2;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
}

.card-actions {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.btn-detalle,
.btn-continuar {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.btn-detalle {
  background: #f3f4f6;
  color: #374151;
}

.btn-detalle:hover {
  background: #e5e7eb;
}

.btn-continuar {
  background: #0554f2;
  color: white;
}

.btn-continuar:hover {
  background: #0540f2;
}

.btn-icon {
  width: 1rem;
  height: 1rem;
}

.completado-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #d1fae5;
  color: #065f46;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
}

.completado-icon {
  width: 1rem;
  height: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .progreso-card {
    padding: 1rem;
  }

  .card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .estadisticas {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
    padding: 0.75rem;
  }

  .card-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .btn-detalle,
  .btn-continuar {
    justify-content: center;
  }
}
</style>
