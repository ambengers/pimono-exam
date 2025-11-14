import {computed, onBeforeMount} from 'vue';
import {useAuthStore} from '@stores/auth';

export function useAuth() {
    const auth = useAuthStore();

    const isLoggedIn = computed(() => auth.isLoggedIn);

    onBeforeMount (async () => {
        if (!auth.user) {
            await auth.loadAuth();
        }
    });

    async function login() {
        await auth.loadAuth();
    }

    return { auth, login };
}