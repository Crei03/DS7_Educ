import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
    // Ejemplo de ruta inicial
    // {
    //     path: '/',
    //     name: 'Home',
    //     component: () => import('../views/HomeView.vue'),
    // },
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/LoginView.vue'),
    },
    // Rutas del profesor
    {
        path: '/profesor',
        component: () => import('../components/profesor/ProfesorLayout.vue'),
        children: [
            {
                path: '',
                redirect: '/profesor/dashboard'
            },
            {
                path: 'dashboard',
                name: 'ProfesorDashboard',
                component: () => import('../views/profesor/DashboardView.vue'),
            },
            {
                path: 'cursos',
                name: 'ProfesorCursos',
                component: () => import('../views/profesor/CursosView.vue'),
            },
            {
                path: 'crear-curso',
                name: 'ProfesorCrearCurso',
                component: () => import('../views/profesor/CrearCursoView.vue'),
            },
            {
                path: 'materiales',
                name: 'ProfesorMateriales',
                component: () => import('../views/profesor/MaterialesView.vue'),
            },
            {
                path: 'evaluaciones',
                name: 'ProfesorEvaluaciones',
                component: () => import('../views/profesor/EvaluacionesView.vue'),
            },
            {
                path: 'estudiantes',
                name: 'ProfesorEstudiantes',
                component: () => import('../views/profesor/EstudiantesView.vue'),
            },
            {
                path: 'perfil',
                name: 'ProfesorPerfil',
                component: () => import('../views/profesor/PerfilView.vue'),
            }
        ]
    },
    // Rutas del estudiante
    {
        path: '/estudiante',
        component: () => import('../components/estudiante/EstudianteLayout.vue'),
        children: [
            {
                path: '',
                redirect: '/estudiante/dashboard'
            },
            {
                path: 'dashboard',
                name: 'EstudianteDashboard',
                component: () => import('../views/estudiante/DashboardView.vue'),
            },
            {
                path: 'cursos',
                name: 'EstudianteCursos',
                component: () => import('../views/estudiante/CursosView.vue'),
            },
            {
                path: 'cursos/:id',
                name: 'EstudianteCursoDetalle',
                component: () => import('../views/estudiante/CursoDetalleView.vue'),
            },
            {
                path: 'cursos/:id/continuar',
                redirect: to => `/estudiante/cursos/${to.params.id}`
            },
            {
                path: 'explorar',
                name: 'EstudianteExplorar',
                component: () => import('../views/estudiante/ExplorarView.vue'),
            },
            {
                path: 'progreso',
                name: 'EstudianteProgreso',
                component: () => import('../views/estudiante/ProgresoView.vue'),
            },
            {
                path: 'evaluaciones',
                name: 'EstudianteEvaluaciones',
                component: () => import('../views/estudiante/EvaluacionesView.vue'),
            },
            {
                path: 'perfil',
                name: 'EstudiantePerfil',
                component: () => import('../views/estudiante/PerfilView.vue'),
            }
        ]
    },
    // Ruta de diagnóstico temporal
    {
        path: '/diagnostico',
        name: 'Diagnostico',
        component: () => import('../views/DiagnosticoEstudiante.vue'),
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Guard de navegación para rutas protegidas
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Rutas que requieren autenticación
    const rutasProtegidas = ['/profesor', '/estudiante'];
    const requiereAuth = rutasProtegidas.some(ruta => to.path.startsWith(ruta));

    if (requiereAuth) {
        // Inicializar autenticación si no está inicializada
        if (!authStore.isAuthenticated) {
            authStore.initializeAuth();
        }

        // Si después de inicializar no hay token o usuario, redirigir a login
        if (!authStore.token || !authStore.user) {
            next('/login');
            return;
        }

        // Verificar que el tipo de usuario coincida con la ruta
        if (to.path.startsWith('/profesor') && authStore.user.tipo !== 'profesor') {
            next('/login');
            return;
        }

        if (to.path.startsWith('/estudiante') && authStore.user.tipo !== 'estudiante') {
            next('/login');
            return;
        }
    }

    next();
});

export default router;
