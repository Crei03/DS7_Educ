<template>
  <div class="material-item" :class="{ 'visto': material.visto }">
    <div class="material-info">
      <div class="material-icon">
        <component :is="getMaterialIcon(material.tipo)" class="icon" />
      </div>
      <div class="material-details">
        <h4 class="material-titulo">{{ material.titulo || 'Sin título' }}</h4>
        <div class="material-meta">
          <span class="material-tipo">{{ getTipoLabel(material.tipo) }}</span>
          <span v-if="material.duracion" class="material-duracion">
            {{ material.duracion }} min
          </span>
        </div>
      </div>
    </div>

    <div class="material-actions">
      <button
        v-if="!material.visto"
        @click="marcarVisto"
        class="btn-marcar"
        title="Marcar como visto"
      >
        <EyeIcon class="btn-icon" />
      </button>
      <div v-else class="visto-badge">
        <CheckCircleIcon class="visto-icon" />
        <span>Visto</span>
      </div>
      
      <a 
        :href="material.url" 
        target="_blank" 
        rel="noopener noreferrer"
        class="btn-abrir"
        title="Abrir material"
      >
        <ArrowTopRightOnSquareIcon class="btn-icon" />
      </a>
    </div>
  </div>
</template>

<script setup>
import { 
  DocumentTextIcon,
  LinkIcon,
  ArchiveBoxIcon,
  PlayIcon,
  EyeIcon,
  CheckCircleIcon,
  ArrowTopRightOnSquareIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  material: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['marcar-visto'])

const getMaterialIcon = (tipo) => {
  const iconos = {
    'pdf': DocumentTextIcon,
    'link': LinkIcon,
    'zip': ArchiveBoxIcon,
    'video': PlayIcon,
    'documento': DocumentTextIcon
  }
  return iconos[tipo] || DocumentTextIcon
}

const getTipoLabel = (tipo) => {
  const labels = {
    'pdf': 'PDF',
    'link': 'Enlace',
    'zip': 'Archivo ZIP',
    'video': 'Video',
    'documento': 'Documento'
  }
  return labels[tipo] || 'Material'
}

const marcarVisto = () => {
  emit('marcar-visto', props.material.id)
}
</script>

<style scoped>
.material-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  margin-bottom: 0.75rem;
  background: white;
  transition: all 0.2s ease;
}

.material-item:hover {
  border-color: #d1d5db;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.material-item.visto {
  background: #f0fdf4;
  border-color: #bbf7d0;
}

.material-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 1;
}

.material-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #f3f4f6;
}

.material-item.visto .material-icon {
  background: #dcfce7;
}

.icon {
  width: 1.25rem;
  height: 1.25rem;
  color: #6b7280;
}

.material-item.visto .icon {
  color: #16a34a;
}

.material-details {
  flex: 1;
}

.material-titulo {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
  line-height: 1.4;
}

.material-meta {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.material-tipo {
  padding: 0.125rem 0.5rem;
  background: #f3f4f6;
  color: #6b7280;
  font-size: 0.75rem;
  border-radius: 12px;
  font-weight: 500;
}

.material-item.visto .material-tipo {
  background: #dcfce7;
  color: #16a34a;
}

.material-duracion {
  font-size: 0.75rem;
  color: #9ca3af;
}

.material-actions {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.btn-marcar,
.btn-abrir {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.btn-marcar:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-abrir:hover {
  background: #f0f9ff;
  border-color: #0ea5e9;
}

.btn-icon {
  width: 1rem;
  height: 1rem;
  color: #6b7280;
}

.btn-abrir:hover .btn-icon {
  color: #0ea5e9;
}

.visto-badge {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  background: #dcfce7;
  color: #16a34a;
  border-radius: 16px;
  font-size: 0.75rem;
  font-weight: 600;
}

.visto-icon {
  width: 1rem;
  height: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .material-item {
    padding: 0.75rem;
  }

  .material-info {
    gap: 0.5rem;
  }

  .material-icon {
    width: 36px;
    height: 36px;
  }

  .icon {
    width: 1rem;
    height: 1rem;
  }

  .material-titulo {
    font-size: 0.8125rem;
  }

  .material-meta {
    gap: 0.5rem;
  }

  .btn-marcar,
  .btn-abrir {
    width: 32px;
    height: 32px;
  }

  .visto-badge {
    padding: 0.25rem 0.5rem;
    gap: 0.25rem;
  }

  .visto-badge span {
    display: none;
  }
}
</style>
