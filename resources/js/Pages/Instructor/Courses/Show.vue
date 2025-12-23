<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    course: Object,
    lessons: Array
});

const addLesson = () => {
    router.visit(route('instructor.lessons.create', props.course.id));
};

const editLesson = (lessonId) => {
    router.visit(route('instructor.lessons.edit', lessonId));
};

const deleteLesson = (lessonId) => {
    if (confirm('Are you sure you want to delete this lesson?')) {
        router.delete(route('instructor.lessons.destroy', lessonId), {
            preserveScroll: true
        });
    }
};

const editCourse = () => {
    router.visit(route('instructor.courses.edit', props.course.id));
};

const getLessonTypeIcon = (type) => {
    const icons = {
        'Video': '🎥',
        'Article': '📝',
        'Mixed': '📚'
    };
    return icons[type] || '📄';
};
</script>

<template>
    <Head :title="course.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ course.title }}
                </h2>
                <button
                    @click="editCourse"
                    class="rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
                >
                    Edit Course
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Course Info -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="mb-4 flex items-center space-x-2">
                                    <Badge :type="course.difficulty === 'Beginner' ? 'success' : course.difficulty === 'Intermediate' ? 'warning' : 'error'">
                                        {{ course.difficulty }}
                                    </Badge>
                                    <Badge :type="course.is_published ? 'success' : 'default'">
                                        {{ course.is_published ? 'Published' : 'Draft' }}
                                    </Badge>
                                    <span class="text-sm text-gray-500">{{ course.category }}</span>
                                </div>
                                
                                <p class="mb-4 text-gray-600">{{ course.short_description }}</p>
                                
                                <div class="flex items-center space-x-6 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        {{ course.students_count }} students
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        {{ course.lessons_count }} lessons
                                    </div>
                                    <div v-if="course.estimated_duration" class="flex items-center">
                                        <svg class="mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ course.estimated_duration }} minutes
                                    </div>
                                </div>
                            </div>
                            
                            <img v-if="course.thumbnail_url" 
                                 :src="course.thumbnail_url" 
                                 :alt="course.title"
                                 class="h-32 w-48 rounded-lg object-cover"
                                 @error="$event.target.style.display='none'"
                            />
                        </div>
                    </div>
                </div>

                <!-- Lessons Section -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                Course Lessons ({{ lessons.length }})
                            </h3>
                            <button
                                @click="addLesson"
                                class="flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                            >
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Lesson
                            </button>
                        </div>
                    </div>

                    <div v-if="lessons.length === 0" class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No lessons yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding your first lesson.</p>
                        <div class="mt-6">
                            <button
                                @click="addLesson"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                            >
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add First Lesson
                            </button>
                        </div>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div v-for="lesson in lessons" :key="lesson.id" class="p-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-2xl">{{ getLessonTypeIcon(lesson.lesson_type) }}</span>
                                        <div>
                                            <h4 class="text-base font-medium text-gray-900">
                                                {{ lesson.order_position }}. {{ lesson.title }}
                                            </h4>
                                            <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                                <span>{{ lesson.lesson_type }}</span>
                                                <span v-if="lesson.estimated_duration">{{ lesson.estimated_duration }} min</span>
                                                <span v-if="lesson.attachments_count">{{ lesson.attachments_count }} attachments</span>
                                                <span v-if="lesson.quizzes_count">{{ lesson.quizzes_count }} quizzes</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="editLesson(lesson.id)"
                                        class="rounded-md bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 hover:bg-blue-100"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="deleteLesson(lesson.id)"
                                        class="rounded-md bg-red-50 px-3 py-1 text-sm font-medium text-red-700 hover:bg-red-100"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
