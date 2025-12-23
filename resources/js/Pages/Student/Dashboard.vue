<template>
    <StudentLayout>
        <Head title="Dashboard" />

        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Welcome Section -->
            <div class="mb-10">
                <h1 class="text-4xl font-black text-gray-900 mb-3">
                    Welcome back, {{ $page.props.auth.user.name }}!
                </h1>
                <p class="text-lg text-gray-600">Continue your learning journey</p>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                <Card padding>
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Enrolled Courses</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.enrolled_courses }}</p>
                        </div>
                    </div>
                </Card>

                <Card padding>
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Completed</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.completed_courses }}</p>
                        </div>
                    </div>
                </Card>

                <Card padding>
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Hours Learned</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_hours }}</p>
                        </div>
                    </div>
                </Card>

                <Card padding>
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Certificates</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.certificates }}</p>
                        </div>
                    </div>
                </Card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Continue Learning Section -->
                <div class="lg:col-span-2">
                    <Card>
                        <template #header>
                            <h2 class="text-xl font-bold text-gray-900">Continue Learning</h2>
                        </template>

                        <div v-if="continueLesson" class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg p-6 text-white">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm opacity-90 mb-2">{{ continueLesson.course_title }}</p>
                                    <h3 class="text-xl font-bold mb-4">{{ continueLesson.lesson_title }}</h3>
                                    <ProgressBar 
                                        :progress="continueLesson.progress" 
                                        :show-label="false"
                                        color="white"
                                    />
                                    <Link 
                                        :href="route('student.courses.show', continueLesson.course_id)"
                                        class="inline-block mt-4 px-6 py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition"
                                    >
                                        Continue
                                    </Link>
                                </div>
                                <div class="ml-4">
                                    <svg class="w-16 h-16 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                    <div v-else class="text-center py-16 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border-2 border-dashed border-blue-200">
                        <svg class="w-20 h-20 mx-auto mb-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p>No courses in progress</p>
                            <Link 
                                :href="route('student.courses.browse')"
                                class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium"
                            >
                                Browse Courses
                            </Link>
                        </div>
                    </Card>

                    <!-- My Courses -->
                    <Card class="mt-8">
                        <template #header>
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-900">My Courses</h2>
                                <Link 
                                    :href="route('student.courses.browse')"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                                >
                                    View All
                                </Link>
                            </div>
                        </template>

                        <div v-if="enrolledCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <Link
                                v-for="course in enrolledCourses" 
                                :key="course.id"
                                :href="route('student.courses.show', course.id)"
                                class="bg-white rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300 cursor-pointer border border-gray-100 hover:-translate-y-1 shadow-lg"
                            >
                                <!-- Course Thumbnail -->
                                <div class="h-40 bg-gradient-to-br from-blue-400 to-blue-600 relative">
                                    <img
                                        v-if="course.thumbnail_url"
                                        :src="course.thumbnail_url"
                                        :alt="course.title"
                                        class="w-full h-full object-cover"
                                        @error="$event.target.style.display='none'"
                                    />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                </div>
                                <!-- Course Info -->
                                <div class="p-6">
                                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ course.title }}</h3>
                                    <Badge :variant="course.progress === 100 ? 'success' : 'primary'">
                                        {{ course.progress }}% Complete
                                    </Badge>
                                    <div class="mt-3">
                                        <ProgressBar :progress="course.progress" :show-label="false" />
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <div v-else class="text-center py-8 text-gray-500">
                            <p>You haven't enrolled in any courses yet</p>
                            <Link 
                                :href="route('student.courses.browse')"
                                class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                            >
                                Browse Courses
                            </Link>
                        </div>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Progress Summary -->
                    <Card>
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900">Progress Summary</h3>
                        </template>

                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Overall Progress</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ stats.overall_progress }}%</span>
                                </div>
                                <ProgressBar :progress="stats.overall_progress" :show-label="false" />
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-600">Completed Lessons</span>
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ stats.completed_lessons }} / {{ stats.total_lessons }}
                                    </span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-600">Quiz Score Average</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ stats.quiz_average }}%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Learning Streak</span>
                                    <span class="text-sm font-semibold text-blue-600">{{ stats.learning_streak }} days</span>
                                </div>
                            </div>
                        </div>
                    </Card>

                    <!-- Recent Certificates -->
                    <Card>
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900">Recent Certificates</h3>
                        </template>

                        <div v-if="recentCertificates.length > 0" class="space-y-3">
                            <div 
                                v-for="cert in recentCertificates" 
                                :key="cert.id"
                                class="flex items-center p-3 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg border border-amber-200"
                            >
                                <svg class="w-8 h-8 text-amber-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ cert.course_title }}</p>
                                    <p class="text-xs text-gray-600">{{ cert.issued_date }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-6 text-gray-500 text-sm">
                            No certificates yet. Complete a course to earn one!
                        </div>
                    </Card>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Card from '@/Components/Card.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import Badge from '@/Components/Badge.vue';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            enrolled_courses: 0,
            completed_courses: 0,
            total_hours: 0,
            certificates: 0,
            overall_progress: 0,
            completed_lessons: 0,
            total_lessons: 0,
            quiz_average: 0,
            learning_streak: 0
        })
    },
    continueLesson: {
        type: Object,
        default: null
    },
    enrolledCourses: {
        type: Array,
        default: () => []
    },
    recentCertificates: {
        type: Array,
        default: () => []
    }
});
</script>
