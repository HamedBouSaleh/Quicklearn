<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="QuickLearn - Learn Anything, Anytime" />

    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-blue-50">
        <!-- Navigation -->
        <nav class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200/50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-2xl font-black bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">QuickLearn</span>
                    </div>
                    <div v-if="canLogin" class="flex items-center space-x-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-6 py-2 text-slate-700 font-semibold hover:text-blue-600 transition-colors"
                            >
                                Sign In
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all hover:scale-105"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="pt-28 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <!-- Animated Background Elements -->
            <div class="absolute top-20 -right-32 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            
            <div class="max-w-5xl mx-auto text-center relative z-10">
                <div class="inline-block mb-8">
                    <div class="px-4 py-2 bg-blue-100 rounded-full border border-blue-200">
                        <p class="text-sm font-semibold text-blue-700">Trusted by 50,000+ Students Worldwide</p>
                    </div>
                </div>
                
                <h1 class="text-6xl md:text-7xl font-black text-slate-900 mb-6 leading-tight tracking-tight">
                    Transform Your <span class="bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-600 bg-clip-text text-transparent">Future Today</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-slate-600 mb-8 max-w-3xl mx-auto leading-relaxed font-medium">
                    Join thousands of professionals mastering in-demand skills. Learn from industry experts with interactive courses designed for real-world impact.
                </p>
                
                <div v-if="canRegister" class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <Link :href="route('register')" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold rounded-xl hover:shadow-2xl transition-all hover:scale-105 text-lg">
                        Start Free Today
                    </Link>
                    <Link :href="route('login')" class="px-8 py-4 bg-white border-2 border-slate-200 text-slate-900 font-bold rounded-xl hover:border-blue-600 hover:shadow-lg transition-all text-lg">
                        Sign In
                    </Link>
                </div>
            </div>

            <!-- Feature Pills -->
            <div class="mt-16 max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center space-x-3 bg-white/50 backdrop-blur p-4 rounded-lg border border-slate-200/50">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Learn at Your Pace</p>
                        <p class="text-sm text-slate-600">24/7 access to all courses</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 bg-white/50 backdrop-blur p-4 rounded-lg border border-slate-200/50">
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Earn Certificates</p>
                        <p class="text-sm text-slate-600">Recognized by employers</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 bg-white/50 backdrop-blur p-4 rounded-lg border border-slate-200/50">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Expert Instructors</p>
                        <p class="text-sm text-slate-600">Industry professionals</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-24 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-5xl font-black text-slate-900 mb-4">Why Choose QuickLearn?</h2>
                    <p class="text-xl text-slate-600 max-w-2xl mx-auto">Everything you need to succeed in your learning journey</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group bg-white p-8 rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-3">Learn at Your Pace</h3>
                        <p class="text-slate-600 leading-relaxed">Access all course materials anytime, anywhere. Pause, rewind, and repeat lessons as many times as you need.</p>
                    </div>

                    <div class="group bg-white p-8 rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-3">Earn Certificates</h3>
                        <p class="text-slate-600 leading-relaxed">Showcase your achievements with recognized certificates. Boost your resume and impress employers worldwide.</p>
                    </div>

                    <div class="group bg-white p-8 rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-3">Expert Instructors</h3>
                        <p class="text-slate-600 leading-relaxed">Learn from seasoned professionals with years of real-world experience in their respective fields.</p>
                    </div>

                    <div class="group bg-white p-8 rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-3">Community Support</h3>
                        <p class="text-slate-600 leading-relaxed">Join an active community where you can ask questions, share experiences, and grow together.</p>
                    </div>

                    <div class="group bg-white p-8 rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-3">Career Growth</h3>
                        <p class="text-slate-600 leading-relaxed">Develop in-demand skills to advance your career and unlock new opportunities in your field.</p>
                    </div>

                    <div class="group bg-white p-8 rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-3">Flexible Learning</h3>
                        <p class="text-slate-600 leading-relaxed">Balance work, family, and education with our flexible course schedules and self-paced learning options.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-cyan-600">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center text-white">
                    <div>
                        <div class="text-5xl font-black mb-2">50K+</div>
                        <p class="text-blue-100 text-lg">Active Learners</p>
                    </div>
                    <div>
                        <div class="text-5xl font-black mb-2">500+</div>
                        <p class="text-blue-100 text-lg">Expert Courses</p>
                    </div>
                    <div>
                        <div class="text-5xl font-black mb-2">4.9★</div>
                        <p class="text-blue-100 text-lg">Average Rating</p>
                    </div>
                    <div>
                        <div class="text-5xl font-black mb-2">95%</div>
                        <p class="text-blue-100 text-lg">Completion Rate</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-20 px-4 sm:px-6 lg:px-8 bg-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100 rounded-full filter blur-3xl opacity-20 -z-10"></div>
            
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-5xl font-black text-slate-900 mb-6">Ready to Transform Your Career?</h2>
                <p class="text-xl text-slate-600 mb-8 max-w-2xl mx-auto leading-relaxed">Start learning today and join thousands of professionals who've already boosted their skills and career prospects with QuickLearn.</p>
                <Link v-if="canRegister" :href="route('register')" class="inline-block px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold rounded-xl hover:shadow-2xl transition-all hover:scale-105 text-lg">
                    Get Started Free
                </Link>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-400 py-16 px-4 sm:px-6 lg:px-8 border-t border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-12 mb-12">
                    <div class="md:col-span-1">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg"></div>
                            <span class="font-black text-lg text-white">QuickLearn</span>
                        </div>
                        <p class="text-slate-500 text-sm leading-relaxed">Empowering learners worldwide with quality education and career advancement.</p>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4 text-white text-sm uppercase tracking-wider">Product</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#" class="hover:text-blue-400 transition">Courses</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Pricing</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Mobile App</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">What's New</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4 text-white text-sm uppercase tracking-wider">Company</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#" class="hover:text-blue-400 transition">About Us</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Blog</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Careers</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Contact</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4 text-white text-sm uppercase tracking-wider">Resources</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#" class="hover:text-blue-400 transition">Help Center</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Documentation</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">API</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Community</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4 text-white text-sm uppercase tracking-wider">Legal</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#" class="hover:text-blue-400 transition">Privacy</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Terms</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Cookies</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition">Support</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-slate-800 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-slate-500">&copy; 2025 QuickLearn. All rights reserved.</p>
                        <div class="flex space-x-6 mt-4 md:mt-0">
                            <a href="#" class="text-slate-500 hover:text-blue-400 transition text-sm">Twitter</a>
                            <a href="#" class="text-slate-500 hover:text-blue-400 transition text-sm">LinkedIn</a>
                            <a href="#" class="text-slate-500 hover:text-blue-400 transition text-sm">GitHub</a>
                            <a href="#" class="text-slate-500 hover:text-blue-400 transition text-sm">Facebook</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes blob {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}
</style>

