import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user') || 'null'),
        token: localStorage.getItem('auth_token') || null,
        isAuthenticated: false,
        loading: false,
        error: null
    }),

    getters: {
        isProfesor: (state) => state.user?.tipo === 'profesor',
        isEstudiante: (state) => state.user?.tipo === 'estudiante',
        userName: (state) => state.user?.nombre || 'Usuario'
    },

    actions: {
        async login(credentials) {
            this.loading = true
            this.error = null

            try {
                const response = await api.post('/auth/login', credentials)
                let { user, token, tipo } = response.data

                // Aseguramos que el tipo esté dentro del objeto user
                user = { ...user, tipo }

                this.user = user
                this.token = token
                this.isAuthenticated = true

                localStorage.setItem('user', JSON.stringify(user))
                localStorage.setItem('auth_token', token)

                return { success: true, user }
            } catch (error) {
                this.error = error.response?.data?.message || 'Error de autenticación'
                return { success: false, error: this.error }
            } finally {
                this.loading = false
            }
        },

        async logout() {
            try {
                await api.post('/auth/logout')
            } catch (error) {
                // Error silencioso en logout
            } finally {
                this.user = null
                this.token = null
                this.isAuthenticated = false

                localStorage.removeItem('user')
                localStorage.removeItem('auth_token')
            }
        },

        async fetchUser() {
            if (!this.token) return false

            try {
                const response = await api.get('/user')
                this.user = response.data
                this.isAuthenticated = true
                localStorage.setItem('user', JSON.stringify(response.data))
                return true
            } catch (error) {
                // Solo hacer logout si es un error 401 (token inválido)
                if (error.response?.status === 401) {
                    this.logout()
                    return false
                }
                // Para otros errores (red, servidor), mantener la sesión
                return true
            }
        },

        initializeAuth() {
            const token = localStorage.getItem('auth_token')
            const userJson = localStorage.getItem('user')

            if (token && userJson) {
                try {
                    this.token = token
                    this.user = JSON.parse(userJson)
                    this.isAuthenticated = true
                } catch (error) {
                    this.logout()
                }
            }
        }
    }
})
