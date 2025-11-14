<script setup lang="ts">
import { ref, computed, watch, onUnmounted } from 'vue';

interface Option {
    [key: string]: any;
}

interface Props {
    modelValue: string | number | null;
    options: Option[];
    label?: string;
    placeholder?: string;
    searchPlaceholder?: string;
    noResultsText?: string;
    optionLabel?: string | ((option: Option) => string);
    optionValue?: string | ((option: Option) => string | number);
    required?: boolean;
    disabled?: boolean;
    error?: string;
    hint?: string;
    hideLabel?: boolean;
    id?: string;
    name?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Select an option',
    searchPlaceholder: 'Search...',
    noResultsText: 'No results found',
    optionLabel: 'label',
    optionValue: 'value',
    required: false,
    disabled: false,
    hideLabel: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: string | number | null];
    change: [value: string | number | null];
}>();

const isOpen = ref(false);
const searchQuery = ref('');
const dropdownRef = ref<HTMLElement | null>(null);
const searchInputRef = ref<HTMLInputElement | null>(null);

const selectedOption = computed(() => {
    if (props.modelValue === null || props.modelValue === undefined) {
        return null;
    }
    return props.options.find(option => getOptionValue(option) === props.modelValue) || null;
});

const filteredOptions = computed(() => {
    if (!searchQuery.value) {
        return props.options;
    }
    
    const query = searchQuery.value.toLowerCase();
    return props.options.filter(option => {
        const label = getOptionLabel(option).toLowerCase();
        return label.includes(query);
    });
});

function getOptionLabel(option: Option): string {
    if (typeof props.optionLabel === 'function') {
        return props.optionLabel(option);
    }
    return option[props.optionLabel] || String(option);
}

function getOptionValue(option: Option): string | number {
    if (typeof props.optionValue === 'function') {
        return props.optionValue(option);
    }
    return option[props.optionValue] || option;
}

function isSelected(option: Option): boolean {
    const value = getOptionValue(option);
    return props.modelValue === value;
}

function selectOption(option: Option) {
    const value = getOptionValue(option);
    emit('update:modelValue', value);
    emit('change', value);
    isOpen.value = false;
    searchQuery.value = '';
}

function toggleDropdown() {
    if (props.disabled) return;
    isOpen.value = !isOpen.value;
    
    if (isOpen.value && searchInputRef.value) {
        setTimeout(() => {
            searchInputRef.value?.focus();
        }, 100);
    }
}

function closeDropdown() {
    isOpen.value = false;
    searchQuery.value = '';
}

function handleClickOutside(event: MouseEvent) {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        closeDropdown();
    }
}

const inputClasses = computed(() => {
    const baseClasses = 'appearance-none relative block w-full px-3 py-2 border placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-colors rounded-md bg-white';
    
    const borderClasses = props.error
        ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
        : 'border-gray-300';
    
    const disabledClasses = props.disabled
        ? 'bg-gray-100 cursor-not-allowed'
        : '';
    
    return `${baseClasses} ${borderClasses} ${disabledClasses}`;
});

watch(isOpen, (newValue) => {
    if (newValue) {
        document.addEventListener('click', handleClickOutside);
    } else {
        document.removeEventListener('click', handleClickOutside);
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
<template>
    <div class="relative">
        <label v-if="label && !hideLabel" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <div class="relative" ref="dropdownRef">
            <!-- Input Field -->
            <div
                @click="toggleDropdown"
                :class="inputClasses"
                class="cursor-pointer"
            >
                <div class="flex items-center justify-between">
                    <span :class="{ 'text-gray-500': !selectedOption }">
                        {{ selectedOption ? getOptionLabel(selectedOption) : placeholder }}
                    </span>
                    <svg
                        class="h-5 w-5 text-gray-400 transition-transform duration-200"
                        :class="{ 'rotate-180': isOpen }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Dropdown Menu -->
            <Transition name="dropdown">
                <div
                    v-if="isOpen"
                    class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
                >
                    <!-- Search Input -->
                    <div class="p-2 border-b border-gray-200 sticky top-0 bg-white">
                        <input
                            ref="searchInputRef"
                            v-model="searchQuery"
                            type="text"
                            :placeholder="searchPlaceholder"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            @click.stop
                        />
                    </div>

                    <!-- Options List -->
                    <ul class="py-1">
                        <li
                            v-if="filteredOptions.length === 0"
                            class="px-4 py-2 text-sm text-gray-500 text-center"
                        >
                            {{ noResultsText }}
                        </li>
                        <li
                            v-for="option in filteredOptions"
                            :key="getOptionValue(option)"
                            @click="selectOption(option)"
                            :class="[
                                'px-4 py-2 text-sm cursor-pointer hover:bg-indigo-50 transition-colors',
                                isSelected(option) ? 'bg-indigo-100 text-indigo-900 font-medium' : 'text-gray-900'
                            ]"
                        >
                            {{ getOptionLabel(option) }}
                        </li>
                    </ul>
                </div>
            </Transition>
        </div>

        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        <p v-if="hint && !error" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: opacity 0.15s ease, transform 0.15s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
