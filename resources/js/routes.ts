const routes = [
    // Errors...
    {
        path: '/',
        redirect: '/login', // Will be overridden by navigation guard based on auth state
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('./pages/Login.vue'),
        meta: { requiresAuth: false },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('./pages/Register.vue'),
        meta: { requiresAuth: false },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('./pages/Dashboard.vue'),
        meta: { requiresAuth: true },
    },
]

export default routes