<template>
    <StudentLayout>
        <Head title="Browse Courses" />
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
    </StudentLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';

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
