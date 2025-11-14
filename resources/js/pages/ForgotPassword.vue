<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForms } from '@composables/useForms';
import FormInput from '@components/forms/FormInput.vue';
import DefaultButton from '@components/buttons/DefaultButton.vue';
import { useToast } from '@composables/useToast';
import axios from 'axios';

const toast = useToast();

const { fields, resetErrors, setErrors, getError } = useForms({
    email: '',
});

const isSubmitting = ref(false);
const isSuccess = ref(false);
const resetLink = ref<string | null>(null);

onMounted(() => {
    resetErrors();
});

const handleForgotPassword = async () => {
    if (isSubmitting.value) {
        return;
    }
    
    resetErrors();
    isSubmitting.value = true;

    try {
        const response = await axios.post('/forgot-password', {
            email: fields.value.email,
        });

        isSuccess.value = true;
        
        // If reset link is provided (for development), store it
        if (response.data.reset_link) {
            resetLink.value = response.data.reset_link;
        }
        
        toast.success('Password reset link has been sent to your email address.');
    } catch (error: any) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
            setErrors(error.response.data);
        } else {
            const errorMessage = error.response?.data?.message || 'Failed to send password reset link. Please try again.';
            setErrors({
                errors: {
                    email: [errorMessage]
                }
            });
            toast.error(errorMessage);
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Forgot your password?
                    </h2>
                    <p class="text-sm text-gray-600">
                        No worries! Enter your email address and we'll send you a link to reset your password.
                    </p>
                </div>

                <!-- Success Message -->
                <div v-if="isSuccess" class="rounded-lg bg-green-50 border border-green-200 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-green-800 mb-2">
                                Password reset link has been sent! Please check your email inbox.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="!isSuccess && getError('email')" class="rounded-lg bg-red-50 border border-red-200 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ getError('email') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form v-if="!isSuccess" class="mt-6 space-y-6" @submit.prevent="handleForgotPassword">
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
                    </div>

                    <div>
                        <DefaultButton
                            type="submit"
                            :loading="isSubmitting"
                            loading-text="Sending..."
                            text="Send reset link"
                            :full-width="true"
                        />
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Remember your password?</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <router-link
                            :to="{ name: 'login' }"
                            class="inline-flex items-center font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                        >
                            Back to sign in
                            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </router-link>
                    </div>
                </form>

                <!-- Success State -->
                <div v-if="isSuccess" class="mt-6 text-center">
                    <router-link
                        :to="{ name: 'login' }"
                        class="inline-flex items-center font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                    >
                        Back to sign in
                        <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

