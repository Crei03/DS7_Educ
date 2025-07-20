<template>
  <footer class="profesor-footer">
    <div class="footer-content">
      <div class="footer-center">
        <p class="footer-copyright">
          Copyright © {{ currentYear }} Plataforma Académica
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped>
.profesor-footer {
  background: #0554f2;
  color: #fff;
  padding: 2rem 0;
  margin-left: 260px;
}
.footer-content {
  padding: 0 2rem;
}
.footer-center {
  text-align: center;
}
.footer-copyright {
  font-size: 1rem;
  color: #63a1f2;
}
</style>
