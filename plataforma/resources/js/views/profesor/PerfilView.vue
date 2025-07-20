<template>
  <div class="perfil-container">
    <div class="perfil-header">
      <h1 class="perfil-title">Mi Perfil</h1>
      <p class="perfil-subtitle">Configura tu información personal</p>
    </div>
    
    <div class="perfil-content">
      <div class="perfil-info-card">
        <div class="perfil-avatar-section">
          <div class="perfil-avatar">
            <span class="avatar-iniciales">
              {{ getIniciales(authStore.user?.nombrePrimario, authStore.user?.apellidoPrimario) }}
            </span>
          </div>
          <div class="perfil-datos-basicos">
            <h2 class="perfil-nombre-completo">
              {{ authStore.user?.nombrePrimario }} 
              {{ authStore.user?.nombreSecundario || '' }} 
              {{ authStore.user?.apellidoPrimario }} 
              {{ authStore.user?.apellidoSecundario || '' }}
            </h2>
            <p class="perfil-tipo">{{ authStore.user?.tipo || 'Profesor' }}</p>
            <p class="perfil-email">{{ authStore.user?.correo }}</p>
          </div>
        </div>
      </div>
      
      <div class="perfil-form-card">
        <h3 class="form-title">Información Personal</h3>
        
        <form @submit.prevent="actualizarPerfil" class="perfil-form">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Primer Nombre *</label>
              <input
                v-model="form.nombrePrimario"
                type="text"
                class="form-input"
                required
              />
            </div>
            <div class="form-group">
              <label class="form-label">Segundo Nombre</label>
              <input
                v-model="form.nombreSecundario"
                type="text"
                class="form-input"
              />
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Primer Apellido *</label>
              <input
                v-model="form.apellidoPrimario"
                type="text"
                class="form-input"
                required
              />
            </div>
            <div class="form-group">
              <label class="form-label">Segundo Apellido</label>
              <input
                v-model="form.apellidoSecundario"
                type="text"
                class="form-input"
              />
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Cédula *</label>
              <input
                v-model="form.cedula"
                type="text"
                class="form-input"
                placeholder="X-XXXX-XXXXX"
                required
              />
            </div>
            <div class="form-group">
              <label class="form-label">Email *</label>
              <input
                v-model="form.correo"
                type="email"
                class="form-input"
                required
              />
            </div>
          </div>
          
          <div v-if="mensaje" :class="['form-mensaje', mensaje.tipo]">
            <p>{{ mensaje.texto }}</p>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="resetForm" class="btn-cancelar">
              Cancelar
            </button>
            <button 
              type="submit" 
              class="btn-guardar"
              :disabled="guardando"
            >
              <span v-if="guardando">Guardando...</span>
              <span v-else>Guardar cambios</span>
            </button>
          </div>
        </form>
      </div>
      
      <div class="perfil-security-card">
        <h3 class="form-title">Seguridad</h3>
        
        <!-- <div class="security-item">
          <div class="security-info">
            <h4 class="security-title">Cambiar contraseña</h4>
            <p class="security-description">Actualiza tu contraseña para mantener tu cuenta segura</p>
          </div>
          <button class="btn-security">
            Cambiar contraseña
          </button>
        </div> -->
        
        <div class="security-item">
          <div class="security-info">
            <h4 class="security-title">Cerrar sesión</h4>
            <p class="security-description">Cerrar sesión en este dispositivo</p>
          </div>
          <button @click="cerrarSesion" class="btn-logout">
            Cerrar sesión
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const guardando = ref(false)
const mensaje = ref(null)

const form = reactive({
  nombrePrimario: '',
  nombreSecundario: '',
  apellidoPrimario: '',
  apellidoSecundario: '',
  cedula: '',
  correo: ''
})

const getIniciales = (nombre, apellido) => {
  const inicial1 = nombre?.charAt(0)?.toUpperCase() || ''
  const inicial2 = apellido?.charAt(0)?.toUpperCase() || ''
  return inicial1 + inicial2
}

const cargarDatosUsuario = () => {
  if (authStore.user) {
    form.nombrePrimario = authStore.user.nombrePrimario || ''
    form.nombreSecundario = authStore.user.nombreSecundario || ''
    form.apellidoPrimario = authStore.user.apellidoPrimario || ''
    form.apellidoSecundario = authStore.user.apellidoSecundario || ''
    form.cedula = authStore.user.cedula || ''
    form.correo = authStore.user.correo || ''
  }
}

const resetForm = () => {
  cargarDatosUsuario()
  mensaje.value = null
}

const actualizarPerfil = async () => {
  guardando.value = true
  mensaje.value = null

  try {
    const id = authStore.user?.id
    const response = await api.put(`/profesor/${id}`, form)

    if (response.data.success) {
      authStore.user = { ...authStore.user, ...form }
      localStorage.setItem('user', JSON.stringify(authStore.user))
      mensaje.value = {
        tipo: 'success',
        texto: 'Perfil actualizado correctamente'
      }
    } else {
      mensaje.value = {
        tipo: 'error',
        texto: response.data.message || 'Error al actualizar el perfil'
      }
    }
  } catch (error) {
    console.error('Error actualizando perfil:', error)
    mensaje.value = {
      tipo: 'error',
      texto: 'Error inesperado al actualizar el perfil'
    }
  } finally {
    guardando.value = false
  }
}

const cerrarSesion = async () => {
  if (confirm('¿Estás seguro que deseas cerrar sesión?')) {
    await authStore.logout()
    router.push('/login')
  }
}

onMounted(() => {
  cargarDatosUsuario()
})
</script>

<style scoped>
.perfil-container {
  padding: 2rem;
}

.perfil-header {
  margin-bottom: 2rem;
}

.perfil-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.perfil-subtitle {
  color: #6b7280;
  font-size: 1.1rem;
}

.perfil-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.perfil-info-card {
  background: #fff;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.perfil-avatar-section {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.perfil-avatar {
  width: 80px;
  height: 80px;
  background: #0554f2;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.avatar-iniciales {
  color: #fff;
  font-weight: bold;
  font-size: 1.75rem;
}

.perfil-nombre-completo {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.perfil-tipo {
  background: #e0f2fe;
  color: #0c4a6e;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  display: inline-block;
  margin-bottom: 0.5rem;
  text-transform: capitalize;
}

.perfil-email {
  color: #6b7280;
  margin: 0;
}

.perfil-form-card,
.perfil-security-card {
  background: #fff;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.form-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.perfil-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input {
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

.form-mensaje {
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.9rem;
}

.form-mensaje.success {
  background: #dcfce7;
  color: #166534;
  border: 1px solid #bbf7d0;
}

.form-mensaje.error {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.btn-cancelar {
  background: #f3f4f6;
  color: #374151;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-cancelar:hover {
  background: #e5e7eb;
}

.btn-guardar {
  background: #0554f2;
  color: #fff;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-guardar:hover:not(:disabled) {
  background: #0540f2;
}

.btn-guardar:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.security-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.security-item:last-child {
  border-bottom: none;
}

.security-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
}

.security-description {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0;
}

.btn-security {
  background: #f3f4f6;
  color: #374151;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-security:hover {
  background: #e5e7eb;
}

.btn-logout {
  background: #fef2f2;
  color: #dc2626;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-logout:hover {
  background: #fee2e2;
}
</style>
