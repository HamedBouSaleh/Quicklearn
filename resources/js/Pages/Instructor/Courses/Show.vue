<script setup>
import { Head, router } from '@inertiajs/vue3';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';

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

const createQuiz = (lessonId) => {
    router.visit(route('instructor.quizzes.create', lessonId));
};

const viewQuizzes = (lessonId) => {
    router.visit(route('instructor.quizzes.index', lessonId));
};
</script>

<template>
    <Head :title="course.title" />

    <InstructorLayout>
        <div class="max-w-5xl">
            <!-- Course Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ course.title }}</h1>
                        <p class="mt-2 text-gray-600">{{ course.short_description }}</p>
                    </div>
                    <button
                        @click="editCourse"
                        class="rounded-lg bg-primary-500 px-6 py-2 text-white font-medium hover:bg-primary-600 transition"
                    >
                        Edit Course
                    </button>
                </div>
            </div>

            <!-- Course Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-gray-600 text-sm">Difficulty</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ course.difficulty }}</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-gray-600 text-sm">Status</p>
                    </div>
                    <p class="text-xl font-bold" :class="course.is_published ? 'text-green-600' : 'text-gray-600'">
                        {{ course.is_published ? 'Published' : 'Draft' }}
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-gray-600 text-sm">Students</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ course.students_count || 0 }}</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-gray-600 text-sm">Lessons</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ lessons.length }}</p>
                </div>
            </div>

            <!-- Lessons Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900">
                            Course Lessons ({{ lessons.length }})
                        </h2>
                        <button
                            @click="addLesson"
                            class="flex items-center rounded-lg bg-primary-500 px-4 py-2 text-white font-medium hover:bg-primary-600 transition"
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
                            class="inline-flex items-center rounded-lg bg-primary-500 px-4 py-2 text-white font-medium hover:bg-primary-600 transition"
                        >
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add First Lesson
                        </button>
                    </div>
                </div>

                <div v-else class="divide-y divide-gray-200">
                    <div v-for="(lesson, index) in lessons" :key="lesson.id" class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                        <span class="text-lg font-semibold text-primary-600">{{ index + 1 }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-bold text-gray-900">
                                            {{ lesson.title }}
                                        </h4>
                                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ lesson.lesson_type }}
                                            </span>
                                            <span v-if="lesson.estimated_duration" class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ lesson.estimated_duration }} min
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button
                                    v-if="lesson.quizzes_count > 0"
                                    @click="viewQuizzes(lesson.id)"
                                    class="rounded-lg bg-purple-50 px-4 py-2 text-sm font-medium text-purple-600 hover:bg-purple-100 transition"
                                >
                                    {{ lesson.quizzes_count }} Quiz{{ lesson.quizzes_count !== 1 ? 'zes' : '' }}
                                </button>
                                <button
                                    @click="createQuiz(lesson.id)"
                                    class="rounded-lg bg-green-50 px-4 py-2 text-sm font-medium text-green-600 hover:bg-green-100 transition"
                                >
                                    + Quiz
                                </button>
                                <button
                                    @click="editLesson(lesson.id)"
                                    class="rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-100 transition"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteLesson(lesson.id)"
                                    class="rounded-lg bg-red-50 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-100 transition"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </InstructorLayout>
</template>
