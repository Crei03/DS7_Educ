import { defineStore } from 'pinia'
import api from '@/services/api'

export const useCursosStore = defineStore('cursos', {
    state: () => ({
        cursos: [],
        cursosProfesor: [],
        cursoActual: null,
        loading: false,
        error: null
    }),

    getters: {
        cursosActivos: (state) => state.cursosProfesor.filter(curso => curso.estado === 'activo'),
        totalCursosActivos: (state) => state.cursosProfesor.filter(curso => curso.estado === 'activo').length,
        totalEstudiantes: (state) => state.cursosProfesor.reduce((total, curso) => total + (curso.estudiantes_count || 0), 0)
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
        }
    }
})
