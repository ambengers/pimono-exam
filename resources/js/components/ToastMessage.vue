<script setup lang="ts">
import { computed, onMounted, onUnmounted } from 'vue';

export interface Toast {
    id: string;
    message: string;
    type: 'success' | 'error' | 'warning' | 'info';
    duration?: number;
}

interface Props {
    toast: Toast;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    close: [];
}>();

const toastClasses = computed(() => {
    const baseClasses = 'flex items-center p-4 rounded-lg shadow-lg border max-w-sm w-full';
    
    const typeClasses = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800',
    };
    
    return `${baseClasses} ${typeClasses[props.toast.type]}`;
});

const iconClasses = computed(() => {
    const baseClasses = 'h-5 w-5 flex-shrink-0';
    
    const typeClasses = {
        success: 'text-green-400',
        error: 'text-red-400',
        warning: 'text-yellow-400',
        info: 'text-blue-400',
    };
    
    return `${baseClasses} ${typeClasses[props.toast.type]}`;
});

let timeoutId: ReturnType<typeof setTimeout> | null = null;

function close() {
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
    emit('close');
}

onMounted(() => {
    const duration = props.toast.duration ?? 5000;
    if (duration > 0) {
        timeoutId = setTimeout(() => {
            close();
        }, duration);
    }
});

onUnmounted(() => {
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
});

function getIcon() {
    switch (props.toast.type) {
        case 'success':
            return 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'error':
            return 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'warning':
            return 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
        case 'info':
            return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
        default:
            return '';
    }
}
</script>
<template>
    <Transition name="toast">
        <div
            v-if="toast"
            :class="toastClasses"
            role="alert"
        >
            <div class="flex-shrink-0">
                <svg
                    :class="iconClasses"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        :d="getIcon()"
                    />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">
                    {{ toast.message }}
                </p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button
                    @click="close"
                    class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none hover:cursor-pointer transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.toast-enter-active {
    transition: all 0.3s ease-out;
}

.toast-leave-active {
    transition: all 0.2s ease-in;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

.toast-enter-to,
.toast-leave-from {
    opacity: 1;
    transform: translateX(0);
}
</style>

