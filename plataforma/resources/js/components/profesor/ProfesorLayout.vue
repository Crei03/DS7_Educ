<template>
  <div class="profesor-layout">
    <ProfesorNavBar />
    <div class="profesor-main">
      <main class="profesor-content">
        <router-view />
      </main>
    </div>
</div>
<ProfesorFooter />
</template>

<script setup>
import ProfesorNavBar from '@/components/profesor/ProfesorNavBar.vue'
import ProfesorFooter from '@/components/profesor/ProfesorFooter.vue'
</script>

<style scoped>
.profesor-layout {
  min-height: 100vh;
  background: #f2f2f2;
  display: flex;
}
.profesor-main {
  margin-left: 260px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 100%;
}
.profesor-content {
  flex: 1;
  padding: 2rem;
}
</style>
