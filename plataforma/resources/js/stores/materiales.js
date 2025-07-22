import { defineStore } from 'pinia'
import api from '@/services/api'

export const useMaterialesStore = defineStore('materiales', {
    state: () => ({
        materiales: [],
        materialActual: null,
        modulos: [],
        loading: false,
        error: null
    }),

    getters: {
        totalMateriales: (state) => state.materiales.length,
        materialesPorTipo: (state) => {
            return state.materiales.reduce((acc, material) => {
                const tipo = material.tipo || 'documento'
                acc[tipo] = (acc[tipo] || 0) + 1
                return acc
            }, {})
        }
    },

    actions: {
        async fetchMaterialesProfesor(profesorId) {
            this.loading = true
            this.error = null

            try {
                const response = await api.get(`/profesores/${profesorId}/materiales`)
                // La API retorna { data: { materiales: [...] } } o { materiales: [...] }
                this.materiales = response.data.materiales || response.data.data?.materiales || []
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener materiales del profesor'
                console.error('Error al obtener materiales del profesor:', error)
            } finally {
                this.loading = false
            }
        },
        async fetchMateriales() {
            this.loading = true
            this.error = null

            try {
                const response = await api.get('/materiales')
                this.materiales = response.data.materiales || response.data.data?.materiales || []
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener materiales'
                console.error('Error al obtener materiales:', error)
            } finally {
                this.loading = false
            }
        },

        async createMaterial(materialData) {
            this.loading = true
            this.error = null

            try {
                const formData = new FormData()
                Object.keys(materialData).forEach(key => {
                    if (materialData[key] instanceof File) {
                        formData.append(key, materialData[key])
                    } else {
                        formData.append(key, materialData[key])
                    }
                })

                const response = await api.post('/materiales', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })

                const nuevoMaterial = response.data.materiales
                this.materiales.push(nuevoMaterial)

                return { success: true, material: nuevoMaterial }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al crear material'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async updateMaterial(materialId, materialData) {
            this.loading = true
            this.error = null

            try {
                const response = await api.put(`/materiales/${materialId}`, materialData)
                const materialActualizado = response.data.materiales

                const index = this.materiales.findIndex(material => material && material.id === materialId)
                if (index !== -1 && materialActualizado) {
                    this.materiales[index] = materialActualizado
                } else if (materialActualizado) {
                    // Si no se encuentra el índice, agregarlo al array
                    this.materiales.push(materialActualizado)
                }

                return { success: true, material: materialActualizado }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar material'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async deleteMaterial(materialId) {
            this.loading = true
            this.error = null

            try {
                await api.delete(`/materiales/${materialId}`)
                this.materiales = this.materiales.filter(material => material.id !== materialId)
                return { success: true }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al eliminar material'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async downloadMaterial(materialId) {
            try {
                const response = await api.get(`/materiales/${materialId}/download`, {
                    responseType: 'blob'
                })

                const url = window.URL.createObjectURL(new Blob([response.data]))
                const link = document.createElement('a')
                link.href = url
                link.download = response.headers['content-disposition']?.split('filename=')[1] || 'archivo'
                document.body.appendChild(link)
                link.click()
                document.body.removeChild(link)
                window.URL.revokeObjectURL(url)

                return { success: true }
            } catch (error) {
                console.error('Error al descargar material:', error)
                return { success: false, error: 'Error al descargar el archivo' }
            }
        },

        async fetchModulosProfesor(profesorId) {
            this.loading = true
            this.error = null

            try {
                const response = await api.get(`/profesores/${profesorId}/modulos`)
                this.modulos = response.data.data?.modulos || response.data.modulos || []
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al obtener módulos del profesor'
                console.error('Error al obtener módulos del profesor:', error)
            } finally {
                this.loading = false
            }
        }
    }
})
