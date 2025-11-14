import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import '../css/app.css';
import { useAuth } from './composables/useAuth';

const pinia = createPinia();

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            redirect: '/login', // Will be overridden by navigation guard based on auth state
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('./pages/Login.vue'),
            meta: { requiresAuth: false, guestOnly: true },
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('./pages/Register.vue'),
            meta: { requiresAuth: false, guestOnly: true },
        },
        {
            path: '/dashboard',
            name: 'dashboard',
            component: () => import('./pages/Dashboard.vue'),
            meta: { requiresAuth: true },
        },
    ],
});

const app = createApp(App);
app.use(pinia);
app.use(router);

// Navigation guards (after Pinia is initialized)
router.beforeEach(async (to, _from, next) => {
    const auth = useAuth();
    
    // Fetch user if not already loaded
    if (!auth.user.value && !auth.isAuthenticated.value) {
        await auth.fetchUser();
    }

    // Handle root path redirect
    if (to.path === '/') {
        if (auth.isAuthenticated.value) {
            next({ name: 'dashboard' });
        } else {
            next({ name: 'login' });
        }
        return;
    }

    // Check if route requires authentication
    if (to.meta.requiresAuth && !auth.isAuthenticated.value) {
        next({ name: 'login', query: { redirect: to.fullPath } });
        return;
    }

    // Check if route is guest-only (login/register)
    if (to.meta.guestOnly && auth.isAuthenticated.value) {
        next({ name: 'dashboard' });
        return;
    }

    next();
});

// Mount the app when DOM is ready
const mountElement = document.getElementById('app');
if (mountElement) {
    app.mount('#app');
}

