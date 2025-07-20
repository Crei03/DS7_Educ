<template>
  <footer class="estudiante-footer">
    <div class="footer-container">
      <div class="footer-content">
        <!-- Logo y descripción -->
        <div class="footer-section">
          <div class="footer-brand">
            <AcademicCapIcon class="footer-brand-icon" />
            <h3 class="footer-brand-text">Mi Academia</h3>
          </div>
          <p class="footer-description">
            Plataforma de aprendizaje para estudiantes comprometidos con su crecimiento académico.
          </p>
        </div>

        <!-- Enlaces rápidos -->
        <div class="footer-section">
          <h4 class="footer-title">Enlaces Rápidos</h4>
          <nav class="footer-nav">
            <router-link to="/estudiante/dashboard" class="footer-link">
              Dashboard
            </router-link>
            <router-link to="/estudiante/cursos" class="footer-link">
              Mis Cursos
            </router-link>
            <router-link to="/estudiante/progreso" class="footer-link">
              Mi Progreso
            </router-link>
            <router-link to="/estudiante/evaluaciones" class="footer-link">
              Evaluaciones
            </router-link>
          </nav>
        </div>

        <!-- Soporte -->
        <div class="footer-section">
          <h4 class="footer-title">Soporte</h4>
          <nav class="footer-nav">
            <a href="#" class="footer-link">Centro de Ayuda</a>
            <a href="#" class="footer-link">Contacto</a>
            <a href="#" class="footer-link">Términos de Uso</a>
            <a href="#" class="footer-link">Política de Privacidad</a>
          </nav>
        </div>

        <!-- Información adicional -->
        <div class="footer-section">
          <h4 class="footer-title">Estadísticas</h4>
          <div class="footer-stats">
            <div class="stat-item">
              <span class="stat-number">{{ userStats.cursosMatriculados || 0 }}</span>
              <span class="stat-label">Cursos Matriculados</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">{{ userStats.certificados || 0 }}</span>
              <span class="stat-label">Certificados</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer bottom -->
      <div class="footer-bottom">
        <div class="footer-bottom-content">
          <p class="footer-copyright">
            © {{ currentYear }} Mi Academia. Todos los derechos reservados.
          </p>
          <div class="footer-social">
            <span class="social-text">Síguenos:</span>
            <div class="social-links">
              <a href="#" class="social-link" aria-label="Facebook">
                <svg class="social-icon" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </a>
              <a href="#" class="social-link" aria-label="Twitter">
                <svg class="social-icon" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, computed } from 'vue'
import { AcademicCapIcon } from '@heroicons/vue/24/outline'

const currentYear = computed(() => new Date().getFullYear())

// Estadísticas del usuario (se puede obtener de un store)
const userStats = ref({
  cursosMatriculados: 0,
  certificados: 0
})

// Aquí se pueden cargar las estadísticas reales del usuario
// desde el store o API cuando se implemente
</script>

<style scoped>
.estudiante-footer {
  background-color: #0554f2;
  color: white;
  margin-top: auto;
}

.footer-container {
  max-width: none;
  margin: 0;
  padding: 0 2rem;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  padding: 2rem 0;
}

.footer-section {
  display: flex;
  flex-direction: column;
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.footer-brand-icon {
  width: 2rem;
  height: 2rem;
  color: #63a1f2;
}

.footer-brand-text {
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0;
  color: white;
}

.footer-description {
  color: #c7d2fe;
  line-height: 1.6;
  margin: 0;
}

.footer-title {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0 0 1rem 0;
  color: white;
}

.footer-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.footer-link {
  color: #c7d2fe;
  text-decoration: none;
  transition: color 0.2s;
}

.footer-link:hover {
  color: #63a1f2;
}

.footer-stats {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  color: #63a1f2;
}

.stat-label {
  font-size: 0.875rem;
  color: #c7d2fe;
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 1.5rem 0;
}

.footer-bottom-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

@media (min-width: 768px) {
  .footer-bottom-content {
    flex-direction: row;
    justify-content: space-between;
  }
}

.footer-copyright {
  font-size: 0.875rem;
  color: #c7d2fe;
  margin: 0;
}

.footer-social {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.social-text {
  font-size: 0.875rem;
  color: #c7d2fe;
}

.social-links {
  display: flex;
  gap: 0.75rem;
}

.social-link {
  color: #c7d2fe;
  transition: color 0.2s;
}

.social-link:hover {
  color: #63a1f2;
}

.social-icon {
  width: 1.25rem;
  height: 1.25rem;
}
</style>
