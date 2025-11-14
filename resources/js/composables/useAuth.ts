import {computed, onBeforeMount} from 'vue';
import {useAuthStore} from '@stores/auth';
import axios from 'axios';

export function useAuth() {
    const authStore = useAuthStore();

    const isLoggedIn = computed(() => authStore.isLoggedIn);

    onBeforeMount (async () => {
        if (!authStore.user) {
            await authStore.loadAuth();
        }
    });

    async function login(email: string, password: string) {
        return axios.post('/login', {
            email: email,
            password: password,
        })
    }

    async function register(name: string, email: string, password: string, passwordConfirmation: string) {
        return axios.post('/register', {
            name: name,
            email: email,
            password: password,
            password_confirmation: passwordConfirmation,
        })
    }

    async function logout() {
        return axios.post('/logout')
    }

    return { isLoggedIn, login, register, logout };
}