<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">{{ auth.user?.name }}</span>
                        <button
                            @click="handleLogout"
                            :disabled="auth.loading"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="border-4 border-dashed border-gray-200 rounded-lg p-8">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            Welcome, {{ auth.user?.name }}!
                        </h2>
                        <p class="text-gray-600 mb-4">
                            You're successfully logged in.
                        </p>
                        <div class="bg-white rounded-lg shadow p-6 max-w-md mx-auto">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">User Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Name:</dt>
                                    <dd class="text-gray-900 font-medium">{{ auth.user?.name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Email:</dt>
                                    <dd class="text-gray-900 font-medium">{{ auth.user?.email }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuth } from '@composables/useAuth';

const router = useRouter();
const auth = useAuth();

const handleLogout = async () => {
    auth.logout()
        .then((response) => {
            router.push({ name: 'login' });
        });
};
</script>

