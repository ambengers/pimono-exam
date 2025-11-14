<script setup lang="ts">
import { computed } from 'vue';
import ToastMessage, { type Toast } from './ToastMessage.vue';

interface Props {
    toasts: Toast[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'remove-toast': [id: string];
}>();

function removeToast(id: string) {
    emit('remove-toast', id);
}
</script>
<template>
    <Teleport to="body">
        <div
            class="fixed top-4 right-4 z-50 space-y-2"
            aria-live="polite"
            aria-atomic="true"
        >
            <ToastMessage
                v-for="toast in toasts"
                :key="toast.id"
                :toast="toast"
                @close="removeToast(toast.id)"
            />
        </div>
    </Teleport>
</template>

