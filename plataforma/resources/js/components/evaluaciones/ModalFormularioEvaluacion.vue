<template>
  <div v-if="isOpen" class="modal-overlay" @click="cerrarModal">
    <div class="modal-container" @click.stop>
      <div class="modal-header">
        <h2 class="modal-title">{{ evaluacionId ? 'Editar Evaluación' : 'Nueva Evaluación' }}</h2>
        <button class="btn-cerrar" @click="cerrarModal">
          <XMarkIcon class="icon" />
        </button>
      </div>

      <div v-if="loading" class="modal-loading">
        <div class="spinner"></div>
        <p>{{ evaluacionId ? 'Cargando evaluación...' : 'Creando evaluación...' }}</p>
      </div>

      <div v-else class="modal-content">
        <form @submit.prevent="guardarEvaluacion" class="evaluacion-form">
          <!-- Información básica -->
          <div class="form-section">
            <h3 class="form-section-title">Información General</h3>
            
            <div class="form-group">
              <label for="titulo" class="form-label">Título de la evaluación</label>
              <input
                id="titulo"
                v-model="formulario.titulo"
                type="text"
                class="form-input"
                :class="{ 'form-input-error': errores.titulo }"
                placeholder="Ej: Examen Final de Laravel"
                maxlength="120"
                required
              />
              <span v-if="errores.titulo" class="form-error">{{ errores.titulo[0] }}</span>
            </div>

            <div class="form-group">
              <label for="curso_id" class="form-label">Curso</label>
              <select
                id="curso_id"
                v-model="formulario.curso_id"
                class="form-input"
                :class="{ 'form-input-error': errores.curso_id }"
                required
              >
                <option value="">Seleccionar curso</option>
                <option v-for="curso in cursos" :key="curso.id" :value="curso.id">
                  {{ curso.titulo }}
                </option>
              </select>
              <span v-if="errores.curso_id" class="form-error">{{ errores.curso_id[0] }}</span>
            </div>

            <div class="form-group">
              <label for="duracion_min" class="form-label">Duración (minutos)</label>
              <input
                id="duracion_min"
                v-model.number="formulario.duracion_min"
                type="number"
                class="form-input"
                :class="{ 'form-input-error': errores.duracion_min }"
                placeholder="60"
                min="1"
                max="300"
              />
              <span v-if="errores.duracion_min" class="form-error">{{ errores.duracion_min[0] }}</span>
              <small class="form-help">Dejar vacío para tiempo ilimitado</small>
            </div>
          </div>

          <!-- Preguntas -->
          <div class="form-section">
            <div class="form-section-header">
              <h3 class="form-section-title">Preguntas</h3>
              <button type="button" @click="agregarPregunta" class="btn-add-pregunta">
                <PlusIcon class="btn-icon" />
                Agregar pregunta
              </button>
            </div>

            <div v-if="formulario.preguntas.length === 0" class="preguntas-empty">
              <QuestionMarkCircleIcon class="icon-empty" />
              <p>No hay preguntas agregadas</p>
              <small>Agrega al menos una pregunta para crear la evaluación</small>
            </div>

            <div v-else class="preguntas-lista">
              <div 
                v-for="(pregunta, preguntaIndex) in formulario.preguntas" 
                :key="preguntaIndex"
                class="pregunta-item"
              >
                <div class="pregunta-header">
                  <span class="pregunta-numero">Pregunta {{ preguntaIndex + 1 }}</span>
                  <button 
                    type="button" 
                    @click="eliminarPregunta(preguntaIndex)"
                    class="btn-eliminar-pregunta"
                  >
                    <TrashIcon class="btn-icon" />
                  </button>
                </div>

                <div class="form-group">
                  <label class="form-label">Enunciado</label>
                  <textarea
                    v-model="pregunta.enunciado"
                    class="form-textarea"
                    :class="{ 'form-input-error': errores[`preguntas.${preguntaIndex}.enunciado`] }"
                    placeholder="Escribe aquí la pregunta..."
                    rows="3"
                    required
                  ></textarea>
                  <span v-if="errores[`preguntas.${preguntaIndex}.enunciado`]" class="form-error">
                    {{ errores[`preguntas.${preguntaIndex}.enunciado`][0] }}
                  </span>
                </div>

                <div class="opciones-container">
                  <label class="form-label">Opciones de respuesta</label>
                  <div 
                    v-for="(opcion, opcionIndex) in pregunta.opciones" 
                    :key="opcionIndex"
                    class="opcion-item"
                  >
                    <div class="opcion-input-group">
                      <input
                        type="radio"
                        :name="`correcta_${preguntaIndex}`"
                        :checked="opcion.es_correcta"
                        @change="marcarCorrecta(preguntaIndex, opcionIndex)"
                        class="opcion-radio"
                      />
                      <input
                        v-model="opcion.texto"
                        type="text"
                        class="form-input opcion-input"
                        :placeholder="`Opción ${String.fromCharCode(65 + opcionIndex)}`"
                        required
                      />
                      <button 
                        v-if="pregunta.opciones.length > 2"
                        type="button" 
                        @click="eliminarOpcion(preguntaIndex, opcionIndex)"
                        class="btn-eliminar-opcion"
                      >
                        <XMarkIcon class="btn-icon" />
                      </button>
                    </div>
                  </div>
                  
                  <button 
                    v-if="pregunta.opciones.length < 6"
                    type="button" 
                    @click="agregarOpcion(preguntaIndex)"
                    class="btn-add-opcion"
                  >
                    <PlusIcon class="btn-icon" />
                    Agregar opción
                  </button>
                  
                  <small class="form-help">Selecciona la opción correcta marcando el círculo</small>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-modal btn-secondary" @click="cerrarModal">
          Cancelar
        </button>
        <button 
          type="button" 
          @click="guardarEvaluacion" 
          class="btn-modal btn-primary"
          :disabled="loading || !puedeGuardar"
        >
          <span v-if="loading" class="btn-spinner"></span>
          {{ evaluacionId ? 'Actualizar' : 'Crear' }} Evaluación
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import {
  XMarkIcon,
  PlusIcon,
  QuestionMarkCircleIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import { useEvaluacionesStore } from '@/stores/evaluaciones'
import { useCursosStore } from '@/stores/cursos'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  isOpen: Boolean,
  evaluacionId: {
    type: Number,
    default: null
  }
})

