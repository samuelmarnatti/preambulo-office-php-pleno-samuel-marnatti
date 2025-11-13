import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [{
        path: '/login',
        name: 'Login',
        component: () =>
            import ('@/views/Login/index.vue'),
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'Register',
        component: () =>
            import ('@/views/Register/index.vue'),
        meta: { guest: true },
    },
    {
        path: '/',
        name: 'Home',
        component: () =>
            import ('@/views/Home/index.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/filmes',
        name: 'Filmes',
        component: () =>
            import ('@/views/Filmes/index.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/filmes/:id/detalhes',
        name: 'FilmesDetalhes',
        component: () =>
            import ('@/views/Filmes/detalhes.vue'),
        meta: { requiresAuth: true, role: 'cliente' },
    },
    {
        path: '/filmes/novo',
        name: 'FilmesNovo',
        component: () =>
            import ('@/views/Filmes/form.vue'),
        meta: { requiresAuth: true, role: 'atendente' },
    },
    {
        path: '/filmes/:id/editar',
        name: 'FilmesEditar',
        component: () =>
            import ('@/views/Filmes/form.vue'),
        meta: { requiresAuth: true, role: 'atendente' },
    },
    {
        path: '/minhas-locacoes',
        name: 'MinhasLocacoes',
        component: () =>
            import ('@/views/MinhasLocacoes/index.vue'),
        meta: { requiresAuth: true, role: 'cliente' },
    },
    {
        path: '/admin/dashboard',
        name: 'AdminDashboard',
        component: () =>
            import ('@/views/admin/Dashboard/index.vue'),
        meta: { requiresAuth: true, role: 'administrador' },
    },
    {
        path: '/usuarios',
        name: 'Usuarios',
        component: () =>
            import ('@/views/Usuarios/index.vue'),
        meta: { requiresAuth: true, role: 'atendente' },
    },
    {
        path: '/usuarios/novo',
        name: 'UsuariosNovo',
        component: () =>
            import ('@/views/Usuarios/form.vue'),
        meta: { requiresAuth: true, role: 'atendente' },
    },
    {
        path: '/usuarios/:id/editar',
        name: 'UsuariosEditar',
        component: () =>
            import ('@/views/Usuarios/form.vue'),
        meta: { requiresAuth: true, role: 'atendente' },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Guard de navegação
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'Login' });
    } else if (to.meta.guest && isAuthenticated) {
        next({ name: 'Home' });
    } else if (to.meta.role && authStore.user?.role !== to.meta.role) {
        next({ name: 'Home' });
    } else {
        next();
    }
});

export default router;