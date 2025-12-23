<template>
    <StudentLayout>
        <Head title="Quiz Results" />

        <div class="max-w-4xl mx-auto">
            <!-- Results Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-6"
                     :class="isPassed ? 'bg-green-100' : 'bg-red-100'">
                    <svg v-if="isPassed" class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    {{ isPassed ? 'Congratulations!' : 'Keep Trying!' }}
                </h1>
                <p class="text-xl text-gray-600">{{ quiz.title }}</p>
            </div>

            <!-- Score Card -->
            <Card class="mb-8">
                <div class="text-center py-8">
                    <div class="mb-6">
                        <div class="inline-flex items-baseline">
                            <span class="text-6xl font-bold" :class="isPassed ? 'text-green-600' : 'text-red-600'">
                                {{ result.score }}
                            </span>
                            <span class="text-3xl text-gray-400 ml-2">/ {{ result.total_score }}</span>
                        </div>
                        <p class="text-xl text-gray-600 mt-2">Your Score</p>
                    </div>

                    <div class="mb-6">
                        <div class="text-5xl font-bold" :class="isPassed ? 'text-green-600' : 'text-red-600'">
                            {{ percentage }}%
                        </div>
                        <p class="text-gray-600 mt-2">Percentage</p>
                    </div>

                    <div class="max-w-md mx-auto">
                        <ProgressBar 
                            :progress="percentage" 
                            :color="isPassed ? 'green' : 'red'"
                            :show-label="false"
                        />
                    </div>
                </div>
            </Card>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <Card padding>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ result.correct_answers }}</div>
                        <div class="text-sm text-gray-600 mt-1">Correct</div>
                    </div>
                </Card>

                <Card padding>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ result.wrong_answers }}</div>
                        <div class="text-sm text-gray-600 mt-1">Incorrect</div>
                    </div>
                </Card>

                <Card padding>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ result.time_taken }}</div>
                        <div class="text-sm text-gray-600 mt-1">Time Taken</div>
                    </div>
                </Card>
            </div>

            <!-- Pass/Fail Message -->
            <Card class="mb-8" :class="isPassed ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                             :class="isPassed ? 'bg-green-100' : 'bg-red-100'">
                            <svg v-if="isPassed" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg v-else class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold" :class="isPassed ? 'text-green-900' : 'text-red-900'">
                            {{ isPassed ? 'You passed this quiz!' : 'You did not pass this quiz' }}
                        </h3>
                        <p class="mt-1 text-sm" :class="isPassed ? 'text-green-700' : 'text-red-700'">
                            {{ isPassed 
                                ? 'Great job! You have successfully completed this quiz and can move forward.' 
                                : `You need ${quiz.passing_score}% to pass. Review the material and try again.` 
                            }}
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Question Review -->
            <Card class="mb-8">
                <template #header>
                    <h2 class="text-xl font-bold text-gray-900">Question Review</h2>
                </template>

                <div class="space-y-6">
                    <div 
                        v-for="(question, index) in result.questions" 
                        :key="question.id"
                        class="pb-6 border-b border-gray-200 last:border-b-0 last:pb-0"
                    >
                        <div class="flex items-start mb-4">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                 :class="question.is_correct ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                <svg v-if="question.is_correct" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">
                                    Question {{ index + 1 }}: {{ question.question }}
                                </h4>
                                
                                <!-- User's Answer -->
                                <div class="mb-2">
                                    <span class="text-sm font-medium text-gray-700">Your answer: </span>
                                    <span :class="question.is_correct ? 'text-green-600' : 'text-red-600'">
                                        {{ question.user_answer }}
                                    </span>
                                </div>

                                <!-- Correct Answer (if wrong) -->
                                <div v-if="!question.is_correct" class="mb-2">
                                    <span class="text-sm font-medium text-gray-700">Correct answer: </span>
                                    <span class="text-green-600 font-semibold">
                                        {{ question.correct_answer }}
                                    </span>
                                </div>

                                <!-- Explanation (if available) -->
                                <div v-if="question.explanation" class="mt-2 p-3 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-blue-900">
                                        <span class="font-semibold">Explanation:</span> {{ question.explanation }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>

            <!-- Actions -->
            <Card>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link 
                        :href="route('courses.show', course.id)"
                        class="w-full sm:w-auto px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-blue-500 hover:text-blue-600 transition text-center"
                    >
                        Back to Course
                    </Link>

                    <button 
                        v-if="quiz.allow_retake && !isPassed"
                        @click="retakeQuiz"
                        class="w-full sm:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                    >
                        Retake Quiz
                    </button>

                    <Link 
                        v-if="isPassed && nextLesson"
                        :href="route('lessons.show', nextLesson.id)"
                        class="w-full sm:w-auto px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition text-center"
                    >
                        Continue to Next Lesson
                    </Link>
                </div>
            </Card>
        </div>
    </StudentLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
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
    result: {
        type: Object,
        required: true
    },
    nextLesson: {
        type: Object,
        default: null
    }
});

const percentage = computed(() => {
    return Math.round((props.result.score / props.result.total_score) * 100);
});

const isPassed = computed(() => {
    return percentage.value >= (props.quiz.passing_score || 70);
});

const retakeQuiz = () => {
    router.visit(route('quizzes.take', props.quiz.id));
};
</script>
