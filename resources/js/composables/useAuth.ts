import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '../stores/auth';

export function useAuth() {
    const authStore = useAuthStore();
    
    const { user, loading, error } = storeToRefs(authStore);
    
    // Computed property for authentication status
    const isAuthenticated = computed(() => user.value !== null);

    return {
        // State (refs that Vue templates automatically unwrap)
        user,
        loading,
        error,
        isAuthenticated,

        // Actions
        login: authStore.login,
        register: authStore.register,
        logout: authStore.logout,
        fetchUser: authStore.fetchUser,
    };
}

