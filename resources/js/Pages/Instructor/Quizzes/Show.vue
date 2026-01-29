<script setup>
import { Head, router } from '@inertiajs/vue3';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';

const props = defineProps({
    quiz: Object,
    attempts: Array
});

const editQuiz = () => {
    router.visit(route('instructor.quizzes.edit', props.quiz.id));
};

const deleteQuiz = () => {
    if (confirm('Are you sure you want to delete this quiz?')) {
        router.delete(route('instructor.quizzes.destroy', props.quiz.id));
    }
};

const gradeAttempt = (attemptId) => {
    router.visit(route('instructor.quizzes.grade', attemptId));
};
</script>

<template>
    <Head :title="quiz.title" />

    <InstructorLayout>
        <div class="max-w-5xl">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ quiz.title }}</h1>
                        <p class="mt-2 text-gray-600">{{ quiz.course_title }} / {{ quiz.lesson_title }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <button
                            @click="editQuiz"
                            class="rounded-lg bg-blue-500 px-6 py-2 text-white font-medium hover:bg-blue-600 transition"
                        >
                            Edit Quiz
                        </button>
                        <button
                            @click="deleteQuiz"
                            class="rounded-lg bg-red-500 px-6 py-2 text-white font-medium hover:bg-red-600 transition"
                        >
                            Delete Quiz
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quiz Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <p class="text-gray-600 text-sm mb-2">Questions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ quiz.questions_count }}</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <p class="text-gray-600 text-sm mb-2">Time Limit</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ quiz.time_limit ? quiz.time_limit + ' min' : 'No limit' }}
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <p class="text-gray-600 text-sm mb-2">Passing Score</p>
                    <p class="text-2xl font-bold text-gray-900">{{ quiz.passing_score }}%</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <p class="text-gray-600 text-sm mb-2">Total Attempts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ quiz.attempts_count }}</p>
                </div>
            </div>

            <!-- Description -->
            <div v-if="quiz.description" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-3">Description</h2>
                <p class="text-gray-700">{{ quiz.description }}</p>
            </div>

            <!-- Student Attempts -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-white p-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        Student Attempts ({{ attempts.length }})
                    </h2>
                </div>

                <div v-if="attempts.length === 0" class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No attempts yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Students haven't taken this quiz yet.</p>
                </div>

                <div v-else>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Score
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Submitted
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Time Taken
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="attempt in attempts" :key="attempt.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ attempt.student_name }}</div>
                                            <div class="text-sm text-gray-500">{{ attempt.student_email }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold" :class="attempt.passed ? 'text-green-600' : 'text-red-600'">
                                            {{ attempt.score }}%
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="attempt.passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        >
                                            {{ attempt.passed ? 'Passed' : 'Failed' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ attempt.submitted_at || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ attempt.time_taken ? attempt.time_taken + ' min' : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button
                                            @click="gradeAttempt(attempt.id)"
                                            class="text-primary-600 hover:text-primary-900"
                                        >
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </InstructorLayout>
</template>
