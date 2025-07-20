<template>
  <div class="modulo-detalle">
    <div class="modulo-header" @click="toggleModulo">
      <div class="modulo-info">
        <div class="modulo-titulo">
          <ChevronRightIcon 
            class="chevron-icon" 
            :class="{ 'rotated': isExpanded }" 
          />
          <span>{{ modulo.titulo }}</span>
        </div>
        <div class="modulo-stats">
          <span class="stat-badge">
            {{ materialesCompletados }}/{{ totalMateriales }} materiales
          </span>
        </div>
      </div>
      <div class="modulo-progreso">
        <div class="progreso-circular" :class="getProgressClass(modulo.progreso)">
          <span class="progreso-porcentaje">{{ modulo.progreso }}%</span>
        </div>
      </div>
    </div>

    <div v-show="isExpanded" class="modulo-contenido">
      <div class="materiales-lista">
        <MaterialItem
          v-for="material in modulo.materiales"
          :key="material.id"
          :material="material"
          @marcar-visto="marcarMaterialVisto"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'
import MaterialItem from './MaterialItem.vue'

const props = defineProps({
  modulo: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['materialVisto'])

const isExpanded = ref(false)

const totalMateriales = computed(() => {
  return props.modulo.materiales?.length || 0
})

const materialesCompletados = computed(() => {
  if (!props.modulo.materiales) return 0
  return props.modulo.materiales.filter(material => material.visto).length
})

const getProgressClass = (progreso) => {
  if (progreso >= 100) return 'completado'
  if (progreso >= 70) return 'avanzado'
  if (progreso >= 30) return 'medio'
  return 'inicio'
}

const toggleModulo = () => {
  isExpanded.value = !isExpanded.value
}

const marcarMaterialVisto = (materialId) => {
  emit('materialVisto', materialId)
}
</script>

<style scoped>
.modulo-detalle {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  margin-bottom: 1rem;
  overflow: hidden;
}

.modulo-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.modulo-header:hover {
  background: #f9fafb;
}

.modulo-info {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
}

.modulo-titulo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #1f2937;
  font-size: 1rem;
}

.chevron-icon {
  width: 1rem;
  height: 1rem;
  transition: transform 0.2s ease;
  color: #6b7280;
}

.chevron-icon.rotated {
  transform: rotate(90deg);
}

.modulo-stats {
  display: flex;
  gap: 0.5rem;
}

.stat-badge {
  padding: 0.25rem 0.5rem;
  background: #f3f4f6;
  color: #6b7280;
  font-size: 0.75rem;
  border-radius: 12px;
  font-weight: 500;
}

.modulo-progreso {
  display: flex;
  align-items: center;
}

.progreso-circular {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid;
  position: relative;
}

.progreso-circular.inicio {
  border-color: #fbbf24;
  background: #fef3c7;
}

.progreso-circular.medio {
  border-color: #3b82f6;
  background: #dbeafe;
}

.progreso-circular.avanzado {
  border-color: #10b981;
  background: #d1fae5;
}

.progreso-circular.completado {
  border-color: #059669;
  background: #d1fae5;
}

.progreso-porcentaje {
  font-size: 0.75rem;
  font-weight: 700;
  color: #1f2937;
}

.modulo-contenido {
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}

.materiales-lista {
  padding: 1rem 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .modulo-header {
    padding: 0.75rem 1rem;
  }

  .modulo-titulo {
    font-size: 0.875rem;
  }

  .progreso-circular {
    width: 50px;
    height: 50px;
  }

  .materiales-lista {
    padding: 0.75rem 1rem;
  }
}
</style>
