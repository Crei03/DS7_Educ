import axios from 'axios'

// Configuración base de Axios
const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
})

// Interceptor para agregar token de autenticación
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token')
        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Interceptor para manejo de errores
api.interceptors.response.use(
    (response) => response,
    (error) => {
        // Solo limpiar datos locales en 401, pero no redirigir automáticamente
        // Dejar que cada componente maneje la redirección según su contexto
        if (error.response?.status === 401) {
            // No redirigir automáticamente, dejar que el store o componente lo maneje
        }
        return Promise.reject(error)
    }
)

export default api
