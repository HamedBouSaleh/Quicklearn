<script setup>
import { Head, Link } from '@inertiajs/vue3';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';

const props = defineProps({
    attempt: Object,
    questions: Array
});
</script>

<template>
    <Head :title="`Review Attempt - ${attempt.student_name}`" />

    <InstructorLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <Link 
                    :href="route('instructor.quizzes.show', attempt.quiz_id)"
                    class="mb-4 text-primary-600 hover:text-primary-700 flex items-center text-sm font-medium"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Quiz
                </Link>
                
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Review Student Attempt</h1>
                        <p class="mt-2 text-gray-600">{{ attempt.quiz_title }}</p>
                    </div>
                </div>
            </div>

            <!-- Student Info & Score -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Student</p>
                        <p class="text-lg font-semibold text-gray-900">{{ attempt.student_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Score</p>
                        <p class="text-lg font-semibold" :class="attempt.passed ? 'text-green-600' : 'text-red-600'">
                            {{ attempt.score }}%
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Status</p>
                        <span 
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                            :class="attempt.passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                        >
                            {{ attempt.passed ? 'Passed' : 'Failed' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Submitted</p>
                        <p class="text-lg font-semibold text-gray-900">{{ attempt.submitted_at || 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Questions & Answers -->
            <div class="space-y-6">
                <div 
                    v-for="(question, index) in questions" 
                    :key="question.id"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
                >
                    <!-- Question Header -->
                    <div class="mb-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Question {{ index + 1 }}
                            </h3>
                            <span class="text-sm font-medium text-gray-600">
                                {{ question.points }} {{ question.points === 1 ? 'point' : 'points' }}
                            </span>
                        </div>
                        <p class="text-gray-800">{{ question.question_text }}</p>
                    </div>

                    <!-- Answers for MCQ/True-False -->
                    <div v-if="question.question_type === 'MCQ' || question.question_type === 'TrueFalse'" class="space-y-2">
                        <div
                            v-for="answer in question.answers"
                            :key="answer.id"
                            class="flex items-center p-3 rounded-lg border-2 transition"
                            :class="{
                                'border-green-500 bg-green-50': answer.is_correct,
                                'border-red-500 bg-red-50': answer.is_selected && !answer.is_correct,
                                'border-gray-200 bg-gray-50': !answer.is_correct && !answer.is_selected
                            }"
                        >
                            <div class="flex items-center flex-1">
                                <!-- Radio/Checkbox indicator -->
                                <div class="flex-shrink-0 w-6 h-6 mr-3">
                                    <div 
                                        v-if="answer.is_selected"
                                        class="w-6 h-6 rounded-full flex items-center justify-center"
                                        :class="answer.is_correct ? 'bg-green-500' : 'bg-red-500'"
                                    >
                                        <svg v-if="answer.is_correct" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div 
                                        v-else-if="answer.is_correct"
                                        class="w-6 h-6 rounded-full border-2 border-green-500"
                                    ></div>
                                    <div 
                                        v-else
                                        class="w-6 h-6 rounded-full border-2 border-gray-300"
                                    ></div>
                                </div>

                                <!-- Answer text -->
                                <span 
                                    class="text-sm font-medium"
                                    :class="{
                                        'text-green-900': answer.is_correct,
                                        'text-red-900': answer.is_selected && !answer.is_correct,
                                        'text-gray-700': !answer.is_correct && !answer.is_selected
                                    }"
                                >
                                    {{ answer.answer_text }}
                                </span>
                            </div>

                            <!-- Labels -->
                            <div class="flex items-center space-x-2">
                                <span v-if="answer.is_selected" class="text-xs font-semibold px-2 py-1 rounded" :class="answer.is_correct ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'">
                                    Student's Answer
                                </span>
                                <span v-if="answer.is_correct" class="text-xs font-semibold px-2 py-1 rounded bg-green-200 text-green-800">
                                    Correct Answer
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Answer for Short Answer -->
                    <div v-else-if="question.question_type === 'ShortAnswer'" class="mt-4">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-600 mb-2">Student's Answer:</p>
                            <p class="text-gray-900">{{ question.student_text_answer || 'No answer provided' }}</p>
                        </div>
                        <p class="mt-2 text-sm text-amber-600">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            This is a short answer question. Please review manually.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </InstructorLayout>
</template>
