<template>
    <Head :title="`Taking: ${quiz.title}`" />
    
    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation Bar -->
        <nav class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="text-2xl font-bold text-primary-500">QuickLearn</div>
                    <div class="flex items-center space-x-6">
                        <!-- Timer -->
                        <div v-if="quiz.time_limit" 
                             class="flex items-center px-4 py-2 rounded-lg transition-colors"
                             :class="timeRemaining <= 30 ? 'bg-red-100 border-2 border-red-500' : 'bg-orange-100 border-2 border-orange-400'">
                            <svg class="w-5 h-5 mr-2" 
                                 :class="timeRemaining <= 30 ? 'text-red-600' : 'text-orange-600'" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-bold text-lg" 
                                  :class="timeRemaining <= 30 ? 'text-red-700' : 'text-orange-800'">
                                {{ formatTime(timeRemaining) }}
                            </span>
                        </div>
                        <div class="text-sm font-medium text-gray-700">Question {{ currentQuestionIndex + 1 }} / {{ questions.length }}</div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="pt-16">
            <div class="max-w-5xl mx-auto py-8 px-6">
                <!-- Progress Bar -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progress</span>
                        <span class="text-sm font-medium text-primary-600">{{ answeredCount }} / {{ questions.length }} answered</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-primary-500 h-2 rounded-full transition-all" :style="{ width: ((answeredCount / questions.length) * 100) + '%' }"></div>
                    </div>
                </div>

                <!-- Question Card -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 mb-6">
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-700">
                                Question {{ currentQuestionIndex + 1 }}
                            </span>
                            <span class="text-sm text-gray-600">{{ currentQuestion.points }} {{ currentQuestion.points === 1 ? 'point' : 'points' }}</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ currentQuestion.question_text }}</h2>
                    </div>

                    <!-- Answer Options -->
                    <div class="space-y-3">
                        <div 
                            v-for="(answer, index) in currentQuestion.answers" 
                            :key="answer.id"
                            @click="selectAnswer(answer.id)"
                            class="relative p-5 border-2 rounded-xl cursor-pointer transition-all"
                            :class="selectedAnswer === answer.id ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-primary-300 hover:bg-gray-50'"
                        >
                            <label class="flex items-start cursor-pointer">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                         :class="selectedAnswer === answer.id ? 'border-primary-600 bg-primary-600' : 'border-gray-300'">
                                        <div v-if="selectedAnswer === answer.id" class="w-3 h-3 rounded-full bg-white"></div>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <span class="text-gray-900 font-medium text-lg">
                                        {{ String.fromCharCode(65 + index) }}. {{ answer.answer_text }}
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex items-center justify-between mb-6">
                    <button 
                        v-if="currentQuestionIndex > 0"
                        @click="previousQuestion"
                        class="flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-primary-500 hover:text-primary-600 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Previous
                    </button>
                    <div v-else></div>

                    <button 
                        v-if="currentQuestionIndex < questions.length - 1"
                        @click="nextQuestion"
                        class="flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition"
                    >
                        Next
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button 
                        v-else
                        @click="showSubmitConfirmation = true"
                        class="px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
                    >
                        Review & Submit
                    </button>
                </div>

                <!-- Question Navigation Grid -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Question Overview</h3>

                    <div class="grid grid-cols-5 sm:grid-cols-10 gap-2 mb-4">
                        <button 
                            v-for="(question, index) in questions" 
                            :key="question.id"
                            @click="goToQuestion(index)"
                            class="aspect-square flex items-center justify-center rounded-lg font-semibold text-sm transition"
                            :class="getQuestionNavClass(index)"
                        >
                            {{ index + 1 }}
                        </button>
                    </div>

                    <div class="flex items-center justify-center space-x-6 text-sm pt-4 border-t border-gray-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-primary-600 rounded-lg mr-2"></div>
                            <span class="text-gray-600">Current</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 border-2 border-green-500 rounded-lg mr-2"></div>
                            <span class="text-gray-600">Answered</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-100 border-2 border-gray-300 rounded-lg mr-2"></div>
                            <span class="text-gray-600">Unanswered</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Confirmation Modal -->
        <Teleport to="body">
            <div v-if="showSubmitConfirmation" class="fixed inset-0 z-50 overflow-y-auto" @click="showSubmitConfirmation = false">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div @click.stop class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-6 py-6">
                            <div class="flex items-start mb-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Submit Quiz?</h3>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p>You have answered <span class="font-semibold text-gray-900">{{ answeredCount }}</span> out of <span class="font-semibold text-gray-900">{{ questions.length }}</span> questions.</p>
                                        <p v-if="answeredCount < questions.length" class="text-yellow-700 font-medium">
                                            ⚠️ Warning: {{ questions.length - answeredCount }} question(s) remain unanswered!
                                        </p>
                                        <p class="mt-4">Once submitted, you cannot change your answers.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex space-x-3 mt-6">
                                <button
                                    @click="showSubmitConfirmation = false"
                                    class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition"
                                >
                                    Review Answers
                                </button>
                                <button
                                    @click="submitQuiz"
                                    :disabled="submitting"
                                    class="flex-1 px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{ submitting ? 'Submitting...' : 'Confirm Submit' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';

