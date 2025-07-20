import { createRouter, createWebHistory } from 'vue-router';

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
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
