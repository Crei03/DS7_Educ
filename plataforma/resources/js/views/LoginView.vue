<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1 class="login-title">Iniciar Sesión</h1>
        <p class="login-subtitle">Accede a la plataforma académica</p>
      </div>

      <form @submit.prevent="login" class="login-form">
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-input"
            placeholder="tu@email.com"
            required
          />
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Contraseña</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="form-input"
            placeholder="Tu contraseña"
            required
          />
        </div>

        <div v-if="error" class="form-error">
          <p>{{ error }}</p>
        </div>

        <button
          type="submit"
          class="btn-login"
          :disabled="loading"
        >
          <span v-if="loading">Iniciando sesión...</span>
          <span v-else>Iniciar Sesión</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const error = ref(null)

const form = reactive({
  email: '',
  password: ''
})

const login = async () => {
  loading.value = true
  error.value = null

  try {
    const resultado = await authStore.login(form)
    
    if (resultado.success) {
      // Depuración: mostrar resultado y tipo de usuario
      console.log('Login exitoso:', resultado)
      // Redirigir según el tipo de usuario
      if (resultado.user && resultado.user.tipo === 'profesor') {
        console.log('Redirigiendo a /profesor/dashboard')
        router.push('/profesor/dashboard')
      } else if (resultado.user && resultado.user.tipo === 'estudiante') {
        console.log('Redirigiendo a /estudiante/dashboard')
        router.push('/estudiante/dashboard')
      } else {
        console.log('Redirigiendo a /')
        router.push('/')
      }
    } else {
      error.value = resultado.error
      console.log('Error en login:', resultado.error)
    }
  } catch (err) {
    error.value = 'Error inesperado al iniciar sesión'
    console.error('Error de login:', err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f2f2f2;
  padding: 2rem;
}

.login-card {
  background: #fff;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 400px;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-title {
  font-size: 1.75rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.login-subtitle {
  color: #6b7280;
  font-size: 1rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #0554f2;
}

.form-error {
  background: #fef2f2;
  color: #dc2626;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid #fecaca;
  font-size: 0.9rem;
}

.btn-login {
  width: 100%;
  background: #0554f2;
  color: #fff;
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-login:hover:not(:disabled) {
  background: #0540f2;
}

.btn-login:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}
</style>
