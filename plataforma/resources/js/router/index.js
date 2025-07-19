import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    // Ejemplo de ruta inicial
    {
        path: '/',
        name: 'Home',
        component: () => import('../views/HomeView.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
