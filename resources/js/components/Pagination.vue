<script setup lang="ts">
interface Props {
    currentPage: number;
    lastPage: number;
    links?: any[];
    onPageChange?: (page: number) => void;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'page-change': [page: number];
}>();

function goToPage(page: number, event?: Event) {
    if (event) {
        event.preventDefault();
    }
    
    if (page < 1 || page > props.lastPage || page === props.currentPage) {
        return;
    }
    
    if (props.onPageChange) {
        props.onPageChange(page);
    } else {
        emit('page-change', page);
    }
}
</script>
<template>
    <nav v-if="lastPage > 1" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6" aria-label="Pagination">
        <div class="hidden sm:block">
            <p class="text-sm text-gray-700">
                Showing page
                <span class="font-medium">{{ currentPage }}</span>
                of
                <span class="font-medium">{{ lastPage }}</span>
            </p>
        </div>
        <div class="flex flex-1 justify-between sm:justify-end">
            <button
                type="button"
                @click="goToPage(currentPage - 1, $event)"
                :disabled="currentPage === 1"
                :class="[
                    'relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
                    currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                ]"
            >
                Previous
            </button>
            <button
                type="button"
                @click="goToPage(currentPage + 1, $event)"
                :disabled="currentPage === lastPage"
                :class="[
                    'relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
                    currentPage === lastPage ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                ]"
            >
                Next
            </button>
        </div>
    </nav>
</template>

