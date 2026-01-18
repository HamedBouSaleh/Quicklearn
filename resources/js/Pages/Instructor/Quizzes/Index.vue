<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    lesson: Object,
    quizzes: Array
});

const showDeleteModal = ref(false);
const quizToDelete = ref(null);

const createQuiz = () => {
    router.visit(route('instructor.quizzes.create', props.lesson.id));
};

const viewQuiz = (quizId) => {
    router.visit(route('instructor.quizzes.show', quizId));
};

const editQuiz = (quizId) => {
    router.visit(route('instructor.quizzes.edit', quizId));
};

const confirmDelete = (quiz) => {
    quizToDelete.value = quiz;
    showDeleteModal.value = true;
};

const deleteQuiz = () => {
    if (quizToDelete.value) {
        router.delete(route('instructor.quizzes.destroy', quizToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false;
                quizToDelete.value = null;
            }
        });
    }
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    quizToDelete.value = null;
};

const backToCourse = () => {
    router.visit(route('instructor.courses.show', props.lesson.course_id));
};
</script>

<template>
    <Head :title="`Quizzes for ${lesson.title}`" />

    <InstructorLayout>
        <div class="max-w-5xl">
            <!-- Header -->
            <div class="mb-8">
                <button 
                    @click="backToCourse"
                    class="mb-4 text-primary-600 hover:text-primary-700 flex items-center text-sm font-medium"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Course
                </button>
                
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Quizzes for {{ lesson.title }}</h1>
                        <p class="mt-2 text-gray-600">{{ lesson.course_title }}</p>
                    </div>
                    <button
                        @click="createQuiz"
                        class="flex items-center rounded-lg bg-primary-500 px-6 py-2 text-white font-medium hover:bg-primary-600 transition"
                    >
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Quiz
                    </button>
                </div>
            </div>

            <!-- Quizzes List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-white p-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        All Quizzes ({{ quizzes.length }})
                    </h2>
                </div>

                <div v-if="quizzes.length === 0" class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No quizzes yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first quiz for this lesson.</p>
                    <div class="mt-6">
                        <button
                            @click="createQuiz"
                            class="inline-flex items-center rounded-lg bg-primary-500 px-4 py-2 text-white font-medium hover:bg-primary-600 transition"
                        >
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create First Quiz
                        </button>
                    </div>
                </div>

                <div v-else class="divide-y divide-gray-200">
                    <div v-for="quiz in quizzes" :key="quiz.id" class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900">{{ quiz.title }}</h4>
                                        <p v-if="quiz.description" class="mt-1 text-sm text-gray-600">{{ quiz.description }}</p>
                                        
                                        <div class="mt-3 flex items-center space-x-6 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ quiz.questions_count }} Question{{ quiz.questions_count !== 1 ? 's' : '' }}
                                            </span>
                                            
                                            <span v-if="quiz.time_limit" class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ quiz.time_limit }} min
                                            </span>
                                            
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                </svg>
                                                {{ quiz.passing_score }}% to pass
                                            </span>
                                            
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                                {{ quiz.attempts_count }} Attempt{{ quiz.attempts_count !== 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                        
                                        <p v-if="quiz.created_at" class="mt-2 text-xs text-gray-500">
                                            Created on {{ quiz.created_at }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <button
                                    @click="viewQuiz(quiz.id)"
                                    class="rounded-lg bg-primary-50 px-4 py-2 text-sm font-medium text-primary-600 hover:bg-primary-100 transition"
                                >
                                    View
                                </button>
                                <button
                                    @click="editQuiz(quiz.id)"
                                    class="rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-100 transition"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="confirmDelete(quiz)"
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

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="cancelDelete">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Delete Quiz</h3>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Are you sure you want to delete "{{ quizToDelete?.title }}"? This action cannot be undone and all student attempts will be lost.
                </p>

                <div class="flex space-x-3">
                    <button
                        @click="cancelDelete"
                        type="button"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteQuiz"
                        type="button"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition"
                    >
                        Delete Quiz
                    </button>
                </div>
            </div>
        </Modal>
    </InstructorLayout>
</template>
