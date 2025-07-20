<template>
  <div class="materiales-container">
    <div class="materiales-header">
      <div>
        <h1 class="materiales-title">Materiales</h1>
        <p class="materiales-subtitle">Gestiona todos tus materiales digitales</p>
      </div>
      <button @click="abrirModalSubir" class="btn-nuevo-material">
        <PlusIcon class="icon" />
        Subir material
      </button>
    </div>
    
    <div v-if="materialesStore.loading" class="materiales-loading">
      <p>Cargando materiales...</p>
    </div>
    
    <div v-else-if="materialesStore.materiales.length === 0" class="materiales-empty">
      <DocumentTextIcon class="materiales-icon-empty" />
      <h3 class="materiales-empty-title">No tienes materiales subidos</h3>
      <p class="materiales-empty-text">Comienza subiendo tu primer material para compartir con los estudiantes.</p>
      <button @click="abrirModalSubir" class="btn-subir-primero">
        Subir primer material
      </button>
    </div>
    
    <div v-else class="materiales-grid">
      <div 
        v-for="material in materialesStore.materiales" 
        :key="material.id"
        class="material-card"
      >
        <div class="material-card-header">
          <div class="material-card-icon">
            <DocumentTextIcon class="material-icon" />
          </div>
          <span class="material-card-tipo">{{ material.tipo || 'Documento' }}</span>
        </div>
        
        <h3 class="material-card-title">{{ material.titulo }}</h3>
                
        <div class="material-card-meta">
          <div class="material-meta-item">
            <span class="material-meta-label">Curso:</span>
            <span class="material-meta-value">{{ material.modulo?.curso?.titulo || 'N/A' }}</span>
          </div>
        </div>
        
        <div class="material-card-actions">
          <button @click="abrirModalVer(material)" class="btn-material-action btn-ver">Ver</button>
          <!-- <button @click="descargarMaterial(material.id)" class="btn-material-action btn-descargar">Descargar</button> -->
          <button @click="eliminarMaterial(material)" class="btn-material-action btn-eliminar">Eliminar</button>
        </div>
      </div>
    </div>

    <!-- Modal Subir Material -->
    <div v-if="mostrarModalSubir" class="modal-overlay" @click="cerrarModalSubir">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">Subir Nuevo Material</h2>
          <button @click="cerrarModalSubir" class="modal-close">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>
        
        <form @submit.prevent="subirMaterial" class="modal-form">
          <div class="form-group">
            <label for="titulo" class="form-label">Título del material *</label>
            <input
              id="titulo"
              v-model="formSubir.titulo"
              type="text"
              class="form-input"
              placeholder="Ingresa el título del material"
              required
            />
          </div>

          <div class="form-group">
            <label for="tipo" class="form-label">Tipo de material *</label>
            <select
              id="tipo"
              v-model="formSubir.tipo"
              class="form-select"
              required
            >
              <option value="">Selecciona un tipo</option>
              <option value="pdf">PDF</option>
              <option value="link">Enlace</option>
              <option value="zip">Archivo ZIP</option>
            </select>
          </div>

          <div class="form-group">
            <label for="url" class="form-label">URL o archivo *</label>
            <input
              v-if="formSubir.tipo === 'link'"
              id="url"
              v-model="formSubir.url"
              type="url"
              class="form-input"
              placeholder="https://ejemplo.com"
              required
            />
            <input
              v-else
              id="archivo"
              @change="manejarArchivo"
              type="file"
              class="form-file"
              :accept="formSubir.tipo === 'pdf' ? '.pdf' : '.zip'"
              required
            />
          </div>

          <div class="form-group">
            <label for="modulo_id" class="form-label">Módulo (opcional)</label>
            <select
              id="modulo_id"
              v-model="formSubir.modulo_id"
              class="form-select"
            >
              <option value="">Sin módulo específico</option>
              <option v-for="modulo in modulosStore.modulos" :key="modulo.id" :value="modulo.id">
                {{ modulo.titulo }}
              </option>
            </select>
          </div>

          <div v-if="errorSubir" class="form-error">
            <p>{{ errorSubir }}</p>
          </div>

          <div class="modal-actions">
            <button type="button" @click="cerrarModalSubir" class="btn-secondary">
              Cancelar
            </button>
            <button type="submit" :disabled="cargandoSubir" class="btn-primary">
              <span v-if="cargandoSubir">Subiendo...</span>
              <span v-else>Subir Material</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Ver/Editar Material -->
    <div v-if="mostrarModalVer" class="modal-overlay" @click="cerrarModalVer">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">Editar Material</h2>
          <button @click="cerrarModalVer" class="modal-close">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>
        
        <form @submit.prevent="actualizarMaterial" class="modal-form">
          <div class="form-group">
            <label for="titulo-editar" class="form-label">Título del material *</label>
            <input
              id="titulo-editar"
              v-model="formEditar.titulo"
              type="text"
              class="form-input"
              required
            />
          </div>

          <div class="form-group">
            <label for="tipo-editar" class="form-label">Tipo de material *</label>
            <select
              id="tipo-editar"
              v-model="formEditar.tipo"
              class="form-select"
              required
            >
              <option value="pdf">PDF</option>
              <option value="link">Enlace</option>
              <option value="zip">Archivo ZIP</option>
            </select>
          </div>

          <div class="form-group">
            <label for="url-editar" class="form-label">URL *</label>
            <input
              id="url-editar"
              v-model="formEditar.url"
              type="text"
              class="form-input"
              required
            />
          </div>

          <div class="form-group">
            <label for="modulo_id-editar" class="form-label">Módulo (opcional)</label>
            <select
              id="modulo_id-editar"
              v-model="formEditar.modulo_id"
              class="form-select"
            >
              <option value="">Sin módulo específico</option>
              <option v-for="modulo in modulosStore.modulos" :key="modulo.id" :value="modulo.id">
                {{ modulo.titulo }}
              </option>
            </select>
          </div>

          <div v-if="errorEditar" class="form-error">
            <p>{{ errorEditar }}</p>
          </div>

          <div class="modal-actions">
            <button type="button" @click="cerrarModalVer" class="btn-secondary">
              Cancelar
            </button>
            <button type="submit" :disabled="cargandoEditar" class="btn-primary">
              <span v-if="cargandoEditar">Actualizando...</span>
              <span v-else>Actualizar Material</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import { 
  DocumentTextIcon, 
  PlusIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'
import { useMaterialesStore } from '@/stores/materiales'
import { useAuthStore } from '@/stores/auth'
import { useModulosStore } from '@/stores/modulos'

const materialesStore = useMaterialesStore()
const authStore = useAuthStore()
const modulosStore = useModulosStore()

// Estados de los modales
const mostrarModalSubir = ref(false)
const mostrarModalVer = ref(false)
const cargandoSubir = ref(false)
const cargandoEditar = ref(false)
const errorSubir = ref(null)
const errorEditar = ref(null)

// Formularios
const formSubir = reactive({
  titulo: '',
  tipo: '',
  url: '',
  archivo: null,
  modulo_id: ''
})

const formEditar = reactive({
  id: null,
  titulo: '',
  tipo: '',
  url: '',
  modulo_id: ''
})

// Funciones para manejar modales
const abrirModalSubir = () => {
  mostrarModalSubir.value = true
  errorSubir.value = null
  // Limpiar formulario
  Object.assign(formSubir, {
    titulo: '',
    tipo: '',
    url: '',
    archivo: null,
    modulo_id: ''
  })
}

const cerrarModalSubir = () => {
  mostrarModalSubir.value = false
}

const abrirModalVer = (material) => {
  mostrarModalVer.value = true
  errorEditar.value = null
  // Cargar datos del material
  Object.assign(formEditar, {
    id: material.id,
    titulo: material.titulo || '',
    tipo: material.tipo || '',
    url: material.url || '',
    modulo_id: material.modulo_id || ''
  })
}

const cerrarModalVer = () => {
  mostrarModalVer.value = false
}

// Funciones para manejar archivos
const manejarArchivo = (event) => {
  formSubir.archivo = event.target.files[0]
}

// Funciones de la API
const subirMaterial = async () => {
  cargandoSubir.value = true
  errorSubir.value = null

  try {
    const datosFormulario = {
      titulo: formSubir.titulo,
      tipo: formSubir.tipo,
      modulo_id: formSubir.modulo_id || null
    }

    if (formSubir.tipo === 'link') {
      datosFormulario.url = formSubir.url
    } else if (formSubir.archivo) {
      datosFormulario.archivo = formSubir.archivo
    }

    const resultado = await materialesStore.createMaterial(datosFormulario)
    
    if (resultado.success) {
      cerrarModalSubir()
      // Recargar materiales
      const profesorId = authStore.user?.id
      if (profesorId) {
        await materialesStore.fetchMaterialesProfesor(profesorId)
      }
    } else {
      errorSubir.value = resultado.error
    }
  } catch (error) {
    errorSubir.value = 'Error inesperado al subir el material'
    console.error('Error al subir material:', error)
  } finally {
    cargandoSubir.value = false
  }
}

const actualizarMaterial = async () => {
  cargandoEditar.value = true
  errorEditar.value = null

  try {
    const datosFormulario = {
      titulo: formEditar.titulo,
      tipo: formEditar.tipo,
      url: formEditar.url,
      modulo_id: formEditar.modulo_id || null
    }

    const resultado = await materialesStore.updateMaterial(formEditar.id, datosFormulario)
    
    if (resultado.success) {
      cerrarModalVer()
      // Recargar materiales
      const profesorId = authStore.user?.id
      if (profesorId) {
        await materialesStore.fetchMaterialesProfesor(profesorId)
      }
    } else {
      errorEditar.value = resultado.error
    }
  } catch (error) {
    errorEditar.value = 'Error inesperado al actualizar el material'
    console.error('Error al actualizar material:', error)
  } finally {
    cargandoEditar.value = false
  }
}

const eliminarMaterial = async (material) => {
  if (confirm(`¿Estás seguro de que quieres eliminar "${material.titulo}"?`)) {
    const resultado = await materialesStore.deleteMaterial(material.id)
    
    if (resultado.success) {
      // Recargar materiales
      const profesorId = authStore.user?.id
      if (profesorId) {
        await materialesStore.fetchMaterialesProfesor(profesorId)
      }
    } else {
      alert('Error al eliminar el material')
    }
  }
}

const descargarMaterial = async (materialId) => {
  await materialesStore.downloadMaterial(materialId)
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

onMounted(() => {
  materialesStore.fetchMaterialesProfesor(authStore.user?.id)
  modulosStore.fetchModulos()
})
</script>

<style scoped>
.materiales-container {
  padding: 2rem;
}

.materiales-header {
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.materiales-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.materiales-subtitle {
  color: #6b7280;
  font-size: 1.1rem;
}

.btn-nuevo-material {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-nuevo-material:hover {
  background: #0540f2;
}

.icon {
  width: 20px;
  height: 20px;
}

.materiales-loading {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1.1rem;
}

.materiales-empty {
  background: #fff;
  border-radius: 1rem;
  padding: 3rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  text-align: center;
}

.materiales-icon-empty {
  width: 64px;
  height: 64px;
  color: #e5e7eb;
  margin: 0 auto 1rem;
}

.materiales-empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.materiales-empty-text {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.btn-subir-primero {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-subir-primero:hover {
  background: #0540f2;
}

.materiales-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.material-card {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: box-shadow 0.2s;
}

.material-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.10);
}

.material-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.material-card-icon {
  background: #f0f9ff;
  padding: 0.5rem;
  border-radius: 0.5rem;
}

.material-icon {
  width: 24px;
  height: 24px;
  color: #0284c7;
}

.material-card-tipo {
  background: #e0f2fe;
  color: #0c4a6e;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
}

.material-card-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.material-card-description {
  color: #6b7280;
  margin-bottom: 1rem;
  line-height: 1.5;
  font-size: 0.9rem;
}

.material-card-meta {
  background: #f9fafb;
  border-radius: 0.5rem;
  padding: 0.75rem;
  margin-bottom: 1rem;
}

.material-meta-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.25rem;
}

.material-meta-item:last-child {
  margin-bottom: 0;
}

.material-meta-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.material-meta-value {
  font-size: 0.875rem;
  color: #1f2937;
  font-weight: 600;
}

.material-card-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-material-action {
  flex: 1;
  padding: 0.5rem 0.75rem;
  border: none;
  border-radius: 0.375rem;
  font-size: 0.875rem;
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

.btn-descargar {
  background: #0554f2;
  color: #fff;
}

.btn-descargar:hover {
  background: #0540f2;
}

.btn-eliminar {
  background: #fef2f2;
  color: #dc2626;
}

.btn-eliminar:hover {
  background: #fee2e2;
}

/* Estilos para modales */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-container {
  background: #fff;
  border-radius: 1rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.5rem 0 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  margin-bottom: 1.5rem;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 0.5rem;
  transition: all 0.2s;
}

.modal-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-form {
  padding: 0 1.5rem 1.5rem 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input,
.form-select,
.form-file {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: #fff;
}

.form-input:focus,
.form-select:focus,
.form-file:focus {
  outline: none;
  border-color: #0554f2;
  box-shadow: 0 0 0 3px rgba(5, 84, 242, 0.1);
}

.form-file {
  padding: 0.5rem;
}

.form-error {
  background: #fef2f2;
  color: #dc2626;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid #fecaca;
  font-size: 0.875rem;
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-primary {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  min-width: 120px;
}

.btn-primary:hover:not(:disabled) {
  background: #0540f2;
}

.btn-primary:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f9fafb;
  color: #374151;
  padding: 0.75rem 1.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
}
</style>
