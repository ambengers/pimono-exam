import { ref } from 'vue';
import type { Toast } from '@components/ToastMessage.vue';

// Shared state across all useToast instances
const toasts = ref<Toast[]>([]);

let toastIdCounter = 0;

function generateId(): string {
    return `toast-${++toastIdCounter}-${Date.now()}`;
}

export function useToast() {
    function show(message: string, type: Toast['type'] = 'info', duration?: number): string {
        const id = generateId();
        const toast: Toast = {
            id,
            message,
            type,
            duration,
        };
        
        toasts.value.push(toast);
        return id;
    }

    function success(message: string, duration?: number): string {
        return show(message, 'success', duration);
    }

    function error(message: string, duration?: number): string {
        return show(message, 'error', duration);
    }

    function warning(message: string, duration?: number): string {
        return show(message, 'warning', duration);
    }

    function info(message: string, duration?: number): string {
        return show(message, 'info', duration);
    }

    function remove(id: string): void {
        const index = toasts.value.findIndex(toast => toast.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    }

    function clear(): void {
        toasts.value = [];
    }

    return {
        toasts,
        show,
        success,
        error,
        warning,
        info,
        remove,
        clear,
    };
}

