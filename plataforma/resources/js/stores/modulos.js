import { defineStore } from 'pinia'
import api from '@/services/api'

export const useModulosStore = defineStore('modulos', {
    state: () => ({
        modulos: [],
        loading: false,
        error: null
    }),
    actions: {
        async fetchModulos() {
            this.loading = true
            this.error = null
            try {
                const res = await api.get('/modulos')
                // La respuesta es paginada: { data: [...] }
                this.modulos = res.data.data || []
            } catch (err) {
                this.error = 'Error al cargar módulos'
                this.modulos = []
            } finally {
                this.loading = false
            }
        }
    }
})
