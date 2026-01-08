<script setup>
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Sign In" />
    
    <div class="min-h-screen flex bg-white">
        <!-- Left Side - Light Blue Branding -->
        <div class="hidden lg:flex lg:flex-1 flex-col justify-between px-16 py-20 bg-gradient-to-br from-blue-50 to-blue-100">
            <div>
                <div class="flex items-center space-x-4 mb-16">
                    <span class="text-4xl font-black text-primary-500">QuickLearn</span>
                </div>

                <h1 class="text-6xl font-black text-blue-900 mb-8 leading-tight max-w-lg">
                    Continue Your <span class="text-primary-500">Learning</span> Journey
                </h1>
                
                <p class="text-xl text-blue-700 mb-12 max-w-lg leading-relaxed font-medium">
                    Welcome back! Dive into your courses, track your progress, and achieve your learning goals with our comprehensive platform.
                </p>
                
                <div class="space-y-6">
                    <div class="flex items-start space-x-4 group">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-all">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-1">Learn at Your Pace</h3>
                            <p class="text-blue-700">24/7 access to all courses and materials</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 group">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-all">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-1">Earn Certificates</h3>
                            <p class="text-blue-700">Get recognized for your achievements</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 group">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-all">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-1">Expert Instructors</h3>
                            <p class="text-blue-700">Learn from industry professionals</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-blue-700 text-sm">
                <p>© 2025 QuickLearn. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex-1 flex items-center justify-center px-6 sm:px-10 lg:px-16 py-12">
            <div class="max-w-md w-full">
                <!-- Mobile Logo -->
                <div class="text-center lg:hidden mb-8">
                    <h2 class="text-3xl font-black text-blue-900">Sign In</h2>
                </div>

                <!-- Form Container -->
                <div class="bg-white border-2 border-blue-200 rounded-2xl p-8 shadow-lg">
                    <div class="hidden lg:block mb-8">
                        <h2 class="text-3xl font-black text-blue-900">Sign In</h2>
                        <p class="text-blue-700 mt-2">Access your learning dashboard</p>
                    </div>

                    <!-- Success Message -->
                    <div v-if="status" class="rounded-lg bg-emerald-500/20 p-4 border border-emerald-500/50 mb-6">
                        <p class="text-sm font-semibold text-emerald-600">{{ status }}</p>
                    </div>

                    <!-- Login Form -->
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-bold text-blue-900 mb-3">Email Address</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                required
                                v-model="form.email"
                                class="w-full px-4 py-3 rounded-xl border-2 border-blue-300 bg-white text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                placeholder="you@example.com"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-bold text-blue-900 mb-3">Password</label>
                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    name="password"
                                    autocomplete="current-password"
                                    required
                                    v-model="form.password"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-blue-300 bg-white text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pr-12"
                                    placeholder="Enter your password"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-600 hover:text-blue-700"
                                >
                                    <svg v-if="!showPassword" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.262l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                        <path d="M15.171 13.576l1.474 1.474a1 1 0 001.414-1.414l-14-14a1 1 0 00-1.414 1.414l1.473 1.473A10.014 10.014 0 00.458 10c1.274 4.057 5.064 7 9.542 7 .8 0 1.577-.066 2.341-.184l.824.824a1 1 0 001.414-1.414l-1.48-1.48z" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="remember"
                                v-model="form.remember"
                                class="rounded border-2 border-blue-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
                            />
                            <label for="remember" class="ml-3 text-sm font-medium text-blue-900">Remember me</label>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="!form.processing">Sign In</span>
                            <span v-else class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Signing In...
                            </span>
                        </button>

                        <!-- Links -->
                        <div class="flex items-center justify-between pt-2">
                            <Link 
                                v-if="canResetPassword" 
                                :href="route('password.request')"
                                class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                            >
                                Forgot password?
                            </Link>
                        </div>
                    </form>

                    <!-- Sign Up Link -->
                    <div class="mt-8 pt-6 border-t-2 border-blue-200 text-center">
                        <p class="text-blue-900">
                            Don't have an account?
                            <Link :href="route('register')" class="text-blue-600 font-bold hover:text-blue-700">
                                Sign up
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