const page = usePage();

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

const currentQuestionIndex = ref(0);
const selectedAnswer = ref(null);
const userAnswers = ref({});
const timeRemaining = ref(props.quiz.time_limit ? props.quiz.time_limit * 60 : 0);
const submitting = ref(false);
const showSubmitConfirmation = ref(false);
const startTime = ref(Date.now()); // Track when quiz started
let timerInterval = null;

const currentQuestion = computed(() => props.questions[currentQuestionIndex.value]);
const answeredCount = computed(() => Object.keys(userAnswers.value).length);

onMounted(() => {
    // Load selected answer for current question
    if (userAnswers.value[currentQuestion.value.id]) {
        selectedAnswer.value = userAnswers.value[currentQuestion.value.id];
    }

    // Start timer if quiz has time limit
    if (props.quiz.time_limit) {
        timerInterval = setInterval(() => {
            if (timeRemaining.value > 0) {
                timeRemaining.value--;
            } else {
                // Auto-submit when time runs out
                clearInterval(timerInterval);
                submitQuiz();
            }
        }, 1000);
    }
});

onUnmounted(() => {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
});

const selectAnswer = (answerId) => {
    selectedAnswer.value = answerId;
    userAnswers.value[currentQuestion.value.id] = answerId;
};

const nextQuestion = () => {
    if (currentQuestionIndex.value < props.questions.length - 1) {
        currentQuestionIndex.value++;
        selectedAnswer.value = userAnswers.value[currentQuestion.value.id] || null;
    }
};

const previousQuestion = () => {
    if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--;
        selectedAnswer.value = userAnswers.value[currentQuestion.value.id] || null;
    }
};

const goToQuestion = (index) => {
    currentQuestionIndex.value = index;
    selectedAnswer.value = userAnswers.value[currentQuestion.value.id] || null;
};

const submitQuiz = () => {
    submitting.value = true;
    
    // Calculate time spent in seconds
    const timeSpentSeconds = Math.floor((Date.now() - startTime.value) / 1000);
    
    // Get client's local time formatted
    const now = new Date();
    const options = { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true };
    const clientSubmittedAt = now.toLocaleDateString('en-US', options).replace(',', ' at');
    
    // Format answers for submission
    const formattedAnswers = Object.entries(userAnswers.value).map(([questionId, answerId]) => ({
        question_id: parseInt(questionId),
        answer_id: answerId
    }));

    router.post(route('student.quizzes.submit', props.attempt.id), {
        answers: formattedAnswers,
        time_spent_seconds: timeSpentSeconds,
        submitted_at_formatted: clientSubmittedAt
    }, {
        onError: () => {
            submitting.value = false;
        }
    });
};

const getQuestionNavClass = (index) => {
    if (index === currentQuestionIndex.value) {
        return 'bg-primary-600 text-white';
    }
    if (userAnswers.value[props.questions[index].id]) {
        return 'bg-green-100 border-2 border-green-500 text-green-700';
    }
    return 'bg-gray-100 border-2 border-gray-300 text-gray-600 hover:border-primary-300 hover:bg-gray-50';
};

const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};
</script>
