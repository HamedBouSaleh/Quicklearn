<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <div class="relative h-48 bg-gradient-to-br from-blue-400 to-blue-600">
            <img 
                v-if="course.thumbnail" 
                :src="course.thumbnail" 
                :alt="course.title"
                class="w-full h-full object-cover"
            />
            <div v-else class="flex items-center justify-center h-full">
                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div class="absolute top-2 right-2">
                <span class="px-3 py-1 text-xs font-semibold text-white bg-black bg-opacity-50 rounded-full">
                    {{ course.difficulty || 'Beginner' }}
                </span>
            </div>
        </div>
        
        <div class="p-5">
            <div class="mb-2">
                <span class="text-xs font-semibold text-blue-600 uppercase">
                    {{ course.category || 'General' }}
                </span>
            </div>
            
            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                {{ course.title }}
            </h3>
            
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                {{ course.description || 'Learn and master new skills with this comprehensive course.' }}
            </p>
            
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ course.duration || '10 hours' }}
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ course.students_count || 0 }} students
                </div>
            </div>
            
            <div v-if="showProgress && course.progress !== undefined" class="mb-4">
                <ProgressBar :progress="course.progress" />
            </div>
            
            <Link 
                :href="href" 
                class="block w-full text-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors"
            >
                {{ buttonText }}
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ProgressBar from './ProgressBar.vue';

defineProps({
    course: {
        type: Object,
        required: true
    },
    href: {
        type: String,
        required: true
    },
    buttonText: {
        type: String,
        default: 'View Course'
    },
    showProgress: {
        type: Boolean,
        default: false
    }
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
