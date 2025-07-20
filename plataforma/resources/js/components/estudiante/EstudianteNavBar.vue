<template>
  <nav class="estudiante-navbar">
    <div class="navbar-header">
      <h1 class="navbar-title">Portal Estudiante</h1>
    </div>
    <div class="navbar-menu">
      <ul>
        <li v-for="item in menuItems" :key="item.name">
          <router-link
            :to="item.path"
            class="navbar-link"
            :class="{ 'navbar-link-active': $route.path === item.path }"
          >
            <component :is="item.icon" class="navbar-icon" />
            {{ item.name }}
          </router-link>
        </li>
      </ul>
    </div>
    <div class="navbar-user">
      <div class="user-info">
        <div class="user-avatar">
          <UserIcon class="user-avatar-icon" />
        </div>
        <div class="user-details">
          <p class="user-name">{{ userStore.user?.nombrePrimario || 'Estudiante' }}</p>
          <p class="user-role">Estudiante</p>
        </div>
      </div>
      <button @click="logout" class="logout-btn">
        <ArrowRightOnRectangleIcon class="logout-icon" />
        Cerrar sesión
      </button>
    </div>
  </nav>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  HomeIcon,
  BookOpenIcon,
  MagnifyingGlassIcon,
  ChartBarIcon,
  ClipboardDocumentListIcon,
  UserIcon,
  ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const userStore = useAuthStore()

const menuItems = [
  {
    name: 'Dashboard',
    path: '/estudiante/dashboard',
    icon: HomeIcon
  },
  {
    name: 'Mis Cursos',
    path: '/estudiante/cursos',
    icon: BookOpenIcon
  },
  {
    name: 'Explorar',
    path: '/estudiante/explorar',
    icon: MagnifyingGlassIcon
  },
  // {
  //   name: 'Mi Progreso',
  //   path: '/estudiante/progreso',
  //   icon: ChartBarIcon
  // },
  {
    name: 'Evaluaciones',
    path: '/estudiante/evaluaciones',
    icon: ClipboardDocumentListIcon
  },
  {
    name: 'Mi Perfil',
    path: '/estudiante/perfil',
    icon: UserIcon
  }
]

const logout = async () => {
  try {
    await userStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
  }
}
</script>

<style scoped>
.estudiante-navbar {
  background: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  height: 100vh;
  width: 260px;
  position: fixed;
  left: 0;
  top: 0;
  z-index: 30;
  display: flex;
  flex-direction: column;
}

.navbar-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.navbar-title {
  font-size: 1.25rem;
  font-weight: bold;
  color: #0554f2;
  margin: 0;
}

.navbar-menu {
  flex: 1;
  padding: 1.5rem 0;
}

.navbar-menu ul {
  list-style: none;
  padding: 0 1rem;
  margin: 0;
}

.navbar-link {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  font-size: 1rem;
  font-weight: 500;
  color: #374151;
  border-radius: 0.5rem;
  margin-bottom: 0.25rem;
  text-decoration: none;
  transition: background 0.2s, color 0.2s;
}

.navbar-link:hover {
  background: #eaf1fe;
  color: #0554f2;
}

.navbar-link-active {
  background: #eaf1fe;
  color: #0554f2;
  border-right: 3px solid #0554f2;
}

.navbar-icon {
  width: 20px;
  height: 20px;
  margin-right: 12px;
  color: #0554f2;
}

.navbar-user {
  border-top: 1px solid #e5e7eb;
  padding: 1rem;
}

.user-info {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
}

.user-avatar {
  width: 32px;
  height: 32px;
  background: #0554f2;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-avatar-icon {
  width: 18px;
  height: 18px;
  color: #fff;
}

.user-details {
  margin-left: 0.75rem;
}

.user-name {
  font-size: 1rem;
  font-weight: 500;
  color: #1f2937;
  margin: 0;
}

.user-role {
  font-size: 0.85rem;
  color: #6b7280;
  margin: 0;
}

.logout-btn {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 1rem;
  font-weight: 500;
  color: #374151;
  background: none;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}

.logout-btn:hover {
  background: #ffeaea;
  color: #e11d48;
}

.logout-icon {
  width: 16px;
  height: 16px;
  margin-right: 8px;
}
</style>
