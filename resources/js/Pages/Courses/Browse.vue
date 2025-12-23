<template>
    <StudentLayout>
        <Head title="Browse Courses" />

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Browse Courses</h1>
                <p class="text-gray-600">Explore our wide range of courses and start learning today</p>
            </div>

            <!-- Filters -->
            <Card class="mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <div class="relative">
                            <input 
                                v-model="filters.search"
                                @input="applyFilters"
                                type="text" 
                                placeholder="Search courses..." 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category }}
                            </option>
                        </select>
                    </div>

                    <!-- Difficulty Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                        <select 
                            v-model="filters.difficulty"
                            @change="applyFilters"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Levels</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filters -->
                <div v-if="hasActiveFilters" class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center flex-wrap gap-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        <Badge v-if="filters.category" variant="primary" class="cursor-pointer" @click="clearFilter('category')">
                            {{ filters.category }} ×
                        </Badge>
                        <Badge v-if="filters.difficulty" variant="info" class="cursor-pointer" @click="clearFilter('difficulty')">
                            {{ filters.difficulty }} ×
                        </Badge>
                        <button 
                            @click="clearAllFilters"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                        >
                            Clear all
                        </button>
                    </div>
                </div>
            </Card>

            <!-- Results Count -->
            <div class="mb-6 flex items-center justify-between">
                <p class="text-gray-600">
                    Showing <span class="font-semibold">{{ filteredCourses.length }}</span> 
                    {{ filteredCourses.length === 1 ? 'course' : 'courses' }}
                </p>
                
                <!-- Sort -->
                <select 
                    v-model="sortBy"
                    @change="applySorting"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                >
                    <option value="popular">Most Popular</option>
                    <option value="newest">Newest First</option>
                    <option value="title">Title A-Z</option>
                </select>
            </div>

            <!-- Courses Grid -->
            <div v-if="filteredCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <CourseCard 
                    v-for="course in filteredCourses" 
                    :key="course.id"
                    :course="course"
                    :href="route('courses.show', course.id)"
                    :button-text="course.is_enrolled ? 'Continue Learning' : 'Enroll Now'"
                    :show-progress="course.is_enrolled"
                />
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
                    class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                >
                    Clear Filters
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="filteredCourses.length > 0" class="mt-12 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">1</button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">2</button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">3</button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Next
                    </button>
                </nav>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Card from '@/Components/Card.vue';
import CourseCard from '@/Components/CourseCard.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    courses: {
        type: Array,
        default: () => []
    },
    categories: {
        type: Array,
        default: () => ['Programming', 'Design', 'Business', 'Marketing', 'Personal Development']
    }
});

const filters = ref({
    search: '',
    category: '',
    difficulty: ''
});

const sortBy = ref('popular');

const hasActiveFilters = computed(() => {
    return filters.value.category || filters.value.difficulty || filters.value.search;
});

const filteredCourses = computed(() => {
    let result = [...props.courses];

    // Apply search filter
    if (filters.value.search) {
        const searchLower = filters.value.search.toLowerCase();
        result = result.filter(course => 
            course.title.toLowerCase().includes(searchLower) ||
            (course.description && course.description.toLowerCase().includes(searchLower))
        );
    }

    // Apply category filter
    if (filters.value.category) {
        result = result.filter(course => course.category === filters.value.category);
    }

    // Apply difficulty filter
    if (filters.value.difficulty) {
        result = result.filter(course => course.difficulty === filters.value.difficulty);
    }

    // Apply sorting
    if (sortBy.value === 'newest') {
        result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    } else if (sortBy.value === 'title') {
        result.sort((a, b) => a.title.localeCompare(b.title));
    } else if (sortBy.value === 'popular') {
        result.sort((a, b) => (b.students_count || 0) - (a.students_count || 0));
    }

    return result;
});

const applyFilters = () => {
    // Filters are reactive, so the computed property will update automatically
};

const applySorting = () => {
    // Sorting is reactive, so the computed property will update automatically
};

const clearFilter = (filterName) => {
    filters.value[filterName] = '';
};

const clearAllFilters = () => {
    filters.value = {
        search: '',
        category: '',
        difficulty: ''
    };
};
</script>
