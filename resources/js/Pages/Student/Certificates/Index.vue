<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const showDropdown = ref(false);

const props = defineProps({
    certificates: {
        type: Array,
        default: () => []
    }
});

const downloadCertificate = (id) => {
    window.open(route('student.certificates.download', id), '_blank');
};
</script>

<template>
    <div class="bg-gray-50 min-h-screen">
        <Head title="My Certificates" />

        <!-- Top Navigation -->
        <nav class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <Link href="/" class="text-2xl font-bold text-primary-500">QuickLearn</Link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="relative text-gray-600 hover:text-primary-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="relative">
                            <button @click="showDropdown = !showDropdown" class="flex items-center space-x-2 hover:opacity-75 transition">
                                <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ $page.props.auth.user?.name?.charAt(0).toUpperCase() || 'U' }}
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ $page.props.auth.user?.name }}</span>
                            </button>
                            <div v-if="showDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50">
                                <NavLink :href="route('profile.edit')" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 first:rounded-t-lg">
                                    Profile Settings
                                </NavLink>
                                <Link method="post" :href="route('logout')" class="block px-4 py-2 text-red-600 hover:bg-red-50 last:rounded-b-lg cursor-pointer">
                                    Logout
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-white h-screen sticky top-16 shadow-sm">
                <nav class="p-4">
                    <ul class="space-y-2">
                        <li>
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="flex items-center space-x-3 px-4 py-3 rounded-lg font-medium" :class="route().current('dashboard') ? 'bg-sky-100 text-primary-500' : 'text-gray-600 hover:bg-gray-50'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>Home</span>
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('student.courses.index')" :active="route().current('student.courses.index')" class="flex items-center space-x-3 px-4 py-3 rounded-lg font-medium" :class="route().current('student.courses.index') ? 'bg-sky-100 text-primary-500' : 'text-gray-600 hover:bg-gray-50'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span>My Courses</span>
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('student.courses.browse')" :active="route().current('student.courses.browse')" class="flex items-center space-x-3 px-4 py-3 rounded-lg font-medium" :class="route().current('student.courses.browse') ? 'bg-sky-100 text-primary-500' : 'text-gray-600 hover:bg-gray-50'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span>Browse Courses</span>
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('student.certificates.index')" :active="route().current('student.certificates.*')" class="flex items-center space-x-3 px-4 py-3 rounded-lg font-medium" :class="route().current('student.certificates.*') ? 'bg-sky-100 text-primary-500' : 'text-gray-600 hover:bg-gray-50'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Certificates</span>
                            </NavLink>
                        </li>
                        <li class="pt-4 border-t border-gray-200">
                            <NavLink :href="route('profile.edit')" :active="route().current('profile.*')" class="flex items-center space-x-3 px-4 py-3 rounded-lg font-medium" :class="route().current('profile.*') ? 'bg-sky-100 text-primary-500' : 'text-gray-600 hover:bg-gray-50'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                                <span>Settings</span>
                            </NavLink>
                        </li>
                        <li>
                            <Link method="post" :href="route('logout')" class="flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Logout</span>
                            </Link>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-8 bg-gray-50">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">My Certificates</h1>
                    <p class="text-gray-600">View and download your course completion certificates</p>
                </div>

                <!-- Certificates Grid -->
                <div v-if="certificates && certificates.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="certificate in certificates" :key="certificate.id" class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="h-32 bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ certificate.course_title }}</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Completed: {{ certificate.completion_date }}
                            </p>
                            
                            <div class="flex space-x-2">
                                <Link :href="route('student.certificates.show', certificate.id)"
                                      class="flex-1 px-4 py-2 bg-primary-500 text-white text-center rounded-lg hover:bg-primary-600 transition text-sm font-medium">
                                    View
                                </Link>
                                <button @click="downloadCertificate(certificate.id)"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Certificates Yet</h3>
                    <p class="text-gray-600 mb-6">Complete courses to earn certificates</p>
                    <Link :href="route('student.courses.browse')"
                          class="inline-block px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition font-medium">
                        Browse Courses
                    </Link>
                </div>
            </main>
        </div>
    </div>
</template>
