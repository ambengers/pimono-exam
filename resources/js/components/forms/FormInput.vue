<template>
    <div>
        <label v-if="label && !hideLabel" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <input
            :id="id"
            :name="name"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            :autocomplete="autocomplete"
            :class="inputClasses"
            @input="handleInput"
            @blur="handleBlur"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        <p v-if="hint && !error" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    modelValue: string | number;
    id?: string;
    name?: string;
    type?: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url' | 'search';
    label?: string;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
    autocomplete?: string;
    error?: string;
    hint?: string;
    hideLabel?: boolean;
    rounded?: 'none' | 'top' | 'bottom' | 'both';
}

const props = withDefaults(defineProps<Props>(), {
    type: 'text',
    required: false,
    disabled: false,
    hideLabel: false,
    rounded: 'both',
});

const emit = defineEmits<{
    'update:modelValue': [value: string | number];
    blur: [];
}>();

const inputClasses = computed(() => {
    const baseClasses = 'appearance-none relative block w-full px-3 py-2 border placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-colors';
    
    const roundedClasses = {
        none: 'rounded-none',
        top: 'rounded-t-md',
        bottom: 'rounded-b-md',
        both: 'rounded-md',
    };
    
    const borderClasses = props.error
        ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
        : 'border-gray-300';
    
    const disabledClasses = props.disabled
        ? 'bg-gray-100 cursor-not-allowed'
        : 'bg-white';
    
    return [
        baseClasses,
        roundedClasses[props.rounded],
        borderClasses,
        disabledClasses,
    ].join(' ');
});

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', target.value);
};

const handleBlur = () => {
    emit('blur');
};
</script>

