<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useForms } from '@composables/useForms';
import FormInput from '@components/forms/FormInput.vue';
import DefaultButton from '@components/buttons/DefaultButton.vue';
import { useToast } from '@composables/useToast';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const toast = useToast();

const { fields, resetErrors, setErrors, getError } = useForms({
    email: '',
    password: '',
    password_confirmation: '',
    token: '',
});

const isSubmitting = ref(false);
const isSuccess = ref(false);

onMounted(() => {
    resetErrors();
    // Get token and email from route params/query
    const token = route.params.token as string;
    const email = route.query.email as string;
    
    if (token) {
        fields.value.token = token;
    }
    if (email) {
        fields.value.email = email;
    }
});

const handleResetPassword = async () => {
    if (isSubmitting.value) {
        return;
    }
    
    resetErrors();
    isSubmitting.value = true;

    try {
        await axios.post('/reset-password', {
            token: fields.value.token,
            email: fields.value.email,
            password: fields.value.password,
            password_confirmation: fields.value.password_confirmation,
        });

        isSuccess.value = true;
        toast.success('Password has been reset successfully!');
        
        // Redirect to login after 2 seconds
        setTimeout(() => {
            router.push({ name: 'login' });
        }, 2000);
    } catch (error: any) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
            setErrors(error.response.data);
        } else {
            const errorMessage = error.response?.data?.message || 'Failed to reset password. Please try again.';
            setErrors({
                errors: {
                    password: [errorMessage]
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
                        Reset your password
                    </h2>
                    <p class="text-sm text-gray-600">
                        Enter your new password below
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
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                Password reset successfully! Redirecting to login...
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="!isSuccess && (getError('email') || getError('password') || getError('password_confirmation') || getError('token'))" class="rounded-lg bg-red-50 border border-red-200 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p v-if="getError('email')" class="text-sm font-medium text-red-800">{{ getError('email') }}</p>
                            <p v-else-if="getError('password')" class="text-sm font-medium text-red-800">{{ getError('password') }}</p>
                            <p v-else-if="getError('password_confirmation')" class="text-sm font-medium text-red-800">{{ getError('password_confirmation') }}</p>
                            <p v-else-if="getError('token')" class="text-sm font-medium text-red-800">{{ getError('token') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form v-if="!isSuccess" class="mt-6 space-y-6" @submit.prevent="handleResetPassword">
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
                            :disabled="!!route.query.email"
                            :error="getError('email')"
                        />
                        <FormInput
                            id="password"
                            v-model="fields.password"
                            name="password"
                            type="password"
                            label="New Password"
                            placeholder="Enter your new password"
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
                            label="Confirm New Password"
                            placeholder="Confirm your new password"
                            autocomplete="new-password"
                            :required="true"
                            :error="getError('password_confirmation')"
                        />
                    </div>

                    <div>
                        <DefaultButton
                            type="submit"
                            :loading="isSubmitting"
                            loading-text="Resetting password..."
                            text="Reset password"
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
            </div>
        </div>
    </div>
</template>

