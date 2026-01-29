<template>
    <StudentLayout>
        <Head :title="`Quiz: ${quiz.title}`" />

        <div class="max-w-4xl mx-auto">
            <!-- Back to Dashboard -->
            <div class="mb-4">
                <Link 
                    :href="route('dashboard')"
                    class="px-6 py-2 bg-cyan-500 text-white font-semibold rounded-lg hover:bg-cyan-600 transition flex items-center space-x-2 w-fit"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </Link>
            </div>

            <!-- Quiz Header -->
            <Card class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ quiz.title }}</h1>
                        <p class="text-gray-600">{{ course.title }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Question</div>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ currentQuestionIndex + 1 }} / {{ questions.length }}
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6">
                    <ProgressBar 
                        :progress="Math.round(((currentQuestionIndex + 1) / questions.length) * 100)" 
                        :show-label="false"
                    />
                </div>

                <!-- Timer (Optional) -->
                <div v-if="quiz.time_limit" class="mt-4 flex items-center justify-center">
                    <div class="flex items-center px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold text-yellow-800">
                            Time Remaining: {{ formatTime(timeRemaining) }}
                        </span>
                    </div>
                </div>
            </Card>

            <!-- Current Question -->
            <Card class="mb-6">
                <template #header>
                    <h2 class="text-xl font-bold text-gray-900">
                        Question {{ currentQuestionIndex + 1 }}
                    </h2>
                </template>

                <div class="mb-6">
                    <p class="text-lg text-gray-800 leading-relaxed">
                        {{ currentQuestion.question }}
                    </p>
                </div>

                <!-- Answer Options -->
                <div class="space-y-3">
                    <div 
                        v-for="(answer, index) in currentQuestion.answers" 
                        :key="answer.id"
                        @click="selectAnswer(answer.id)"
                        class="relative p-4 border-2 rounded-lg cursor-pointer transition-all"
                        :class="getAnswerClass(answer.id)"
                    >
                        <label class="flex items-start cursor-pointer">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                     :class="selectedAnswer === answer.id ? 'border-blue-600 bg-blue-600' : 'border-gray-300'">
                                    <div v-if="selectedAnswer === answer.id" class="w-2 h-2 rounded-full bg-white"></div>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-900 font-medium">
                                        {{ String.fromCharCode(65 + index) }}. {{ answer.answer }}
                                    </span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </Card>

            <!-- Navigation -->
            <Card>
                <div class="flex items-center justify-between">
                    <button 
                        v-if="currentQuestionIndex > 0"
                        @click="previousQuestion"
                        class="flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-blue-500 hover:text-blue-600 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Previous
                    </button>
                    <div v-else></div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            {{ answeredCount }} of {{ questions.length }} answered
                        </p>
                    </div>

                    <button 
                        v-if="currentQuestionIndex < questions.length - 1"
                        @click="nextQuestion"
                        :disabled="!selectedAnswer"
                        class="flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Next
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button 
                        v-else
                        @click="submitQuiz"
                        :disabled="!canSubmit || submitting"
                        class="px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ submitting ? 'Submitting...' : 'Submit Quiz' }}
                    </button>
                </div>
            </Card>

            <!-- Question Navigation Grid -->
            <Card class="mt-6">
                <template #header>
                    <h3 class="text-lg font-bold text-gray-900">Question Overview</h3>
                </template>

                <div class="grid grid-cols-5 sm:grid-cols-10 gap-2">
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

                <div class="mt-4 flex items-center justify-center space-x-6 text-sm">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg mr-2"></div>
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
            </Card>
        </div>
    </StudentLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Card from '@/Components/Card.vue';
import ProgressBar from '@/Components/ProgressBar.vue';

const props = defineProps({
    quiz: {
        type: Object,
        required: true
    },
    course: {
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
const timeRemaining = ref(props.quiz.time_limit || 0);
const submitting = ref(false);
let timerInterval = null;

const currentQuestion = computed(() => props.questions[currentQuestionIndex.value]);

const answeredCount = computed(() => Object.keys(userAnswers.value).length);

const canSubmit = computed(() => answeredCount.value === props.questions.length);

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
    if (!canSubmit.value && timeRemaining.value > 0) {
        if (!confirm('You have not answered all questions. Are you sure you want to submit?')) {
            return;
        }
    }

    submitting.value = true;
    router.post(route('quizzes.submit', props.quiz.id), {
        answers: userAnswers.value
    });
};

const getAnswerClass = (answerId) => {
    if (selectedAnswer.value === answerId) {
        return 'border-blue-500 bg-blue-50';
    }
    return 'border-gray-300 hover:border-blue-300 hover:bg-gray-50';
};

const getQuestionNavClass = (index) => {
    if (index === currentQuestionIndex.value) {
        return 'bg-blue-600 text-white';
    }
    if (userAnswers.value[props.questions[index].id]) {
        return 'bg-green-100 border-2 border-green-500 text-green-700';
    }
    return 'bg-gray-100 border-2 border-gray-300 text-gray-600 hover:border-blue-300';
};

const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};
</script>
