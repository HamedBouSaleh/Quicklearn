<template>
    <StudentLayout>
        <Head :title="lesson.title" />

        <div class="max-w-6xl mx-auto">
            <!-- Breadcrumb -->
            <div class="mb-6 flex items-center text-sm text-gray-600">
                <Link :href="route('student.courses.browse')" class="hover:text-blue-600">Courses</Link>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <Link :href="route('student.courses.show', course.id)" class="hover:text-blue-600">{{ course.title }}</Link>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 font-medium">{{ lesson.title }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <Card>
                        <!-- Lesson Header -->
                        <div class="border-b border-gray-200 pb-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <Badge :variant="lesson.lesson_type === 'Video' ? 'primary' : 'secondary'">
                                    {{ lesson.lesson_type }}
                                </Badge>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ lesson.estimated_duration || 0 }} minutes
                                </div>
                            </div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ lesson.title }}</h1>
                        </div>

                        <!-- Video Content -->
                        <div v-if="lesson.lesson_type === 'Video' && lesson.video_url" class="mb-6">
                            <div class="aspect-w-16 aspect-h-9 bg-black rounded-lg overflow-hidden">
                                <iframe
                                    v-if="isYouTubeUrl(lesson.video_url)"
                                    :src="getYouTubeEmbedUrl(lesson.video_url)"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    class="w-full h-full"
                                    style="min-height: 400px;"
                                ></iframe>
                                <video
                                    v-else
                                    :src="lesson.video_url"
                                    controls
                                    class="w-full h-full"
                                >
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>

                        <!-- Text Content -->
                        <div v-if="lesson.content" class="prose max-w-none mb-6">
                            <div v-html="lesson.content" class="text-gray-700 leading-relaxed"></div>
                        </div>

                        <!-- Attachments -->
                        <div v-if="attachments.length > 0" class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Attachments</h3>
                            <div class="space-y-2">
                                <a
                                    v-for="attachment in attachments"
                                    :key="attachment.id"
                                    :href="attachment.file_url"
                                    target="_blank"
                                    class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition"
                                >
                                    <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ attachment.file_name }}</p>
                                        <p class="text-sm text-gray-600">{{ attachment.file_type }}</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Mark Complete Button -->
                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg v-if="lesson.is_completed" class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span v-if="lesson.is_completed" class="text-green-600 font-medium">Lesson Completed</span>
                                    <span v-else>Ready to move forward?</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <button
                                        v-if="previousLesson"
                                        @click="navigateLesson(previousLesson.id)"
                                        class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition"
                                    >
                                        ← Previous
                                    </button>
                                    <button
                                        v-if="!lesson.is_completed"
                                        @click="markComplete"
                                        class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
                                    >
                                        Mark as Complete
                                    </button>
                                    <button
                                        v-if="nextLesson"
                                        @click="navigateLesson(nextLesson.id)"
                                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition"
                                    >
                                        Next →
                                    </button>
                                    <Link
                                        v-else
                                        :href="route('student.courses.show', course.id)"
                                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition"
                                    >
                                        Back to Course
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Sidebar - Lesson List -->
                <div class="lg:col-span-1">
                    <Card>
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900">Course Content</h3>
                        </template>

                        <div class="space-y-2 max-h-[600px] overflow-y-auto">
                            <div
                                v-for="(item, index) in allLessons"
                                :key="item.id"
                                :class="[
                                    'p-3 rounded-lg cursor-pointer transition',
                                    item.id === lesson.id 
                                        ? 'bg-blue-100 border-2 border-blue-500'
                                        : 'border-2 border-transparent hover:border-gray-200 hover:bg-gray-50',
                                    item.is_completed ? 'bg-green-50' : ''
                                ]"
                                @click="navigateLesson(item.id)"
                            >
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-200 text-gray-700 text-xs font-bold flex items-center justify-center mr-2">
                                        {{ index + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ item.title }}</p>
                                        <p class="text-xs text-gray-600">{{ item.estimated_duration || 0 }} min</p>
                                    </div>
                                    <svg
                                        v-if="item.is_completed"
                                        class="flex-shrink-0 w-5 h-5 text-green-500 ml-2"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
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
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    lesson: {
        type: Object,
        required: true
    },
    course: {
        type: Object,
        required: true
    },
    attachments: {
        type: Array,
        default: () => []
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

const isYouTubeUrl = (url) => {
    return url && (url.includes('youtube.com') || url.includes('youtu.be'));
};

const getYouTubeEmbedUrl = (url) => {
    if (!url) return '';
    
    // Extract video ID from various YouTube URL formats
    let videoId = '';
    if (url.includes('youtube.com/watch?v=')) {
        videoId = url.split('watch?v=')[1].split('&')[0];
    } else if (url.includes('youtu.be/')) {
        videoId = url.split('youtu.be/')[1].split('?')[0];
    } else if (url.includes('youtube.com/embed/')) {
        return url; // Already embed format
    }
    
    return `https://www.youtube.com/embed/${videoId}`;
};

const markComplete = () => {
    router.post(route('student.lessons.complete', props.lesson.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Lesson marked as complete
        }
    });
};

const navigateLesson = (lessonId) => {
    router.visit(route('student.lessons.show', lessonId));
};
</script>

<style scoped>
.aspect-w-16 {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
}

.aspect-w-16 > * {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}

.prose {
    max-width: 65ch;
}

.prose p {
    margin-bottom: 1.25em;
}

.prose h2 {
    font-size: 1.5em;
    font-weight: 700;
    margin-top: 2em;
    margin-bottom: 1em;
}

.prose h3 {
    font-size: 1.25em;
    font-weight: 600;
    margin-top: 1.6em;
    margin-bottom: 0.6em;
}

.prose ul, .prose ol {
    margin: 1.25em 0;
    padding-left: 1.625em;
}

.prose li {
    margin: 0.5em 0;
}
</style>
