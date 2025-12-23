<template>
    <InstructorLayout>
        <Head title="My Courses" />

        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Courses</h1>
                    <p class="mt-2 text-gray-600">Manage your courses and track student progress</p>
                </div>
                <Link
                    :href="route('instructor.courses.create')"
                    class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition inline-flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create New Course
                </Link>
            </div>

            <div v-if="courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card
                    v-for="course in courses"
                    :key="course.id"
                    class="hover:shadow-xl transition-shadow cursor-pointer overflow-hidden"
                    @click="router.visit(route('instructor.courses.show', course.id))"
                >
                    <!-- Course Thumbnail -->
                    <div class="h-40 bg-gradient-to-br from-blue-500 to-indigo-600 relative -m-6 mb-4">
                        <img
                            v-if="course.thumbnail_url"
                            :src="course.thumbnail_url"
                            :alt="course.title"
                            class="w-full h-full object-cover"
                            @error="$event.target.style.display='none'"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <Badge :variant="course.difficulty === 'Beginner' ? 'success' : course.difficulty === 'Intermediate' ? 'warning' : 'danger'">
                                {{ course.difficulty }}
                            </Badge>
                            <Badge :variant="course.is_published ? 'success' : 'secondary'">
                                {{ course.is_published ? 'Published' : 'Draft' }}
                            </Badge>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ course.title }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ course.short_description }}</p>
                    </div>

                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            {{ course.students_count }} students
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ course.lessons_count }} lessons
                        </div>
                    </div>

                    <div class="flex items-center space-x-2" @click.stop>
                        <Link
                            :href="route('instructor.courses.edit', course.id)"
                            class="flex-1 px-4 py-2 text-center bg-blue-50 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition"
                        >
                            Edit
                        </Link>
                        <button
                            @click="togglePublish(course.id, course.is_published)"
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition"
                        >
                            {{ course.is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                        <button
                            @click="deleteCourse(course.id)"
                            class="px-4 py-2 bg-red-50 text-red-600 font-medium rounded-lg hover:bg-red-100 transition"
                        >
                            Delete
                        </button>
                    </div>
                </Card>
            </div>

            <div v-else class="text-center py-16">
                <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No courses yet</h3>
                <p class="text-gray-500 mb-6">Create your first course to start teaching</p>
                <Link
                    :href="route('instructor.courses.create')"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Your First Course
                </Link>
            </div>
        </div>
    </InstructorLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';

defineProps({
    courses: {
        type: Array,
        default: () => []
    }
});

const togglePublish = (courseId, currentStatus) => {
    router.put(route('instructor.courses.publish', courseId), {
        is_published: !currentStatus
    }, {
        preserveScroll: true
    });
};

const deleteCourse = (courseId) => {
    if (confirm('Are you sure you want to delete this course? This will also delete all lessons and quizzes.')) {
        router.delete(route('instructor.courses.destroy', courseId));
    }
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
