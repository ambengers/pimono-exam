<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Welcome back
                    </h2>
                    <p class="text-sm text-gray-600">
                        Sign in to your account to continue
                    </p>
                </div>

                <div v-if="getError('email') || getError('password')" class="rounded-lg bg-red-50 border border-red-200 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p v-if="getError('email')" class="text-sm font-medium text-red-800">{{ getError('email') }}</p>
                            <p v-else-if="getError('password')" class="text-sm font-medium text-red-800">{{ getError('password') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form class="mt-6 space-y-6" @submit.prevent="handleLogin">
                    <div class="space-y-4">
                        <FormInput
                            id="email"
                            v-model="fields.email"
                            name="email"
                            type="email"
                            label="Email address"
                            placeholder="Enter your email"
                            autocomplete="email"
                            :required="true"
                            :error="getError('email')"
                        />
                        <FormInput
                            id="password"
                            v-model="fields.password"
                            name="password"
                            type="password"
                            label="Password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            :required="true"
                            :error="getError('password')"
                        />
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember-me"
                                name="remember-me"
                                type="checkbox"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                                Remember me
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                                Forgot password?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                        >
                            <SubmitSpinner
                                :loading="isSubmitting"
                                loading-text="Signing in..."
                                default-text="Sign in"
                            />
                        </button>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">New to our platform?</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <router-link
                            :to="{ name: 'register' }"
                            class="inline-flex items-center font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                        >
                            Create an account
                            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@composables/useAuth';
import { useForms } from '@composables/useForms';
import FormInput from '@components/forms/FormInput.vue';
import SubmitSpinner from '@components/SubmitSpinner.vue';

const router = useRouter();
const auth = useAuth();

const { fields, resetErrors, setErrors, getError } = useForms({
    email: '',
    password: '',
});

const isSubmitting = ref(false);

onMounted(() => {
    resetErrors();
});

const handleLogin = async () => {
    if (isSubmitting.value) {
        return;
    }
    
    resetErrors();
    isSubmitting.value = true;

    auth.login(fields.value.email, fields.value.password)
        .then((response) => {
            router.push({ name: 'dashboard' });
        })
        .catch((error) => {
            if (error.response?.status === 422 && error.response?.data?.errors) {
                setErrors(error.response.data);
            } else {
                const errorMessage = error.response?.data?.message || 'Login failed. Please try again.';
                setErrors({
                    errors: {
                        email: [errorMessage]
                    }
                });
            }
        })
        .finally(() => {
            isSubmitting.value = false;
        });
};
</script>

