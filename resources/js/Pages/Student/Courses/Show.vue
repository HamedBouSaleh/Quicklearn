<template>
    <StudentLayout>
        <Head :title="course.title" />

        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Course Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-10 mb-10 text-white shadow-2xl">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="mb-4">
                            <Badge variant="default" class="bg-white text-blue-600">
                                {{ course.category || 'General' }}
                            </Badge>
                            <Badge :variant="getDifficultyColor(course.difficulty)" class="ml-2">
                                {{ course.difficulty }}
                            </Badge>
                        </div>
                        
                        <h1 class="text-4xl font-bold mb-4">{{ course.title }}</h1>
                        
                        <p class="text-lg mb-6 opacity-90">{{ course.short_description }}</p>

                        <div class="flex items-center space-x-6 text-sm">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ course.instructor_name }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ course.estimated_duration || 0 }} minutes
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                {{ course.enrollment_count || 0 }} students enrolled
                            </div>
                        </div>

                        <!-- Enrollment Status -->
                        <div class="mt-6">
                            <button
                                v-if="!course.is_enrolled"
                                @click="enrollCourse"
                                class="px-8 py-3.5 bg-white text-blue-600 font-bold rounded-xl hover:bg-gray-50 transition shadow-xl hover:shadow-2xl text-lg"
                            >
                                Enroll in this Course
                            </button>
                            <div v-else class="flex items-center space-x-4">
                                <div class="px-6 py-2 bg-green-500 text-white font-semibold rounded-lg">
                                    ✓ Enrolled
                                </div>
                                <div class="text-white">
                                    <span class="text-sm opacity-75">Progress: </span>
                                    <span class="text-xl font-bold">{{ course.progress }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Thumbnail -->
                    <div v-if="course.thumbnail_url" class="ml-8 hidden lg:block">
                        <img
                            :src="course.thumbnail_url"
                            :alt="course.title"
                            class="w-64 h-40 object-cover rounded-lg shadow-xl"
                        />
                    </div>
                </div>

                <!-- Progress Bar (if enrolled) -->
                <div v-if="course.is_enrolled" class="mt-6">
                    <ProgressBar :progress="course.progress" :show-label="true" color="white" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- About This Course -->
                    <Card>
                        <template #header>
                            <h2 class="text-2xl font-bold text-gray-900">About This Course</h2>
                        </template>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 leading-relaxed">
                                {{ course.long_description || course.short_description || 'No detailed description available.' }}
                            </p>
                        </div>
                    </Card>

                    <!-- Course Content / Lessons -->
                    <Card>
                        <template #header>
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-bold text-gray-900">Course Content</h2>
                                <span class="text-sm text-gray-600">{{ lessons.length }} lessons</span>
                            </div>
                        </template>

                        <div v-if="lessons.length > 0" class="space-y-3">
                            <div
                                v-for="(lesson, index) in lessons"
                                :key="lesson.id"
                                :class="[
                                    'flex items-center p-5 rounded-xl border-2 transition-all duration-200',
                                    course.is_enrolled 
                                        ? 'cursor-pointer hover:border-blue-400 hover:bg-blue-50 hover:-translate-y-0.5 hover:shadow-md'
                                        : 'cursor-not-allowed opacity-75',
                                    lesson.is_completed ? 'border-green-300 bg-green-50' : 'border-gray-200'
                                ]"
                                @click="course.is_enrolled && viewLesson(lesson.id)"
                            >
                                <!-- Lesson Number -->
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center mr-4">
                                    {{ index + 1 }}
                                </div>

                                <!-- Lesson Info -->
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-1">{{ lesson.title }}</h3>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            {{ lesson.lesson_type }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ lesson.estimated_duration || 0 }} min
                                        </span>
                                    </div>
                                </div>

                                <!-- Completion Status -->
                                <div class="flex-shrink-0 ml-4">
                                    <svg
                                        v-if="lesson.is_completed"
                                        class="w-6 h-6 text-green-500"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <svg
                                        v-else-if="!course.is_enrolled"
                                        class="w-6 h-6 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-6 h-6 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 text-gray-500">
                            <p>No lessons available yet</p>
                        </div>

                        <!-- Enroll to Unlock -->
                        <div v-if="!course.is_enrolled && lessons.length > 0" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-center">
                            <p class="text-blue-800 font-medium mb-3">Enroll in this course to access all lessons</p>
                            <button
                                @click="enrollCourse"
                                class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                            >
                                Enroll Now
                            </button>
                        </div>
                    </Card>

                    <!-- Quizzes -->
                    <Card v-if="quizzes.length > 0">
                        <template #header>
                            <h2 class="text-2xl font-bold text-gray-900">Quizzes</h2>
                        </template>

                        <div class="space-y-3">
                            <div
                                v-for="quiz in quizzes"
                                :key="quiz.id"
                                class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 transition"
                            >
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-1">{{ quiz.title }}</h3>
                                    <p class="text-sm text-gray-600">
                                        Passing score: {{ quiz.passing_score }}% 
                                        <span v-if="quiz.time_limit">• {{ quiz.time_limit }} minutes</span>
                                    </p>
                                </div>
                                <button
                                    v-if="course.is_enrolled"
                                    @click="startQuiz(quiz.id)"
                                    class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                                >
                                    Start Quiz
                                </button>
                                <div v-else class="px-4 py-2 bg-gray-200 text-gray-600 rounded-lg">
                                    Locked
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Course Stats -->
                    <Card>
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900">Course Details</h3>
                        </template>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                <span class="text-gray-600">Lessons</span>
                                <span class="font-semibold text-gray-900">{{ lessons.length }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                <span class="text-gray-600">Quizzes</span>
                                <span class="font-semibold text-gray-900">{{ quizzes.length }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                <span class="text-gray-600">Duration</span>
                                <span class="font-semibold text-gray-900">{{ course.estimated_duration || 0 }} min</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Level</span>
                                <Badge :variant="getDifficultyColor(course.difficulty)">
                                    {{ course.difficulty }}
                                </Badge>
                            </div>
                        </div>
                    </Card>

                    <!-- Instructor Card -->
                    <Card>
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900">Instructor</h3>
                        </template>

                        <div class="space-y-4">
                            <button 
                                @click="showInstructorModal = true"
                                class="w-full flex items-center p-3 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
                            >
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xl flex-shrink-0">
                                    {{ course.instructor_name.charAt(0).toUpperCase() }}
                                </div>
                                <div class="ml-4 text-left flex-1">
                                    <p class="font-semibold text-gray-900">{{ course.instructor_name }}</p>
                                    <p class="text-sm text-gray-600">Course Instructor</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <div v-if="course.instructor_bio" class="pt-4 border-t border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">About the Instructor</h4>
                                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ course.instructor_bio }}</p>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Instructor Contact Modal -->
        <Teleport to="body">
            <div v-if="showInstructorModal" class="fixed inset-0 z-50 overflow-y-auto" @click="showInstructorModal = false">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                    <!-- Center modal -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal panel -->
                    <div @click.stop class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-6 py-6">
                            <div class="flex items-start justify-between mb-6">
                                <div class="flex items-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                                        {{ course.instructor_name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-2xl font-black text-gray-900">{{ course.instructor_name }}</h3>
                                        <p class="text-sm text-gray-600">Course Instructor</p>
                                    </div>
                                </div>
                                <button @click="showInstructorModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Contact Information -->
                                <div class="bg-blue-50 rounded-xl p-4">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Contact Information</h4>
                                    <a :href="`mailto:${course.instructor_email}`" class="flex items-center text-blue-600 hover:text-blue-700 font-medium">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ course.instructor_email }}
                                    </a>
                                </div>

                                <!-- Instructor Bio -->
                                <div v-if="course.instructor_bio" class="bg-gray-50 rounded-xl p-4">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3">About the Instructor</h4>
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ course.instructor_bio }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </StudentLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import ProgressBar from '@/Components/ProgressBar.vue';

const showInstructorModal = ref(false);

const props = defineProps({
    course: {
        type: Object,
        required: true
    },
    lessons: {
        type: Array,
        default: () => []
    },
    quizzes: {
        type: Array,
        default: () => []
    }
});

const getDifficultyColor = (difficulty) => {
    const colors = {
        'Beginner': 'success',
        'Intermediate': 'warning',
        'Advanced': 'danger'
    };
    return colors[difficulty] || 'primary';
};

const enrollCourse = () => {
    router.post(route('student.courses.enroll', props.course.id));
};

const viewLesson = (lessonId) => {
    router.visit(route('student.lessons.show', lessonId));
};

const startQuiz = (quizId) => {
    router.visit(route('student.quizzes.show', quizId));
};
</script>
