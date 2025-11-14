<script setup lang="ts">
import ToastContainer from '@components/ToastContainer.vue';
import { useToast } from '@composables/useToast';

const { toasts, remove } = useToast();

function handleRemoveToast(id: string) {
    remove(id);
}
</script>
<template>
    <div id="app" class="min-h-screen bg-gray-50">
        <router-view />
        <ToastContainer :toasts="toasts" @remove-toast="handleRemoveToast" />
    </div>
</template>

