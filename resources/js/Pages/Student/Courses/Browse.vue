<template>
    <StudentLayout>
        <Head title="Browse Courses" />

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Browse Courses</h1>
                <p class="mt-2 text-gray-600">Discover and enroll in courses to start learning</p>
            </div>

            <!-- Search & Filters -->
            <Card padding class="mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Search courses..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @input="debouncedSearch"
                        />
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select
                            v-model="filters.category"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @change="applyFilters"
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
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @change="applyFilters"
                        >
                            <option value="">All Levels</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="mt-4 flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">Sort by:</span>
                    <button
                        v-for="option in sortOptions"
                        :key="option.value"
                        @click="filters.sort = option.value; applyFilters()"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition',
                            filters.sort === option.value
                                ? 'bg-blue-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </Card>

            <!-- Course Grid -->
            <div v-if="courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="course in courses"
                    :key="course.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow cursor-pointer"
                    @click="viewCourse(course.id)"
                >
                    <!-- Thumbnail -->
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-indigo-600 relative">
                        <img
                            v-if="course.thumbnail_url"
                            :src="course.thumbnail_url"
                            :alt="course.title"
                            class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        
                        <!-- Difficulty Badge -->
                        <div class="absolute top-4 right-4">
                            <Badge :variant="getDifficultyColor(course.difficulty)">
                                {{ course.difficulty }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div class="p-6">
                        <div class="mb-2">
                            <span class="text-xs font-semibold text-blue-600 uppercase">{{ course.category || 'General' }}</span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ course.title }}</h3>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ course.short_description || 'No description available' }}
                        </p>

                        <!-- Course Meta -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ course.estimated_duration || 0 }} min
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                {{ course.enrollment_count || 0 }} students
                            </div>
                        </div>

                        <!-- Enroll Button -->
                        <button
                            v-if="!course.is_enrolled"
                            @click.stop="enrollCourse(course.id)"
                            class="w-full py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                        >
                            Enroll Now
                        </button>
                        <div
                            v-else
                            class="w-full py-2 bg-green-100 text-green-700 font-semibold rounded-lg text-center"
                        >
                            ✓ Enrolled
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
                <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M12 12h.01M12 12h.01M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No courses found</h3>
                <p class="text-gray-500">Try adjusting your search or filters</p>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';

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

const sortOptions = [
    { label: 'Newest', value: 'newest' },
    { label: 'Most Popular', value: 'popular' },
    { label: 'Title A-Z', value: 'title' }
];

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

const getDifficultyColor = (difficulty) => {
    const colors = {
        'Beginner': 'success',
        'Intermediate': 'warning',
        'Advanced': 'danger'
    };
    return colors[difficulty] || 'primary';
};

const viewCourse = (courseId) => {
    router.visit(route('student.courses.show', courseId));
};

const enrollCourse = (courseId) => {
    router.post(route('student.courses.enroll', courseId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Course enrollment successful
        }
    });
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
