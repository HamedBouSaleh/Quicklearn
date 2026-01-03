<template>
    <div class="bg-gray-50 min-h-screen">
        <Head title="Browse Courses" />

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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Browse Courses</h1>
                    <p class="text-gray-600">Discover and enroll in courses to start learning</p>
                </div>

                <!-- Filters Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <div class="relative">
                                <input 
                                    v-model="filters.search"
                                    @input="debouncedSearch"
                                    type="text" 
                                    placeholder="Search courses..." 
                                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select 
                                v-model="filters.category"
                                @change="applyFilters"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">All Categories</option>
                                <option value="Programming">Programming</option>
                                <option value="Design">Design</option>
                                <option value="Business">Business</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Data Science">Data Science</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Difficulty Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                            <select 
                                v-model="filters.difficulty"
                                @change="applyFilters"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">All Levels</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Sort by:</span>
                            <div class="flex gap-2">
                                <button 
                                    @click="filters.sort = 'newest'; applyFilters()"
                                    :class="filters.sort === 'newest' ? 'bg-primary-500 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                                >
                                    Newest
                                </button>
                                <button 
                                    @click="filters.sort = 'popular'; applyFilters()"
                                    :class="filters.sort === 'popular' ? 'bg-primary-500 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                                >
                                    Most Popular
                                </button>
                                <button 
                                    @click="filters.sort = 'title'; applyFilters()"
                                    :class="filters.sort === 'title' ? 'bg-primary-500 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                                >
                                    Title A-Z
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses Grid -->
                <div v-if="courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="course in courses" :key="course.id" class="bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden">
                        <div class="relative">
                            <!-- Gradient background based on category -->
                            <div class="h-40 flex items-center justify-center" :class="getCategoryGradient(course.category)">
                                <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="course.category === 'Programming'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                    <path v-else-if="course.category === 'Design'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" />
                                </svg>
                            </div>
                            <span v-if="course.difficulty" class="absolute top-3 right-3 bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                                {{ course.difficulty }}
                            </span>
                        </div>
                        <div class="p-5">
                            <span class="text-xs text-primary-500 font-semibold uppercase">{{ course.category }}</span>
                            <h3 class="text-lg font-bold text-gray-900 mt-1 mb-2">{{ course.title }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ course.short_description || 'Learn comprehensive course material' }}</p>
                            <div class="flex items-center text-sm text-gray-600 mb-4">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path>
                                </svg>
                                <span>{{ course.estimated_duration || '12' }} hours</span>
                                <svg class="w-4 h-4 ml-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 12a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                                </svg>
                                <span>{{ course.enrollment_count || 0 }} students</span>
                            </div>
                            <Link 
                                :href="route('student.courses.show', course.id)"
                                class="w-full bg-primary-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-600 transition block text-center"
                            >
                                {{ course.is_enrolled ? 'Continue Learning' : 'Enroll Now' }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No courses found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your filters to find what you're looking for</p>
                    <button 
                        @click="clearAllFilters"
                        class="px-6 py-2 bg-primary-500 text-white font-medium rounded-lg hover:bg-primary-600 transition"
                    >
                        Clear Filters
                    </button>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const showDropdown = ref(false);

const props = defineProps({
    courses: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            category: '',
            difficulty: '',
            sort: 'newest'
        })
    }
});

const filters = reactive({
    search: props.filters.search || '',
    category: props.filters.category || '',
    difficulty: props.filters.difficulty || '',
    sort: props.filters.sort || 'newest'
});

let searchTimeout = null;

const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

const applyFilters = () => {
    router.get(route('student.courses.browse'), filters, {
        preserveState: true,
        preserveScroll: true
    });
};

const getCategoryGradient = (category) => {
    const gradients = {
        'Programming': 'bg-gradient-to-br from-green-500 to-emerald-600',
        'Design': 'bg-gradient-to-br from-purple-500 to-pink-600',
        'Mathematics': 'bg-gradient-to-br from-blue-500 to-sky-600',
        'Business': 'bg-gradient-to-br from-orange-500 to-red-600',
        'Marketing': 'bg-gradient-to-br from-pink-500 to-rose-600',
        'Data Science': 'bg-gradient-to-br from-indigo-500 to-purple-600',
        'Other': 'bg-gradient-to-br from-gray-500 to-gray-600'
    };
    return gradients[category] || 'bg-gradient-to-br from-gray-500 to-gray-600';
};

const clearAllFilters = () => {
    filters.search = '';
    filters.category = '';
    filters.difficulty = '';
    filters.sort = 'newest';
    applyFilters();
};
</script>
