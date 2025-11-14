<script setup lang="ts">
import { useAuth } from '@composables/useAuth';
import LogoutIcon from '@components/icons/LogoutIcon.vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const auth = useAuth();
const { user } = auth;

function handleLogout() {
    auth.logout();

    router.push({ name: 'login' });
};
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
                <div class="bg-white rounded-lg shadow p-6 max-w-md">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">User Information</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Name:</dt>
                            <dd class="text-gray-900 font-medium">{{ user?.name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Email:</dt>
                            <dd class="text-gray-900 font-medium">{{ user?.email }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </main>
    </div>
</template>

