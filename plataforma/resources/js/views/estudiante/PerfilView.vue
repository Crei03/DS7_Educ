<template>
  <div class="perfil-view">
    <div class="page-header">
      <h1 class="page-title">Mi Perfil</h1>
      <p class="page-subtitle">
        Gestiona tu información personal y preferencias de aprendizaje.
      </p>
    </div>

    <div class="placeholder-content">
      <div class="placeholder-icon">
        <UserIcon class="w-16 h-16" />
      </div>
      <h2 class="placeholder-title">Vista en Desarrollo</h2>
      <p class="placeholder-text">
        Aquí podrás editar tu perfil y configurar tus preferencias.
      </p>
      
      <router-link to="/estudiante/dashboard" class="btn-primary">
        Volver al Dashboard
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { UserIcon } from '@heroicons/vue/24/outline'
</script>

<style scoped>
.perfil-view {
  max-width: 800px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: bold;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.page-subtitle {
  font-size: 1.1rem;
  color: #6b7280;
  margin: 0;
}

.placeholder-content {
  background: white;
  border-radius: 12px;
  padding: 3rem;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.placeholder-icon {
  color: #0554f2;
  margin-bottom: 1.5rem;
}

.placeholder-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 1rem 0;
}

.placeholder-text {
  color: #6b7280;
  margin: 0 0 2rem 0;
}

.btn-primary {
  background: #0554f2;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary:hover {
  background: #0540f2;
}

.w-16 { width: 4rem; }
.h-16 { height: 4rem; }
</style>
