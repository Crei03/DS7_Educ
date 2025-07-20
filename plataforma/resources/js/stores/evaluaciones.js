import { defineStore } from 'pinia'
import api from '@/services/api'

export const useEvaluacionesStore = defineStore('evaluaciones', {
    state: () => ({
        evaluaciones: [],
        evaluacionActual: null,
        loading: false,
        error: null
    }),

    getters: {
        totalEvaluaciones: (state) => state.evaluaciones.length,
        evaluacionesPendientes: (state) => state.evaluaciones.filter(evaluacion => evaluacion.estado === 'pendiente'),
        evaluacionesCompletadas: (state) => state.evaluaciones.filter(evaluacion => evaluacion.estado === 'completada')
    },

    actions: {
        async fetchEvaluacionesProfesor(profesorId) {
            this.loading = true
            this.error = null

            try {
                const response = await api.get(`/profesores/${profesorId}/evaluaciones`)
                // La API retorna { data: { evaluaciones: [...] } }
                const evaluaciones = response.data.data?.evaluaciones || response.data.evaluaciones || []

                // Enriquecer datos calculados para cada evaluación
                this.evaluaciones = evaluaciones.map(evaluacion => ({
                    ...evaluacion,
                    preguntas_count: evaluacion.preguntas_count || evaluacion.preguntas?.length || 0,
                    respuestas_count: evaluacion.resultados_count || evaluacion.resultados?.length || 0
                }))
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener evaluaciones del profesor'
                console.error('Error al obtener evaluaciones del profesor:', error)
            } finally {
                this.loading = false
            }
        },
        async fetchEvaluaciones() {
            this.loading = true
            this.error = null

            try {
                const response = await api.get('/evaluaciones')
                this.evaluaciones = response.data.data || response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener evaluaciones'
                console.error('Error al obtener evaluaciones:', error)
            } finally {
                this.loading = false
            }
        },

        async createEvaluacion(evaluacionData) {
            this.loading = true
            this.error = null

            try {
                const response = await api.post('/evaluaciones', evaluacionData)
                const nuevaEvaluacion = response.data.evaluaciones || response.data.data

                this.evaluaciones.push(nuevaEvaluacion)
                return { success: true, evaluacion: nuevaEvaluacion }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al crear evaluación'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async updateEvaluacion(evaluacionId, evaluacionData) {
            this.loading = true
            this.error = null

            try {
                const response = await api.put(`/evaluaciones/${evaluacionId}`, evaluacionData)
                const evaluacionActualizada = response.data.data || response.data

                const index = this.evaluaciones.findIndex(evaluacion => evaluacion.id === evaluacionId)
                if (index !== -1) {
                    this.evaluaciones[index] = evaluacionActualizada
                }

                return { success: true, evaluacion: evaluacionActualizada }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar evaluación'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async deleteEvaluacion(evaluacionId) {
            this.loading = true
            this.error = null

            try {
                await api.delete(`/evaluaciones/${evaluacionId}`)
                this.evaluaciones = this.evaluaciones.filter(evaluacion => evaluacion.id !== evaluacionId)
                return { success: true }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al eliminar evaluación'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async fetchResultadosEvaluacion(evaluacionId) {
            try {
                const response = await api.get(`/evaluaciones/${evaluacionId}/resultados`)
                return response.data.data || response.data
            } catch (error) {
                console.error('Error al obtener resultados de evaluación:', error)
                throw error // Propagamos el error para que el componente lo maneje
            }
        },

        // Helper para refrescar datos después de operaciones CRUD
        async refrescarEvaluacionesProfesor(profesorId) {
            await this.fetchEvaluacionesProfesor(profesorId)
        }
    }
})
