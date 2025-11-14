<script setup lang="ts">
import { ref } from 'vue';
import { useAuth } from '@composables/useAuth';
import LogoutIcon from '@components/icons/LogoutIcon.vue';
import { useRouter } from 'vue-router';
import DefaultButton from '@components/buttons/DefaultButton.vue';
import Modal from '@components/Modal.vue';

const router = useRouter();
const auth = useAuth();
const { user } = auth;

const isTransactionModalOpen = ref(false);

function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
}

function handleLogout() {
    auth.logout();

    router.push({ name: 'login' });
}

function handleMakeTransaction() {
    isTransactionModalOpen.value = true;
}

function handleCloseModal() {
    isTransactionModalOpen.value = false;
}
</script>
<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button
                            @click="handleLogout"
                            class="inline-flex items-center space-x-2 text-sm font-medium text-gray-600 hover:cursor-pointer hover:text-gray-900 focus:outline-none transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <LogoutIcon />
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-2xl">
                    <!-- Balance Section -->
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-200 text-sm font-medium mb-1">Account Balance</p>
                                <p class="text-white text-3xl font-bold">
                                    {{ formatCurrency(user?.balance ?? 0) }}
                                </p>
                            </div>
                            <div class="bg-white/20 rounded-full p-4">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information Section -->
                    <div class="px-6 py-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Account Information
                        </h3>
                        <dl class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    ID
                                </dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ user?.id }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Name
                                </dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ user?.name }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Email
                                </dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ user?.email }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Action Button Section -->
                    <div class="px-6 py-6 bg-gray-50 border-t border-gray-200">
                        <DefaultButton
                            @click="handleMakeTransaction"
                            text="Make a Transaction"
                            size="lg"
                            :full-width="true"
                        >
                            <template #icon>
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </template>
                        </DefaultButton>
                    </div>
                </div>
            </div>
        </main>

        <!-- Transaction Modal -->
        <Modal v-model="isTransactionModalOpen" @close="handleCloseModal">
            <template #title>Make a Transaction</template>
            <div>
                <p class="text-gray-600">Transaction form will go here.</p>
            </div>
        </Modal>
    </div>
</template>

