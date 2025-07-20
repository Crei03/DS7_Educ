<template>
  <div class="estudiante-layout">
    <EstudianteNavBar />
    <div class="estudiante-main">
      <main class="estudiante-content">
        <router-view />
      </main>
    </div>
    <!-- <EstudianteFooter /> -->
</div>
</template>

<script setup>
import EstudianteNavBar from '@/components/estudiante/EstudianteNavBar.vue'
import EstudianteFooter from '@/components/estudiante/EstudianteFooter.vue'
</script>

<style scoped>
.estudiante-layout {
  min-height: 100vh;
  background: #f2f2f2;
  display: flex;
}

.estudiante-main {
  margin-left: 260px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 100%;
}

.estudiante-content {
  flex: 1;
  padding: 2rem;
}

/* Responsive design */
@media (max-width: 768px) {
  .estudiante-content {
    padding: 1rem;
  }
}
</style>
