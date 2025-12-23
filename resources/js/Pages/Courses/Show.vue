<template>
    <StudentLayout>
        <Head :title="course.title" />

        <div class="max-w-7xl mx-auto">
            <!-- Course Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="p-8 md:p-12">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="text-white">
                            <Badge variant="default" class="bg-white bg-opacity-20 text-white border-0 mb-4">
                                {{ course.category }}
                            </Badge>
                            <h1 class="text-4xl font-bold mb-4">{{ course.title }}</h1>
                            <p class="text-lg opacity-90 mb-6">{{ course.description }}</p>

                            <!-- Course Meta -->
                            <div class="flex flex-wrap items-center gap-6 mb-6">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ course.duration || '10 hours' }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ course.students_count || 0 }} students
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    {{ course.difficulty || 'Beginner' }}
                                </div>
                            </div>

                            <!-- Progress Bar (if enrolled) -->
                            <div v-if="course.is_enrolled" class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm">Your Progress</span>
                                    <span class="text-sm font-semibold">{{ course.progress }}%</span>
                                </div>
                                <div class="w-full bg-white bg-opacity-20 rounded-full h-3">
                                    <div 
                                        class="bg-white h-3 rounded-full transition-all duration-300"
                                        :style="{ width: `${course.progress}%` }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <button 
                                v-if="!course.is_enrolled"
                                @click="enrollCourse"
                                :disabled="enrolling"
                                class="px-8 py-3 bg-white text-blue-600 font-bold rounded-lg hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ enrolling ? 'Enrolling...' : 'Enroll in Course' }}
                            </button>
                            <button 
                                v-else
                                @click="continueLearning"
                                class="px-8 py-3 bg-white text-blue-600 font-bold rounded-lg hover:bg-gray-100 transition"
                            >
                                {{ course.progress > 0 ? 'Continue Learning' : 'Start Course' }}
                            </button>
                        </div>

                        <!-- Right Column - Thumbnail -->
                        <div class="hidden lg:block">
                            <div class="relative h-full min-h-[300px] bg-white bg-opacity-10 rounded-xl overflow-hidden backdrop-blur-sm">
                                <img 
                                    v-if="course.thumbnail" 
                                    :src="course.thumbnail" 
                                    :alt="course.title"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="flex items-center justify-center h-full">
                                    <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Course Description -->
                    <Card>
                        <template #header>
                            <h2 class="text-2xl font-bold text-gray-900">About This Course</h2>
                        </template>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 leading-relaxed">
                                {{ course.full_description || course.description }}
                            </p>
                        </div>
                    </Card>

                    <!-- Lessons List -->
                    <Card>
                        <template #header>
                            <h2 class="text-2xl font-bold text-gray-900">Course Content</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ lessons.length }} lessons • {{ course.total_duration || 'Self-paced' }}
                            </p>
                        </template>

                        <div class="space-y-2">
                            <div 
                                v-for="(lesson, index) in lessons" 
                                :key="lesson.id"
                                class="border border-gray-200 rounded-lg hover:border-blue-300 transition"
                            >
                                <button 
                                    @click="toggleLesson(lesson.id)"
                                    class="w-full px-6 py-4 flex items-center justify-between text-left"
                                >
                                    <div class="flex items-center flex-1">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center mr-4 flex-shrink-0"
                                             :class="lesson.is_completed ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'">
                                            <svg v-if="lesson.is_completed" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span v-else class="text-sm font-semibold">{{ index + 1 }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ lesson.title }}</h3>
                                            <div class="flex items-center mt-1 text-sm text-gray-500 space-x-4">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ lesson.duration || '15 min' }}
                                                </span>
                                                <Badge v-if="lesson.type === 'video'" variant="info" class="text-xs">Video</Badge>
                                                <Badge v-else-if="lesson.type === 'quiz'" variant="warning" class="text-xs">Quiz</Badge>
                                                <Badge v-else variant="default" class="text-xs">Reading</Badge>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <svg 
                                            class="w-5 h-5 text-gray-400 transition-transform"
                                            :class="{ 'rotate-180': expandedLesson === lesson.id }"
                                            fill="none" 
                                            stroke="currentColor" 
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </button>

                                <!-- Lesson Details (Expandable) -->
                                <div 
                                    v-show="expandedLesson === lesson.id"
                                    class="px-6 pb-4 border-t border-gray-200"
                                >
                                    <p class="text-sm text-gray-600 mt-4 mb-4">{{ lesson.description }}</p>
                                    <Link 
                                        v-if="course.is_enrolled"
                                        :href="route('lessons.show', lesson.id)"
                                        class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        {{ lesson.is_completed ? 'Review Lesson' : 'Start Lesson' }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </Link>
                                    <span v-else class="text-sm text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Enroll to access this lesson
                                    </span>
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Course Stats -->
                    <Card>
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900">Course Stats</h3>
                        </template>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Lessons</span>
                                <span class="font-semibold text-gray-900">{{ lessons.length }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Duration</span>
                                <span class="font-semibold text-gray-900">{{ course.duration || '10 hours' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Level</span>
                                <Badge :variant="getDifficultyVariant(course.difficulty)">
                                    {{ course.difficulty || 'Beginner' }}
                                </Badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Enrolled</span>
                                <span class="font-semibold text-gray-900">{{ course.students_count || 0 }} students</span>
                            </div>
                        </div>
                    </Card>

                    <!-- What You'll Learn -->
                    <Card>
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900">What You'll Learn</h3>
                        </template>
                        <ul class="space-y-3">
                            <li v-for="(point, index) in learningPoints" :key="index" class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm text-gray-700">{{ point }}</span>
                            </li>
                        </ul>
                    </Card>

                    <!-- Instructor -->
                    <Card v-if="course.instructor">
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900">Instructor</h3>
                        </template>
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xl font-bold">
                                {{ course.instructor.name?.charAt(0).toUpperCase() }}
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-900">{{ course.instructor.name }}</h4>
                                <p class="text-sm text-gray-600">{{ course.instructor.title || 'Course Instructor' }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-700">
                            {{ course.instructor.bio || 'Expert instructor with years of experience in the field.' }}
                        </p>
                    </Card>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    course: {
        type: Object,
        required: true
    },
    lessons: {
        type: Array,
        default: () => []
    },
    learningPoints: {
        type: Array,
        default: () => [
            'Master the fundamentals of the subject',
            'Build practical, real-world projects',
            'Gain hands-on experience',
            'Learn best practices and industry standards'
        ]
    }
});

const expandedLesson = ref(null);
const enrolling = ref(false);

const toggleLesson = (lessonId) => {
    expandedLesson.value = expandedLesson.value === lessonId ? null : lessonId;
};

const enrollCourse = () => {
    enrolling.value = true;
    router.post(route('courses.enroll', props.course.id), {}, {
        onFinish: () => {
            enrolling.value = false;
        }
    });
};

const continueLearning = () => {
    const nextLesson = props.lessons.find(lesson => !lesson.is_completed);
    if (nextLesson) {
        router.visit(route('lessons.show', nextLesson.id));
    } else {
        router.visit(route('lessons.show', props.lessons[0].id));
    }
};

const getDifficultyVariant = (difficulty) => {
    const variants = {
        'Beginner': 'success',
        'Intermediate': 'warning',
        'Advanced': 'danger'
    };
    return variants[difficulty] || 'default';
};
</script>