const emit = defineEmits(['close', 'success'])

const evaluacionesStore = useEvaluacionesStore()
const cursosStore = useCursosStore()
const authStore = useAuthStore()

const loading = ref(false)
const cursos = ref([])
const errores = ref({})

const formulario = reactive({
  titulo: '',
  curso_id: '',
  duracion_min: '',
  preguntas: []
})

const puedeGuardar = computed(() => {
  return formulario.titulo.trim() && 
         formulario.curso_id && 
         formulario.preguntas.length > 0 &&
         formulario.preguntas.every(p => 
           p.enunciado.trim() && 
           p.opciones.length >= 2 && 
           p.opciones.every(o => o.texto.trim()) &&
           p.opciones.some(o => o.es_correcta)
         )
})

const limpiarFormulario = () => {
  formulario.titulo = ''
  formulario.curso_id = ''
  formulario.duracion_min = ''
  formulario.preguntas = []
  errores.value = {}
}

const cerrarModal = () => {
  emit('close')
  limpiarFormulario()
}

const cargarCursos = async () => {
  try {
    // Cargar cursos del profesor actual
    const profesorId = authStore.user?.id
    if (profesorId) {
      await cursosStore.fetchCursosProfesor(profesorId)
      cursos.value = cursosStore.cursosProfesor
    }
  } catch (error) {
    console.error('Error cargando cursos:', error)
  }
}

const agregarPregunta = () => {
  formulario.preguntas.push({
    enunciado: '',
    opciones: [
      { texto: '', es_correcta: true },
      { texto: '', es_correcta: false }
    ]
  })
}

const eliminarPregunta = (index) => {
  formulario.preguntas.splice(index, 1)
}

const agregarOpcion = (preguntaIndex) => {
  formulario.preguntas[preguntaIndex].opciones.push({
    texto: '',
    es_correcta: false
  })
}

const eliminarOpcion = (preguntaIndex, opcionIndex) => {
  const pregunta = formulario.preguntas[preguntaIndex]
  if (pregunta.opciones[opcionIndex].es_correcta) {
    // Si eliminamos la correcta, marcar la primera como correcta
    if (pregunta.opciones.length > 1) {
      const siguienteIndex = opcionIndex === 0 ? 1 : 0
      pregunta.opciones[siguienteIndex].es_correcta = true
    }
  }
  pregunta.opciones.splice(opcionIndex, 1)
}

