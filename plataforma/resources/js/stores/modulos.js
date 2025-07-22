import { defineStore } from 'pinia'
import api from '@/services/api'

export const useModulosStore = defineStore('modulos', {
    state: () => ({
        modulos: [],
        moduloActual: null,
        loading: false,
        error: null
    }),
    actions: {
        // GET /modulos - Listar módulos
        async fetchModulos(profesorId) {
            this.loading = true
            this.error = null
            try {
                const res = await api.get(`/profesores/${profesorId}/modulos`)
                // La respuesta correcta: { data: { profesor: {...}, modulos: [...] } }
                this.modulos = (res.data.data && Array.isArray(res.data.data.modulos)) ? res.data.data.modulos : []
            } catch (err) {
                this.error = 'Error al cargar módulos'
                this.modulos = []
            } finally {
                this.loading = false
            }
        },

        // GET /modulos/{id} - Ver detalles y materiales
        async fetchModulo(id) {
            this.loading = true
            this.error = null
            try {
                const res = await api.get(`/modulos/${id}`)
                this.moduloActual = res.data.data
                return res.data.data
            } catch (err) {
                this.error = 'Error al cargar el módulo'
                this.moduloActual = null
                throw err
            } finally {
                this.loading = false
            }
        },

        // POST /modulos - Crear módulo
        async crearModulo(moduloData) {
            this.loading = true
            this.error = null
            try {
                const res = await api.post('/modulos', moduloData)
                // Puede venir como objeto directo o como { data: {...} }
                const nuevoModulo = res.data?.id ? res.data : (res.data?.data?.id ? res.data.data : null)
                if (nuevoModulo && nuevoModulo.id) {
                    this.modulos.push(nuevoModulo)
                    return nuevoModulo
                } else {
                    throw new Error('Respuesta inesperada al crear módulo')
                }
            } catch (err) {
                this.error = 'Error al crear el módulo'
                throw err
            } finally {
                this.loading = false
            }
        },

        // PUT /modulos/{id} - Actualizar módulo
        async actualizarModulo(id, moduloData) {
            this.loading = true
            this.error = null
            try {
                const res = await api.put(`/modulos/${id}`, moduloData)
                // Puede venir como objeto directo o como { data: {...} }
                const moduloActualizado = res.data?.id ? res.data : (res.data?.data?.id ? res.data.data : null)
                if (moduloActualizado && moduloActualizado.id) {
                    // Actualizar en la lista
                    const index = this.modulos.findIndex(m => m.id === id)
                    if (index !== -1) {
                        this.modulos[index] = moduloActualizado
                    }
                    // Actualizar módulo actual si coincide
                    if (this.moduloActual?.id === id) {
                        this.moduloActual = moduloActualizado
                    }
                    return moduloActualizado
                } else {
                    throw new Error('Respuesta inesperada al actualizar módulo')
                }
            } catch (err) {
                this.error = 'Error al actualizar el módulo'
                throw err
            } finally {
                this.loading = false
            }
        },

        // DELETE /modulos/{id} - Eliminar módulo
        async eliminarModulo(id) {
            this.loading = true
            this.error = null
            try {
                await api.delete(`/modulos/${id}`)

                // Remover de la lista
                this.modulos = this.modulos.filter(m => m.id !== id)

                // Limpiar módulo actual si es el mismo
                if (this.moduloActual?.id === id) {
                    this.moduloActual = null
                }

                return true
            } catch (err) {
                this.error = 'Error al eliminar el módulo'
                throw err
            } finally {
                this.loading = false
            }
        },

        // POST /modulos/reordenar - Reordenar módulos (endpoint específico)
        async reordenarModulos(modulosOrdenados) {
            this.loading = true
            this.error = null
            try {
                const res = await api.post('/modulos/reordenar', { modulos: modulosOrdenados })
                // Actualizar el orden local
                this.modulos = modulosOrdenados
                return res.data
            } catch (err) {
                this.error = 'Error al reordenar los módulos'
                throw err
            } finally {
                this.loading = false
            }
        },

        // Obtener módulos por curso
        async fetchModulosPorCurso(cursoId) {
            this.loading = true
            this.error = null
            try {
                const res = await api.get(`/cursos/${cursoId}/modulos`)
                const modulosCurso = res.data.data || []
                // Opcional: actualizar la lista completa o mantener separado
                return modulosCurso
            } catch (err) {
                this.error = 'Error al cargar módulos del curso'
                throw err
            } finally {
                this.loading = false
            }
        },

        // Limpiar estado
        clearError() {
            this.error = null
        },

        clearModuloActual() {
            this.moduloActual = null
        }
    }
})
