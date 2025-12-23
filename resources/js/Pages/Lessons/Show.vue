<template>
    <StudentLayout>
        <Head :title="lesson.title" />

        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Main Lesson Content -->
                <div class="lg:col-span-3">
                    <!-- Lesson Header -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <Link 
                                :href="route('courses.show', course.id)"
                                class="flex items-center text-blue-600 hover:text-blue-800 font-medium"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Course
                            </Link>
                            <Badge :variant="lesson.is_completed ? 'success' : 'default'">
                                {{ lesson.is_completed ? 'Completed' : 'In Progress' }}
                            </Badge>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ lesson.title }}</h1>
                        <p class="text-gray-600">{{ course.title }}</p>
                    </div>

                    <!-- Video or Content -->
                    <Card class="mb-6">
                        <div v-if="lesson.type === 'video'" class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                            <video 
                                v-if="lesson.video_url"
                                controls 
                                class="w-full h-full"
                                :src="lesson.video_url"
                                @ended="onVideoEnded"
                            >
                                Your browser does not support the video tag.
                            </video>
                            <div v-else class="flex items-center justify-center h-full text-white">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p>Video content will be available soon</p>
                                </div>
                            </div>
                        </div>

                        <!-- Text Content -->
                        <div v-else class="prose max-w-none p-6">
                            <div v-html="lesson.content || '<p>Lesson content will be available here.</p>'"></div>
                        </div>
                    </Card>

                    <!-- Lesson Description -->
                    <Card class="mb-6">
                        <template #header>
                            <h2 class="text-xl font-bold text-gray-900">About This Lesson</h2>
                        </template>
                        <p class="text-gray-700">{{ lesson.description || 'Complete this lesson to continue your learning journey.' }}</p>
                    </Card>

                    <!-- Navigation & Actions -->
                    <Card>
                        <div class="flex items-center justify-between">
                            <button 
                                v-if="previousLesson"
                                @click="navigateToLesson(previousLesson.id)"
                                class="flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-blue-500 hover:text-blue-600 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous Lesson
                            </button>
                            <div v-else></div>

                            <button 
                                v-if="!lesson.is_completed"
                                @click="markAsCompleted"
                                :disabled="completing"
                                class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ completing ? 'Saving...' : 'Mark as Completed' }}
                            </button>

                            <button 
                                v-if="nextLesson"
                                @click="navigateToLesson(nextLesson.id)"
                                class="flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                            >
                                Next Lesson
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <Link 
                                v-else-if="lesson.is_completed"
                                :href="route('courses.show', course.id)"
                                class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                            >
                                Back to Course
                            </Link>
                        </div>
                    </Card>
                </div>

                <!-- Sidebar - Lesson Overview -->
                <div class="lg:col-span-1">
                    <Card class="sticky top-6">
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900">Lesson Overview</h3>
                        </template>

                        <!-- Progress -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Course Progress</span>
                                <span class="text-sm font-semibold text-gray-900">{{ courseProgress }}%</span>
                            </div>
                            <ProgressBar :progress="courseProgress" :show-label="false" />
                        </div>

                        <!-- All Lessons -->
                        <div class="space-y-2 max-h-[600px] overflow-y-auto">
                            <div 
                                v-for="(lessonItem, index) in allLessons" 
                                :key="lessonItem.id"
                                @click="navigateToLesson(lessonItem.id)"
                                class="flex items-start p-3 rounded-lg cursor-pointer transition"
                                :class="lessonItem.id === lesson.id ? 'bg-blue-50 border-2 border-blue-500' : 'hover:bg-gray-50 border-2 border-transparent'"
                            >
                                <div class="w-6 h-6 rounded-full flex items-center justify-center mr-3 flex-shrink-0 mt-0.5"
                                     :class="lessonItem.is_completed ? 'bg-green-500 text-white' : lessonItem.id === lesson.id ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600'">
                                    <svg v-if="lessonItem.is_completed" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span v-else class="text-xs font-semibold">{{ index + 1 }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ lessonItem.title }}</h4>
                                    <div class="flex items-center mt-1">
                                        <svg class="w-3 h-3 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-xs text-gray-500">{{ lessonItem.duration || '15 min' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import ProgressBar from '@/Components/ProgressBar.vue';

const props = defineProps({
    lesson: {
        type: Object,
        required: true
    },
    course: {
        type: Object,
        required: true
    },
    allLessons: {
        type: Array,
        default: () => []
    },
    previousLesson: {
        type: Object,
        default: null
    },
    nextLesson: {
        type: Object,
        default: null
    }
});

const completing = ref(false);

const courseProgress = computed(() => {
    const completedCount = props.allLessons.filter(l => l.is_completed).length;
    return Math.round((completedCount / props.allLessons.length) * 100) || 0;
});

const markAsCompleted = () => {
    completing.value = true;
    router.post(route('lessons.complete', props.lesson.id), {}, {
        onFinish: () => {
            completing.value = false;
        }
    });
};

const navigateToLesson = (lessonId) => {
    router.visit(route('lessons.show', lessonId));
};

const onVideoEnded = () => {
    if (!props.lesson.is_completed) {
        markAsCompleted();
    }
};
</script>
