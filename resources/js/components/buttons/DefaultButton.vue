<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :class="buttonClasses"
        @click="$emit('click', $event)"
    >
        <span v-if="loading" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loadingText }}
        </span>
        <span v-else class="flex items-center">
            <slot name="icon"></slot>
            <slot>
                <span v-if="text">{{ text }}</span>
            </slot>
        </span>
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    type?: 'button' | 'submit' | 'reset';
    size?: 'sm' | 'base' | 'lg';
    fullWidth?: boolean;
    loading?: boolean;
    loadingText?: string;
    text?: string;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'button',
    size: 'base',
    fullWidth: false,
    loading: false,
    loadingText: 'Loading...',
    text: '',
    disabled: false,
});

defineEmits<{
    click: [event: MouseEvent];
}>();

const buttonClasses = computed(() => {
    const baseClasses = 'inline-flex items-center justify-center border border-transparent rounded-lg shadow-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer transition-colors duration-200';
    
    const sizeClasses = {
        sm: 'py-2 px-3 text-sm',
        base: 'py-3 px-4 text-sm',
        lg: 'py-3 px-6 text-base',
    };
    
    const widthClass = props.fullWidth ? 'w-full' : '';
    
    return `${baseClasses} ${sizeClasses[props.size]} ${widthClass}`;
});
</script>

