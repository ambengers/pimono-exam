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
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const isSubmitting = ref(false);

// Clear any existing errors when component mounts
onMounted(() => {
    resetErrors();
});

const handleRegister = async () => {
    // Prevent double submission
    if (isSubmitting.value) {
        return;
    }
    
    resetErrors();
    isSubmitting.value = true;
    
    try {
        await auth.register(
            fields.value.name,
            fields.value.email,
            fields.value.password,
            fields.value.password_confirmation
        );
        
        router.push('/dashboard');
    } catch (error: any) {
        // Handle validation errors (422)
        if (error.response?.status === 422 && error.response?.data?.errors) {
            setErrors(error.response.data);
        } else {
            // Handle other errors
            const errorMessage = error.response?.data?.message || 'Registration failed. Please try again.';
            setErrors({
                errors: {
                    email: [errorMessage]
                }
            });
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>
<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Create your account
                    </h2>
                    <p class="text-sm text-gray-600">
                        Get started with your free account today
                    </p>
                </div>

                <div v-if="getError('name') || getError('email') || getError('password') || getError('password_confirmation')" class="rounded-lg bg-red-50 border border-red-200 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p v-if="getError('name')" class="text-sm font-medium text-red-800">{{ getError('name') }}</p>
                            <p v-else-if="getError('email')" class="text-sm font-medium text-red-800">{{ getError('email') }}</p>
                            <p v-else-if="getError('password')" class="text-sm font-medium text-red-800">{{ getError('password') }}</p>
                            <p v-else-if="getError('password_confirmation')" class="text-sm font-medium text-red-800">{{ getError('password_confirmation') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form class="mt-6 space-y-6" @submit.prevent="handleRegister">
                    <div class="space-y-4">
                        <FormInput
                            id="name"
                            v-model="fields.name"
                            name="name"
                            type="text"
                            label="Full Name"
                            placeholder="Enter your full name"
                            autocomplete="name"
                            :required="true"
                            :error="getError('name')"
                        />
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
                            placeholder="Create a password"
                            autocomplete="new-password"
                            :required="true"
                            hint="Must be at least 8 characters"
                            :error="getError('password')"
                        />
                        <FormInput
                            id="password_confirmation"
                            v-model="fields.password_confirmation"
                            name="password_confirmation"
                            type="password"
                            label="Confirm Password"
                            placeholder="Confirm your password"
                            autocomplete="new-password"
                            :required="true"
                            :error="getError('password_confirmation')"
                        />
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                        >
                            <SubmitSpinner
                                :loading="isSubmitting"
                                loading-text="Creating account..."
                                default-text="Create account"
                            />
                        </button>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Already have an account?</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <router-link
                            to="/login"
                            class="inline-flex items-center font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                        >
                            Sign in instead
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
