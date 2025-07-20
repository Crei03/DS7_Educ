<template>
  <div class="crear-curso-container">
    <div class="crear-curso-header">
      <h1 class="crear-curso-title">Crear Curso</h1>
      <p class="crear-curso-subtitle">Completa el formulario para crear un nuevo curso</p>
    </div>

    <form @submit.prevent="crearCurso" class="crear-curso-form">
      <div class="form-group">
        <label for="titulo" class="form-label">Título del curso *</label>
        <input
          id="titulo"
          v-model="form.titulo"
          type="text"
          class="form-input"
          placeholder="Ej: Matemáticas I"
          required
        />
      </div>

      <div class="form-group">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea
          id="descripcion"
          v-model="form.descripcion"
          class="form-textarea"
          placeholder="Describe el contenido del curso"
          rows="4"
        ></textarea>
      </div>

      <!-- Mensajes de error -->
      <div v-if="error" class="form-error">
        <p>{{ error }}</p>
      </div>

      <!-- Botones de acción -->
      <div class="form-actions">
        <button
          type="button"
          @click="$router.go(-1)"
          class="btn-secondary"
          :disabled="loading"
        >
          Cancelar
        </button>
        <button
          type="submit"
          class="btn-primary"
          :disabled="loading"
        >
          <span v-if="loading">Creando...</span>
          <span v-else>Crear Curso</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCursosStore } from '@/stores/cursos'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const cursosStore = useCursosStore()
const authStore = useAuthStore()

const loading = ref(false)
const error = ref(null)

// Formulario reactivo
const form = reactive({
  titulo: '',
  descripcion: '',
  profesor_id: authStore.user?.id
})

const crearCurso = async () => {
  loading.value = true
  error.value = null

  try {
    const resultado = await cursosStore.createCurso(form)
    if (resultado.success) {
      router.push('/profesor/cursos')
    } else {
      error.value = resultado.error
    }
  } catch (err) {
    error.value = 'Error inesperado al crear el curso'
    console.error('Error al crear curso:', err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.crear-curso-container {
  padding: 2rem;
  max-width: 800px;
  margin: 0 auto;
}
.crear-curso-header {
  margin-bottom: 2rem;
}
.crear-curso-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}
.crear-curso-subtitle {
  color: #6b7280;
  font-size: 1.1rem;
}

.crear-curso-form {
  background: #fff;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-label {
  display: block;
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input, 
.form-textarea, 
.form-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: #0554f2;
}

.form-textarea {
  resize: vertical;
}

.form-checkbox {
  width: auto;
  margin-right: 0.5rem;
}

.form-error {
  background: #fef2f2;
  color: #dc2626;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid #fecaca;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-primary {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
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
  padding: 0.75rem 2rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-secondary:hover:not(:disabled) {
  background: #f3f4f6;
}

.btn-secondary:disabled {
  background: #f9fafb;
  color: #9ca3af;
  cursor: not-allowed;
}
</style>
