const routes = [
    {
        path: '/',
        redirect: '/login', // Will be overridden by navigation guard based on auth state
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@pages/Login.vue'),
        meta: { requiresAuth: false, guestOnly: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@pages/Register.vue'),
        meta: { requiresAuth: false, guestOnly: true },
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('@pages/ForgotPassword.vue'),
        meta: { requiresAuth: false, guestOnly: true },
    },
    {
        path: '/reset-password/:token',
        name: 'reset-password',
        component: () => import('@pages/ResetPassword.vue'),
        meta: { requiresAuth: false, guestOnly: true },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@pages/Dashboard.vue'),
        meta: { requiresAuth: true },
    },
]

export default routes