const marcarCorrecta = (preguntaIndex, opcionIndex) => {
  const pregunta = formulario.preguntas[preguntaIndex]
  pregunta.opciones.forEach((opcion, index) => {
    opcion.es_correcta = index === opcionIndex
  })
}

const guardarEvaluacion = async () => {
  if (!puedeGuardar.value) return

  loading.value = true
  errores.value = {}

  try {
    const datos = {
      titulo: formulario.titulo.trim(),
      curso_id: parseInt(formulario.curso_id),
      duracion_min: formulario.duracion_min || null,
      preguntas: formulario.preguntas.map(p => ({
        enunciado: p.enunciado.trim(),
        opciones: p.opciones.map(o => ({
          texto: o.texto.trim(),
          es_correcta: o.es_correcta
        }))
      }))
    }

    let resultado
    if (props.evaluacionId) {
      resultado = await evaluacionesStore.updateEvaluacion(props.evaluacionId, datos)
    } else {
      resultado = await evaluacionesStore.createEvaluacion(datos)
    }

    if (resultado.success) {
      emit('success', resultado.evaluacion)
      cerrarModal()
      
      // Refrescar lista de evaluaciones
      const profesorId = authStore.user?.id
      if (profesorId) {
        evaluacionesStore.refrescarEvaluacionesProfesor(profesorId)
      }
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errores.value = error.response.data.errors
    }
    console.error('Error guardando evaluación:', error)
  } finally {
    loading.value = false
  }
}

// Cargar datos cuando se abre el modal
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    cargarCursos()
    if (!props.evaluacionId) {
      // Agregar una pregunta por defecto para nuevas evaluaciones
      agregarPregunta()
    }
  }
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 1rem;
  width: 100%;
  max-width: 700px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  flex-shrink: 0;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.btn-cerrar {
  background: none;
  border: none;
  padding: 0.5rem;
  cursor: pointer;
  border-radius: 0.375rem;
  transition: background-color 0.2s;
}

.btn-cerrar:hover {
  background: #f3f4f6;
}

.icon {
  width: 24px;
  height: 24px;
  color: #6b7280;
}

.modal-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #0554f2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.modal-content {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
}

.form-section {
  margin-bottom: 2rem;
}

.form-section:last-child {
  margin-bottom: 0;
}

.form-section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.form-section-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #0554f2;
  box-shadow: 0 0 0 3px rgba(5, 84, 242, 0.1);
}

.form-input-error {
  border-color: #dc2626;
}

.form-error {
  display: block;
  font-size: 0.75rem;
  color: #dc2626;
  margin-top: 0.25rem;
}

.form-help {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.btn-add-pregunta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #0554f2;
  color: white;
  border: none;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-add-pregunta:hover {
  background: #0540f2;
}

.btn-icon {
  width: 16px;
  height: 16px;
}

.preguntas-empty {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
  background: #f9fafb;
  border-radius: 0.5rem;
  border: 2px dashed #e5e7eb;
}

.icon-empty {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  color: #d1d5db;
}

.preguntas-lista {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.pregunta-item {
  background: #f9fafb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
}

.pregunta-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.pregunta-numero {
  background: #0554f2;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 600;
}

.btn-eliminar-pregunta {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-eliminar-pregunta:hover {
  background: #b91c1c;
}

.opciones-container {
  margin-top: 1rem;
}

.opcion-item {
  margin-bottom: 0.75rem;
}

.opcion-input-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.opcion-radio {
  flex-shrink: 0;
}

.opcion-input {
  flex: 1;
  margin: 0;
}

.btn-eliminar-opcion {
  background: #f3f4f6;
  color: #6b7280;
  border: none;
  padding: 0.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: background-color 0.2s;
  flex-shrink: 0;
}

.btn-eliminar-opcion:hover {
  background: #e5e7eb;
  color: #374151;
}

.btn-add-opcion {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: #f3f4f6;
  color: #374151;
  border: none;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-top: 0.5rem;
}

.btn-add-opcion:hover {
  background: #e5e7eb;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  flex-shrink: 0;
}

.btn-modal {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-modal:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover:not(:disabled) {
  background: #e5e7eb;
}

.btn-primary {
  background: #0554f2;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #0540f2;
}

.btn-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #ffffff40;
  border-top: 2px solid #ffffff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
</style>
