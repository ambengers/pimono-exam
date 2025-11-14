import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

interface User {
    id: number;
    name: string;
    email: string;
}

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const isAuthenticated = computed(() => user.value !== null);

    // Initialize CSRF token
    const initCsrfToken = () => {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        }
    };

    // Fetch current user
    const fetchUser = async () => {
        try {
            loading.value = true;
            error.value = null;
            const response = await axios.get('/api/user');
            user.value = response.data;
        } catch (err: any) {
            // Don't set error for 401/403 - user is simply not authenticated
            // This is expected behavior for unauthenticated users
            if (err.response?.status === 401 || err.response?.status === 403) {
                user.value = null;
                error.value = null;
            } else {
                error.value = err.response?.data?.message || 'Failed to fetch user';
                user.value = null;
            }
        } finally {
            loading.value = false;
        }
    };

    // Login
    const login = async (email: string, password: string) => {
        try {
            loading.value = true;
            error.value = null;
            initCsrfToken();
            
            await axios.post('/login', {
                email,
                password,
            });

            await fetchUser();
            return { success: true };
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Login failed';
            return { success: false, error: error.value };
        } finally {
            loading.value = false;
        }
    };

    // Register
    const register = async (name: string, email: string, password: string, passwordConfirmation: string) => {
        try {
            loading.value = true;
            error.value = null;
            initCsrfToken();

            await axios.post('/register', {
                name,
                email,
                password,
                password_confirmation: passwordConfirmation,
            });

            await fetchUser();
            return { success: true };
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Registration failed';
            return { success: false, error: error.value };
        } finally {
            loading.value = false;
        }
    };

    // Logout
    const logout = async () => {
        try {
            loading.value = true;
            error.value = null;
            await axios.post('/logout');
            user.value = null;
            return { success: true };
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Logout failed';
            return { success: false, error: error.value };
        } finally {
            loading.value = false;
        }
    };

    return {
        user,
        loading,
        error,
        isAuthenticated,
        fetchUser,
        login,
        register,
        logout,
    };
});

