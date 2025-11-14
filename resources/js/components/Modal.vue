<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';

interface Props {
    modelValue: boolean;
    closeOnBackdrop?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    closeOnBackdrop: true,
});

const emit = defineEmits<{
    'update:modelValue': [value: boolean];
    close: [];
}>();

function handleClose() {
    emit('update:modelValue', false);
    emit('close');
}

function handleBackdropClick() {
    if (props.closeOnBackdrop) {
        handleClose();
    }
}

// Close on ESC key
function handleEscape(e: KeyboardEvent) {
    if (e.key === 'Escape' && props.modelValue) {
        handleClose();
    }
}

// Add event listener for ESC key
onMounted(() => {
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleEscape);
});
</script>
<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="modelValue"
                class="fixed inset-0 z-50 overflow-y-auto"
                @click.self="handleBackdropClick"
            >
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-800/80 transition-opacity"></div>

                <!-- Modal Container -->
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative bg-white rounded-xl shadow-xl max-w-lg w-full transform transition-all"
                        @click.stop
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <slot name="title">Modal Title</slot>
                            </h3>
                            <button
                                @click="handleClose"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none transition-colors"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="px-6 py-4">
                            <slot></slot>
                        </div>

                        <!-- Footer (optional) -->
                        <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-200">
                            <slot name="footer"></slot>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
    opacity: 0;
    transform: scale(0.95);
}
</style>
