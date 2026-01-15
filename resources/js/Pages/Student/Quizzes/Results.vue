<template>
    <Head :title="`Quiz Results: ${quiz.title}`" />
    
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
                <!-- Results Header -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 mb-8">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 mx-auto mb-4 rounded-full flex items-center justify-center"
                             :class="attempt.passed ? 'bg-green-100' : 'bg-red-100'">
                            <svg v-if="attempt.passed" class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold mb-2" :class="attempt.passed ? 'text-green-600' : 'text-red-600'">
                            {{ attempt.passed ? 'Congratulations!' : 'Keep Practicing!' }}
                        </h1>
                        <p class="text-gray-600">{{ quiz.title }}</p>
                    </div>

                    <!-- Score Display -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <div class="text-4xl font-bold mb-2" :class="attempt.passed ? 'text-green-600' : 'text-red-600'">
                                {{ Math.round(attempt.percentage) }}%
                            </div>
                            <div class="text-sm text-gray-600">Your Score</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <div class="text-4xl font-bold text-gray-900 mb-2">
                                {{ quiz.passing_score }}%
                            </div>
                            <div class="text-sm text-gray-600">Passing Score</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <div class="text-4xl font-bold text-gray-900 mb-2">
                                {{ attempt.time_taken || 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-600">Time Taken (min)</div>
                        </div>
                    </div>

                    <!-- Submitted At -->
                    <div class="mt-6 text-center text-sm text-gray-600">
                        Submitted on {{ attempt.submitted_at }}
                    </div>
                </div>

                <!-- Question Review -->
                <div class="space-y-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Question Review</h2>
                    
                    <div
                        v-for="(question, index) in questions"
                        :key="index"
                        class="bg-white rounded-2xl p-6 shadow-sm border-2"
                        :class="question.is_correct ? 'border-green-200' : 'border-red-200'"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 flex-1">
                                Question {{ index + 1 }}: {{ question.question_text }}
                            </h3>
                            <div v-if="question.is_correct" class="flex-shrink-0 ml-4">
                                <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div v-else class="flex-shrink-0 ml-4">
                                <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="text-sm font-semibold text-blue-900 mb-1">Your Answer:</div>
                                <div class="text-blue-800">{{ question.your_answer }}</div>
                            </div>

                            <div v-if="!question.is_correct" class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="text-sm font-semibold text-green-900 mb-1">Correct Answer:</div>
                                <div class="text-green-800">{{ question.correct_answer }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <a
                        href="/student/courses"
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-primary-500 hover:text-primary-600 transition"
                    >
                        Back to Courses
                    </a>
                    <button
                        @click="retakeQuiz"
                        class="px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition"
                    >
                        Retake Quiz
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps({
    attempt: {
        type: Object,
        required: true
    },
    quiz: {
        type: Object,
        required: true
    },
    questions: {
        type: Array,
        required: true
    }
});

const retakeQuiz = () => {
    router.visit(route('student.quizzes.show', props.attempt.quiz_id));
};
</script>
