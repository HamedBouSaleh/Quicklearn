<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm relative z-40">
            <div class="flex justify-between items-center h-16 pr-4">
                    <!-- Logo -->
                    <div class="flex items-center pl-8">
                        <Link href="/" class="flex items-center">
                            <span class="text-xl font-bold text-blue-600">QuickLearn</span>
                        </Link>
                    </div>

                    <!-- Search Bar -->
                    <div v-if="route().current('student.dashboard')" class="hidden md:flex flex-1 max-w-lg mx-8">
                        <div class="w-full">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    v-model="searchQuery"
                                    @keypress="handleSearchEnter"
                                    placeholder="Search courses..." 
                                    class="w-full pl-10 pr-20 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <button 
                                    @click="handleSearch"
                                    class="absolute inset-y-0 right-0 px-4 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition font-medium text-sm"
                                >
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none transition">
                                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                        {{ $page.props.auth.user?.name?.charAt(0).toUpperCase() || 'U' }}
                                    </div>
                                    <span class="ml-2 hidden sm:block">{{ $page.props.auth.user?.name }}</span>
                                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
        </header>

        <div class="flex relative">
            <!-- Toggle Button - Outside Sidebar -->
            <button 
                @click="sidebarOpen = !sidebarOpen"
                :class="sidebarOpen ? 'left-64' : 'left-0'"
                class="fixed z-30 p-2 bg-gray-300 text-gray-700 hover:bg-gray-400 transition-all duration-300 rounded"
                style="top: 64px;"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Sidebar -->
            <aside 
                :class="[
                    'fixed inset-y-0 left-0 z-20 w-64 bg-gray-100 border-r border-gray-300 shadow-lg transform transition-transform duration-300 ease-in-out',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                ]"
                style="top: 64px;"
            >
                <!-- Navigation Header -->
                <div class="flex items-center px-4 py-3 bg-gray-200 border-b border-gray-300">
                    <svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-semibold text-gray-700">Navigation</span>
                </div>
                <nav class="mt-2 px-4 space-y-1">
                    <NavLink 
                        :href="route('dashboard')" 
                        :active="route().current('dashboard')"
                        class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </NavLink>

                    <NavLink 
                        :href="route('courses.index')" 
                        :active="route().current('courses.*')"
                        class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        My Courses
                    </NavLink>

                    <NavLink 
                        :href="route('student.certificates.index')" 
                        :active="route().current('student.certificates.*')"
                        class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                        Certificates
                    </NavLink>

                    <NavLink 
                        :href="route('profile.edit')" 
                        :active="route().current('profile.*')"
                        class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </NavLink>
                </nav>
            </aside>

            <!-- Overlay for mobile -->
            <div 
                v-if="sidebarOpen" 
                @click="sidebarOpen = false"
                class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
            ></div>

            <!-- Main Content -->
            <main :class="sidebarOpen ? 'lg:ml-64' : 'ml-0'" class="flex-1 p-6 lg:p-8 transition-all duration-300 pt-4">
                <slot />
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">About</h3>
                        <p class="mt-4 text-sm text-gray-600">
                            Learn - Your gateway to quality online education and professional development.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Contact</h3>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600">
                            <li>Email: support@learn.com</li>
                            <li>Phone: +1 (555) 123-4567</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Legal</h3>
                        <ul class="mt-4 space-y-2 text-sm">
                            <a href="#" class="text-gray-600 hover:text-blue-600">Terms of Service</a><br/>
                            <a href="#" class="text-gray-600 hover:text-blue-600">Privacy Policy</a>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-200 pt-8 text-center">
                    <p class="text-sm text-gray-500">&copy; 2025 Learn. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

const sidebarOpen = ref(true);
const searchQuery = ref('');

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('student.courses.browse'), { search: searchQuery.value });
    }
};

const handleSearchEnter = (event) => {
    if (event.key === 'Enter') {
        handleSearch();
    }
};
</script>
