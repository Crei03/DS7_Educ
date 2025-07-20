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
                console.error('Error al cerrar sesión:', error)
            } finally {
                this.user = null
                this.token = null
                this.isAuthenticated = false

                localStorage.removeItem('user')
                localStorage.removeItem('auth_token')
            }
        },

        async fetchUser() {
            if (!this.token) return

            try {
                const response = await api.get('/user')
                this.user = response.data
                this.isAuthenticated = true
                localStorage.setItem('user', JSON.stringify(response.data))
            } catch (error) {
                this.logout()
            }
        },

        initializeAuth() {
            if (this.token && this.user) {
                this.isAuthenticated = true
            }
        }
    }
})
