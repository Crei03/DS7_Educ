import { defineStore } from 'pinia'
import api from '@/services/api'

export const useEstudiantesStore = defineStore('estudiantes', {
    state: () => ({
        progresoEstudiantes: [],
        loading: false,
        error: null
    }),
    actions: {
        async fetchProgresoEstudiantes(profesorId) {
            this.loading = true
            this.error = null
            try {
                const response = await api.get(`/profesores/${profesorId}/progreso-estudiantes`)
                // La API devuelve los cursos y estudiantes en response.data.data.cursos
                this.progresoEstudiantes = response.data.data.cursos || []
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener progreso de estudiantes'
                console.error('Error al obtener progreso de estudiantes:', error)
            } finally {
                this.loading = false
            }
        }
    }
})
