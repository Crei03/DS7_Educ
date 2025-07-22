import { defineStore } from 'pinia'
import api from '@/services/api'

export const useCursosStore = defineStore('cursos', {
    state: () => ({
        cursos: [],
        cursosProfesor: [],
        cursosMatriculados: [], // Cursos del estudiante
        cursosDisponibles: [], // Cursos disponibles para matrícula
        cursoActual: null,
        loading: false,
        error: null
    }),

    getters: {
        // Getters para profesores
        cursosActivos: (state) => state.cursosProfesor.filter(curso => curso.estado === 'activo'),
        totalCursosActivos: (state) => state.cursosProfesor.filter(curso => curso.estado === 'activo').length,
        totalEstudiantes: (state) => state.cursosProfesor.reduce((total, curso) => total + (curso.estudiantes_count || 0), 0),

        // Getters para estudiantes - filtros y cálculos
        cursosEnProgreso: (state) => state.cursosMatriculados.filter(curso => curso.progreso < 100),
        cursosCompletos: (state) => state.cursosMatriculados.filter(curso => curso.progreso >= 100),
        progresoPromedio: (state) => {
            if (state.cursosMatriculados.length === 0) return 0
            const total = state.cursosMatriculados.reduce((sum, curso) => sum + (curso.progreso || 0), 0)
            return Math.round(total / state.cursosMatriculados.length)
        },

        // Obtener curso matriculado por ID con progreso detallado
        getCursoMatriculadoById: (state) => (id) => {
            return state.cursosMatriculados.find(curso => curso.id === id)
        },

        // Estadísticas del estudiante
        estadisticasEstudiante: (state) => {
            const totalCursos = state.cursosMatriculados.length
            const cursosCompletados = state.cursosMatriculados.filter(c => c.progreso >= 100).length
            const promedioProgreso = totalCursos > 0
                ? state.cursosMatriculados.reduce((sum, c) => sum + c.progreso, 0) / totalCursos
                : 0

            return {
                totalCursos,
                cursosCompletados,
                cursosEnProgreso: totalCursos - cursosCompletados,
                promedioProgreso: Math.round(promedioProgreso)
            }
        }
    },

    actions: {
        async fetchCursosProfesor(profesorId) {
            this.loading = true
            this.error = null

            try {
                const response = await api.get(`/profesores/${profesorId}/cursos`)
                let cursos = response.data.cursos || response.data.data?.cursos || []
                // Calcula materiales_count por curso si no viene de la API
                cursos = cursos.map(curso => ({
                    ...curso,
                    materiales_count: Array.isArray(curso.materiales) ? curso.materiales.length : 0
                }))
                this.cursosProfesor = cursos
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener cursos'
                console.error('Error al obtener cursos del profesor:', error)
            } finally {
                this.loading = false
            }
        },

        async createCurso(cursoData) {
            this.loading = true
            this.error = null

            try {
                const response = await api.post('/cursos', cursoData)
                const nuevoCurso = response.data.data || response.data

                this.cursosProfesor.push(nuevoCurso)
                return { success: true, curso: nuevoCurso }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al crear curso'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async updateCurso(cursoId, cursoData) {
            this.loading = true
            this.error = null

            try {
                const response = await api.put(`/cursos/${cursoId}`, cursoData)
                const cursoActualizado = response.data.data || response.data

                const index = this.cursosProfesor.findIndex(curso => curso.id === cursoId)
                if (index !== -1) {
                    this.cursosProfesor[index] = cursoActualizado
                }

                return { success: true, curso: cursoActualizado }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar curso'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async deleteCurso(cursoId) {
            this.loading = true
            this.error = null

            try {
                await api.delete(`/cursos/${cursoId}`)
                this.cursosProfesor = this.cursosProfesor.filter(curso => curso.id !== cursoId)
                return { success: true }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al eliminar curso'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async fetchCursoDetalle(cursoId) {
            this.loading = true
            this.error = null

            try {
                const response = await api.get(`/cursos/${cursoId}`)
                this.cursoActual = response.data.data || response.data
                return this.cursoActual
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener detalle del curso'
                console.error('Error al obtener curso:', error)
            } finally {
                this.loading = false
            }
        },

        async fetchEstudiantesCurso(cursoId) {
            try {
                const response = await api.get(`/cursos/${cursoId}/estudiantes`)
                return response.data.data || response.data
            } catch (error) {
                console.error('Error al obtener estudiantes del curso:', error)
                return []
            }
        },

        async fetchCursos() {
            this.loading = true
            this.error = null

            try {
                const response = await api.get('/cursos')
                this.cursos = response.data.cursos || response.data.data?.cursos || []
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener cursos'
                console.error('Error al obtener cursos:', error)
            } finally {
                this.loading = false
            }
        },

        // Métodos para estudiantes
        async fetchCursosMatriculados(estudianteId) {
            this.loading = true
            this.error = null

            try {
                const response = await api.get(`/estudiantes/${estudianteId}/cursos`)
                let cursosData = response.data.cursos || response.data.data?.cursos || response.data || []

                // Mapear los datos del pivot para que sean accesibles directamente en el curso
                this.cursosMatriculados = cursosData.map(curso => ({
                    ...curso,
                    // Mapear datos del pivot para fácil acceso
                    progreso: parseFloat(curso.pivot?.progreso || 0),
                    fecha_matricula: curso.pivot?.fecha || null,
                    // Agregar información del profesor si está disponible
                    profesor: curso.profesor || {
                        nombreCompleto: 'Profesor no asignado',
                        nombrePrimario: 'N/A',
                        apellidoPrimario: 'N/A'
                    },
                    // Mantener pivot para referencia si es necesario
                    pivot: curso.pivot,
                    // Agregar contadores por defecto si no existen
                    modulos_count: curso.modulos_count || 0,
                    evaluaciones_count: curso.evaluaciones_count || 0,
                    // Generar fecha de última actividad simulada basada en fecha de matrícula
                    ultima_actividad: curso.pivot?.fecha || null
                }))

                console.log('Cursos procesados:', this.cursosMatriculados)
                return this.cursosMatriculados
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener cursos matriculados'
                console.error('Error al obtener cursos matriculados:', error)
                return []
            } finally {
                this.loading = false
            }
        },

        async fetchCursosDisponibles() {
            this.loading = true
            this.error = null

            try {
                const response = await api.get('/cursos/disponibles')
                this.cursosDisponibles = response.data.cursos || response.data.data?.cursos || []
                return this.cursosDisponibles
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener cursos disponibles'
                console.error('Error al obtener cursos disponibles:', error)
                return []
            } finally {
                this.loading = false
            }
        },

        async matricularseEnCurso(cursoId) {
            this.loading = true
            this.error = null

            try {
                const response = await api.post(`/cursos/${cursoId}/matricular`)

                // Procesar respuesta exitosa
                const result = response.data
                if (result.success) {
                    const cursoMatriculado = result.curso

                    // Formatear datos para compatibilidad con el store
                    const cursoFormateado = {
                        ...cursoMatriculado,
                        progreso: cursoMatriculado.progreso || 0,
                        fecha_matricula: cursoMatriculado.fecha_matricula,
                        profesor: {
                            nombreCompleto: cursoMatriculado.profesor?.nombre_completo || 'Profesor no asignado'
                        }
                    }

                    // Actualizar la lista de cursos matriculados
                    this.cursosMatriculados.push(cursoFormateado)

                    // Remover de cursos disponibles si está ahí
                    this.cursosDisponibles = this.cursosDisponibles.filter(curso => curso.id !== cursoId)

                    return result
                } else {
                    throw new Error(result.message || 'Error al matricularse en el curso')
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message || error.message || 'Error al matricularse en el curso'
                this.error = errorMessage
                console.error('Error al matricularse:', error)
                throw new Error(errorMessage)
            } finally {
                this.loading = false
            }
        },

        // Obtener progreso detallado de un curso
        async fetchProgresoDetallado(cursoId) {
            this.loading = true
            this.error = null

            try {
                const response = await api.get(`/estudiante/cursos/${cursoId}/progreso`)

                // Actualizar el curso en cursosMatriculados con información detallada
                const cursoIndex = this.cursosMatriculados.findIndex(c => c.id === cursoId)
                if (cursoIndex !== -1) {
                    this.cursosMatriculados[cursoIndex] = {
                        ...this.cursosMatriculados[cursoIndex],
                        ...response.data,
                        modulos: response.data.modulos || []
                    }
                }

                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener progreso detallado'
                console.error('Error al obtener progreso detallado:', error)
                throw error
            } finally {
                this.loading = false
            }
        },

        // Marcar material como visto
        async marcarMaterialVisto(materialId) {
            try {
                await api.post(`/estudiante/materiales/${materialId}/marcar-visto`)

                // Actualizar estado local
                this.cursosMatriculados.forEach(curso => {
                    if (curso.modulos) {
                        curso.modulos.forEach(modulo => {
                            if (modulo.materiales) {
                                const material = modulo.materiales.find(m => m.id === materialId)
                                if (material) {
                                    material.visto = true
                                }
                            }
                        })
                    }
                })
            } catch (error) {
                console.error('Error al marcar material como visto:', error)
            }
        }
    }
})
