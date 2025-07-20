<template>
    <router-view />
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

onMounted(() => {
  // Solo inicializar autenticación desde localStorage
  // No verificar con el servidor en cada recarga para evitar logouts innecesarios
  authStore.initializeAuth()
})
</script>

<style>
/* Estilos globales pueden ir aquí o en main.css */
</style>
