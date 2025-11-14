import { ref } from 'vue';
import type { Toast } from '@components/ToastMessage.vue';

// Shared state across all useToast instances
const toasts = ref<Toast[]>([]);

let toastIdCounter = 0;

function generateId(): string {
    return `toast-${++toastIdCounter}-${Date.now()}`;
}

type ToastInput = string | { message?: string } | Array<{ message?: string }>;

function extractMessage(input: ToastInput): string {
    if (typeof input === 'string') {
        return input;
    }
    
    if (Array.isArray(input)) {
        // Search for message in array items
        for (const item of input) {
            if (item && typeof item === 'object' && 'message' in item && typeof item.message === 'string') {
                return item.message;
            }
        }
        // If no message found, try first item if it's a string
        if (input.length > 0 && typeof input[0] === 'string') {
            return input[0];
        }
        return 'An error occurred';
    }
    
    if (input && typeof input === 'object') {
        if ('message' in input && typeof input.message === 'string') {
            return input.message;
        }
        return 'An error occurred';
    }
    
    return 'An error occurred';
}

export function useToast() {
    function show(input: ToastInput, type: Toast['type'] = 'info', duration?: number): string {
        const message = extractMessage(input);
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

    function success(input: ToastInput, duration?: number): string {
        return show(input, 'success', duration);
    }

    function error(input: ToastInput, duration?: number): string {
        return show(input, 'error', duration);
    }

    function warning(input: ToastInput, duration?: number): string {
        return show(input, 'warning', duration);
    }

    function info(input: ToastInput, duration?: number): string {
        return show(input, 'info', duration);
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

