import {computed, onMounted} from 'vue';
import {storeToRefs} from 'pinia';
import {useAuthStore} from '@stores/auth';
import axios from 'axios';
import router from '@/router';

export function useAuth() {
    const authStore = useAuthStore();

    // Use storeToRefs to get reactive references from the store
    const { user } = storeToRefs(authStore);
    const isLoggedIn = computed(() => authStore.isLoggedIn);

    onMounted(async () => {
        await authStore.loadAuth();
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
            .then(() => {
                authStore.user = null;

                router.push({ name: 'login' }); 
            });
    }

    return { isLoggedIn, user, login, register, logout };
}