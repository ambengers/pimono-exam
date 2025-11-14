<script setup lang="ts">
import { ref, computed, watch, onUnmounted } from 'vue';

interface Option {
    [key: string]: any;
}

interface Props {
    modelValue: string | number | null;
    options?: Option[];
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
    asyncSearch?: (query: string) => Promise<Option[]>;
    debounceMs?: number;
}

const props = withDefaults(defineProps<Props>(), {
    options: () => [],
    placeholder: 'Select an option',
    searchPlaceholder: 'Search...',
    noResultsText: 'No results found',
    optionLabel: 'label',
    optionValue: 'value',
    required: false,
    disabled: false,
    hideLabel: false,
    debounceMs: 300,
});

const emit = defineEmits<{
    'update:modelValue': [value: string | number | null];
    change: [value: string | number | null];
}>();

const isOpen = ref(false);
const searchQuery = ref('');
const dropdownRef = ref<HTMLElement | null>(null);
const searchInputRef = ref<HTMLInputElement | null>(null);
const isLoading = ref(false);
const asyncOptions = ref<Option[]>([]);
const selectedOptionData = ref<Option | null>(null);
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const selectedOption = computed(() => {
    if (props.modelValue === null || props.modelValue === undefined) {
        return null;
    }
    
    // If we have stored selected option data, use it
    if (selectedOptionData.value && getOptionValue(selectedOptionData.value) === props.modelValue) {
        return selectedOptionData.value;
    }
    
    // Otherwise, try to find it in current options
    const allOptions = props.asyncSearch ? asyncOptions.value : props.options;
    const found = allOptions.find(option => getOptionValue(option) === props.modelValue);
    
    // Store it if found
    if (found) {
        selectedOptionData.value = found;
    }
    
    return found || selectedOptionData.value || null;
});

const filteredOptions = computed(() => {
    if (props.asyncSearch) {
        return asyncOptions.value;
    }
    
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
    selectedOptionData.value = option; // Store the selected option data
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
    if (props.asyncSearch) {
        // Only clear asyncOptions if no option is selected
        // This allows the selected option to remain visible
        if (!selectedOptionData.value) {
            asyncOptions.value = [];
        }
    }
}

function handleClickOutside(event: MouseEvent) {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        closeDropdown();
    }
}

async function performSearch(query: string) {
    if (!props.asyncSearch || !query.trim()) {
        asyncOptions.value = [];
        isLoading.value = false;
        return;
    }

    isLoading.value = true;
    try {
        const results = await props.asyncSearch(query);
        asyncOptions.value = results;
    } catch (error) {
        console.error('Search error:', error);
        asyncOptions.value = [];
    } finally {
        isLoading.value = false;
    }
}

function debouncedSearch(query: string) {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    if (!query.trim()) {
        asyncOptions.value = [];
        isLoading.value = false;
        return;
    }

    // Clear previous results immediately when starting a new search
    asyncOptions.value = [];
    isLoading.value = true;
    debounceTimer = setTimeout(() => {
        performSearch(query);
    }, props.debounceMs);
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

watch(() => props.modelValue, (newValue) => {
    // Clear selectedOptionData if modelValue is reset to null
    if (newValue === null || newValue === undefined) {
        selectedOptionData.value = null;
    }
});

watch(searchQuery, (newQuery) => {
    if (props.asyncSearch) {
        debouncedSearch(newQuery);
    }
});

watch(isOpen, (newValue) => {
    if (newValue) {
        document.addEventListener('click', handleClickOutside);
    } else {
        document.removeEventListener('click', handleClickOutside);
        if (debounceTimer) {
            clearTimeout(debounceTimer);
            debounceTimer = null;
        }
        isLoading.value = false;
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
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
                            v-if="isLoading"
                            class="px-4 py-8 flex items-center justify-center"
                        >
                            <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </li>
                        <li
                            v-else-if="!isLoading && filteredOptions.length === 0 && searchQuery"
                            class="px-4 py-2 text-sm text-gray-500 text-center"
                        >
                            {{ noResultsText }}
                        </li>
                        <li
                            v-else-if="!isLoading && filteredOptions.length === 0 && !searchQuery && props.asyncSearch"
                            class="px-4 py-2 text-sm text-gray-500 text-center"
                        >
                            Start typing to search...
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
