<template>
    <InstructorLayout>
        <Head title="Create Quiz" />

        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <Link :href="route('instructor.courses.show', lesson.course_id)" class="text-primary-600 hover:text-primary-700 font-medium">
                    ← Back to Course
                </Link>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Quiz</h1>
                <p class="text-gray-600 mb-8">For lesson: {{ lesson.title }}</p>

                <form @submit.prevent="submit">
                    <!-- Quiz Details -->
                    <div class="space-y-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quiz Title *</label>
                            <input
                                v-model="form.title"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="e.g., Chapter 1 Quiz"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Brief description of the quiz..."
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Time Limit (minutes)</label>
                                <input
                                    v-model.number="form.time_limit"
                                    type="number"
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Leave empty for no limit"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%) *</label>
                                <input
                                    v-model.number="form.passing_score"
                                    type="number"
                                    required
                                    min="0"
                                    max="100"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="e.g., 70"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input
                                        v-model="form.allow_retake"
                                        type="checkbox"
                                        class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500"
                                    />
                                    <span class="text-sm font-medium text-gray-700">Allow students to retake quiz</span>
                                </label>
                            </div>

                            <div v-if="form.allow_retake">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Attempts</label>
                                <input
                                    v-model.number="form.max_attempts"
                                    type="number"
                                    min="1"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Default: 3 attempts (or specify a number)"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Questions -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Questions</h2>
                            <button
                                type="button"
                                @click="addQuestion"
                                class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition"
                            >
                                + Add Question
                            </button>
                        </div>

                        <div v-if="form.questions.length === 0" class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <p class="text-gray-600 mb-4">No questions added yet</p>
                            <button
                                type="button"
                                @click="addQuestion"
                                class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition"
                            >
                                Add First Question
                            </button>
                        </div>

                        <div v-else class="space-y-6">
                            <div
                                v-for="(question, qIndex) in form.questions"
                                :key="qIndex"
                                class="bg-gray-50 rounded-lg p-6 border border-gray-200"
                            >
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Question {{ qIndex + 1 }}</h3>
                                    <button
                                        type="button"
                                        @click="removeQuestion(qIndex)"
                                        class="text-red-600 hover:text-red-700"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Question Text *</label>
                                        <input
                                            v-model="question.question_text"
                                            type="text"
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            placeholder="Enter your question..."
                                        />
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Question Type *</label>
                                            <select
                                                v-model="question.question_type"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            >
                                                <option value="multiple_choice">Multiple Choice</option>
                                                <option value="true_false">True/False</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Points *</label>
                                            <input
                                                v-model.number="question.points"
                                                type="number"
                                                required
                                                min="1"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                                placeholder="e.g., 10"
                                            />
                                        </div>
                                    </div>

                                    <!-- Answers -->
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <label class="text-sm font-medium text-gray-700">Answer Options *</label>
                                            <button
                                                v-if="question.question_type !== 'true_false'"
                                                type="button"
                                                @click="addAnswer(qIndex)"
                                                class="text-sm text-primary-600 hover:text-primary-700 font-medium"
                                            >
                                                + Add Answer
                                            </button>
                                        </div>

                                        <div class="space-y-2">
                                            <div
                                                v-for="(answer, aIndex) in question.answers"
                                                :key="aIndex"
                                                class="flex items-center gap-3"
                                            >
                                                <input
                                                    v-model="answer.answer_text"
                                                    type="text"
                                                    required
                                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                                    :placeholder="`Answer ${aIndex + 1}`"
                                                />
                                                <label class="flex items-center space-x-2 whitespace-nowrap">
                                                    <input
                                                        type="checkbox"
                                                        v-model="answer.is_correct"
                                                        class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500"
                                                    />
                                                    <span class="text-sm text-gray-700">Correct</span>
                                                </label>
                                                <button
                                                    v-if="question.question_type !== 'true_false' && question.answers.length > 2"
                                                    type="button"
                                                    @click="removeAnswer(qIndex, aIndex)"
                                                    class="text-red-600 hover:text-red-700"
                                                >
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-end space-x-4">
                        <Link
                            :href="route('instructor.courses.show', lesson.course_id)"
                            class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="processing || form.questions.length === 0"
                            class="px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ processing ? 'Creating...' : 'Create Quiz' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </InstructorLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';

const props = defineProps({
    lesson: {
        type: Object,
        required: true
    }
});

const processing = ref(false);

const form = ref({
    title: '',
    description: '',
    time_limit: null,
    passing_score: 70,
    allow_retake: true,
    max_attempts: null,
    questions: []
});

const addQuestion = () => {
    form.value.questions.push({
        question_text: '',
        question_type: 'multiple_choice',
        points: 10,
        answers: [
            { answer_text: '', is_correct: false },
            { answer_text: '', is_correct: false }
        ]
    });
};

const removeQuestion = (index) => {
    if (confirm('Are you sure you want to remove this question?')) {
        form.value.questions.splice(index, 1);
    }
};

const addAnswer = (questionIndex) => {
    form.value.questions[questionIndex].answers.push({
        answer_text: '',
        is_correct: false
    });
};

const removeAnswer = (questionIndex, answerIndex) => {
    form.value.questions[questionIndex].answers.splice(answerIndex, 1);
};

const submit = () => {
    processing.value = true;
    router.post(route('instructor.quizzes.store', props.lesson.id), form.value, {
        onFinish: () => {
            processing.value = false;
        }
    });
};
</script>
