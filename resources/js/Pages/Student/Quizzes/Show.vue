<template>
    <Head :title="quiz.title" />
    
    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation Bar -->
        <nav class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="/student/dashboard" class="text-2xl font-bold text-primary-500">QuickLearn</a>
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ user.name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="pt-16">
            <div class="max-w-4xl mx-auto py-8 px-6">
                <!-- Quiz Header -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 mb-8">
                    <div class="mb-6">
                        <a :href="`/student/courses/${quiz.course_id}`" class="text-primary-600 hover:text-primary-700 text-sm font-medium mb-2 inline-block">
                            ← Back to Course
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ quiz.title }}</h1>
                        <p v-if="quiz.description" class="text-gray-600">{{ quiz.description }}</p>
                    </div>

                    <!-- Quiz Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Questions</div>
                                <div class="text-xl font-bold text-gray-900">{{ quiz.questions_count }}</div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Time Limit</div>
                                <div class="text-xl font-bold text-gray-900">{{ quiz.time_limit ? quiz.time_limit + ' min' : 'No Limit' }}</div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Passing Score</div>
                                <div class="text-xl font-bold text-gray-900">{{ quiz.passing_score }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Previous Attempt -->
                    <div v-if="previousAttempt" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-2">Previous Attempt</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-800">
                                    Score: <span class="font-bold">{{ previousAttempt.percentage }}%</span>
                                    <span v-if="previousAttempt.passed" class="ml-2 text-green-600">✓ Passed</span>
                                    <span v-else class="ml-2 text-red-600">✗ Failed</span>
                                </p>
                                <p class="text-xs text-blue-600">{{ previousAttempt.submitted_at }}</p>
                                <p v-if="attemptCount > 0" class="text-xs text-blue-600 mt-1">
                                    Attempts: {{ attemptCount }}{{ quiz.max_attempts ? ` / ${quiz.max_attempts}` : '' }}
                                </p>
                            </div>
                            <button
                                @click="viewResults"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition"
                            >
                                View Results
                            </button>
                        </div>
                    </div>

                    <!-- Retake Message -->
                    <div v-if="!canRetake && retakeMessage" class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-amber-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="font-semibold text-amber-900">Cannot Retake Quiz</h3>
                                <p class="text-sm text-amber-800 mt-1">{{ retakeMessage }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Start Quiz Button -->
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <p>Read the instructions carefully before starting.</p>
                            <p v-if="quiz.time_limit">Once started, the timer will begin automatically.</p>
                        </div>
                        <button
                            v-if="!previousAttempt || canRetake"
                            @click="startQuiz"
                            :disabled="starting"
                            class="px-8 py-3 bg-primary-600 text-white font-bold rounded-lg hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ starting ? 'Starting...' : (previousAttempt ? 'Retake Quiz' : 'Start Quiz') }}
                        </button>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Instructions</h2>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Answer all {{ quiz.questions_count }} questions
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            You need {{ quiz.passing_score }}% or higher to pass
                        </li>
                        <li v-if="quiz.time_limit" class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Complete the quiz within {{ quiz.time_limit }} minutes
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            You can navigate between questions before submitting
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Review your answers before final submission
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const starting = ref(false);

const user = computed(() => page.props.auth.user);

const props = defineProps({
    quiz: {
        type: Object,
        required: true
    },
    previousAttempt: {
        type: Object,
        default: null
    },
    canRetake: {
        type: Boolean,
        default: true
    },
    retakeMessage: {
        type: String,
        default: ''
    },
    attemptCount: {
        type: Number,
        default: 0
    }
});

const startQuiz = () => {
    starting.value = true;
    router.post(route('student.quizzes.start', props.quiz.id));
};

const viewResults = () => {
    if (props.previousAttempt) {
        router.visit(route('student.quizzes.results', props.previousAttempt.id));
    }
};
</script>
