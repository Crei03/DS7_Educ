import { defineStore } from 'pinia'
import api from '@/services/api'

export const useEvaluacionesEstudianteStore = defineStore('evaluacionesEstudiante', {
    state: () => ({
        evaluacionesCurso: [],
        evaluacionActiva: null,
        historialEvaluaciones: [],
        cargandoEvaluaciones: false,
        cargandoHistorial: false,
        resolviendoEvaluacion: false,
        tiempoRestante: null,
        timerInterval: null
    }),

    getters: {
        evaluacionesPendientes: (state) => {
            return state.evaluacionesCurso.filter(
                evaluacion => !evaluacion.resultado_estudiante?.completada
            )
        },

        evaluacionesCompletadas: (state) => {
            return state.evaluacionesCurso.filter(
                evaluacion => evaluacion.resultado_estudiante?.completada
            )
        },

        puntajePromedio: (state) => {
            const completadas = state.evaluacionesCompletadas

            if (completadas.length === 0) return 0

            const suma = completadas.reduce((acc, evaluacion) => {
                const puntaje = evaluacion.resultado_estudiante?.puntaje || 0
                return acc + puntaje
            }, 0)

            const promedio = suma / completadas.length
            return isNaN(promedio) ? 0 : Math.round(promedio)
        },

        evaluacionEnProgreso: (state) => {
            return state.evaluacionActiva !== null
        }
    },

    actions: {
        /**
         * Cargar evaluaciones de un curso específico
         */
        async cargarEvaluacionesCurso(cursoId) {
            this.cargandoEvaluaciones = true

            try {
                const response = await api.get(`/estudiante/cursos/${cursoId}/evaluaciones`)
                this.evaluacionesCurso = response.data
                return response.data
            } catch (error) {
                console.error('Error al cargar evaluaciones:', error)
                throw error
            } finally {
                this.cargandoEvaluaciones = false
            }
        },

        /**
         * Cargar historial completo de evaluaciones del estudiante
         */
        async cargarHistorialEvaluaciones() {
            this.cargandoHistorial = true

            try {
                const response = await api.get('/estudiante/evaluaciones/historial')
                this.historialEvaluaciones = response.data
                return response.data
            } catch (error) {
                console.error('Error al cargar historial:', error)
                throw error
            } finally {
                this.cargandoHistorial = false
            }
        },

        /**
         * Iniciar una evaluación específica
         */
        async iniciarEvaluacion(evaluacionId) {
            try {
                const response = await api.get(`/evaluaciones/${evaluacionId}`)
                this.evaluacionActiva = response.data

                // Inicializar timer si la evaluación tiene duración
                if (response.data.duracion_min) {
                    this.iniciarTimer(response.data.duracion_min)
                }

                return response.data
            } catch (error) {
                console.error('Error al cargar evaluación:', error)
                throw error
            }
        },

        /**
         * Iniciar timer para la evaluación
         */
        iniciarTimer(duracionMinutos) {
            this.tiempoRestante = duracionMinutos * 60 // convertir a segundos

            this.timerInterval = setInterval(() => {
                if (this.tiempoRestante > 0) {
                    this.tiempoRestante--
                } else {
                    // Tiempo agotado - enviar automáticamente
                    this.tiempoAgotado()
                }
            }, 1000)
        },

        /**
         * Detener timer
         */
        detenerTimer() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval)
                this.timerInterval = null
            }
            this.tiempoRestante = null
        },

        /**
         * Manejar cuando se agota el tiempo
         */
        async tiempoAgotado() {
            if (this.evaluacionActiva && !this.resolviendoEvaluacion) {
                // Enviar con respuestas vacías o las que tenga marcadas
                const respuestas = {}
                await this.resolverEvaluacion(this.evaluacionActiva.id, respuestas, true)
            }
        },

        /**
         * Resolver/enviar evaluación
         */
        async resolverEvaluacion(evaluacionId, respuestas, tiempoAgotado = false) {
            this.resolviendoEvaluacion = true
            this.detenerTimer()

            try {
                const response = await api.post(`/estudiante/evaluaciones/${evaluacionId}/resolver`, {
                    respuestas: respuestas
                })

                // Actualizar evaluación como completada en el estado local
                const evaluacionIndex = this.evaluacionesCurso.findIndex(e => e.id === evaluacionId)
                if (evaluacionIndex !== -1) {
                    this.evaluacionesCurso[evaluacionIndex].resultado_estudiante = {
                        completada: true,
                        puntaje: response.data.resultado.puntaje,
                        fecha: response.data.resultado.fecha
                    }
                }

                // Limpiar evaluación activa
                this.evaluacionActiva = null

                return {
                    ...response.data,
                    tiempoAgotado: tiempoAgotado
                }
            } catch (error) {
                console.error('Error al resolver evaluación:', error)
                throw error
            } finally {
                this.resolviendoEvaluacion = false
            }
        },

        /**
         * Limpiar estado de evaluación activa
         */
        limpiarEvaluacionActiva() {
            this.detenerTimer()
            this.evaluacionActiva = null
        },

        /**
         * Resetear store
         */
        resetearStore() {
            this.detenerTimer()
            this.evaluacionesCurso = []
            this.evaluacionActiva = null
            this.historialEvaluaciones = []
            this.cargandoEvaluaciones = false
            this.cargandoHistorial = false
            this.resolviendoEvaluacion = false
        }
    }
})
