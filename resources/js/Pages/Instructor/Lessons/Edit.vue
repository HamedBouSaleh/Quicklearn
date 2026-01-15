<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    lesson: Object
});

const form = useForm({
    title: props.lesson.title || '',
    content: props.lesson.content || '',
    lesson_type: props.lesson.lesson_type || 'Video',
    video_url: props.lesson.video_url || '',
    estimated_duration: props.lesson.estimated_duration || '',
    order_position: props.lesson.order_position || ''
});

const submitForm = () => {
    form.put(route('instructor.lessons.update', props.lesson.id), {
        onSuccess: () => {
            // Redirect handled by backend
        }
    });
};
</script>

<template>
    <Head title="Edit Lesson" />

    <InstructorLayout>
        <div class="max-w-4xl">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Lesson</h1>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">
                            Lesson Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="lesson_type" class="block text-sm font-medium text-gray-700">
                            Lesson Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="lesson_type"
                            v-model="form.lesson_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="Video">Video</option>
                            <option value="Article">Article</option>
                            <option value="Mixed">Mixed (Video + Text)</option>
                        </select>
                        <InputError :message="form.errors.lesson_type" class="mt-2" />
                    </div>

                    <!-- Video URL (only if type is video) -->
                    <div v-if="form.lesson_type === 'Video' || form.lesson_type === 'Mixed'">
                        <label for="video_url" class="block text-sm font-medium text-gray-700">
                            Video URL (YouTube)
                        </label>
                        <input
                            id="video_url"
                            v-model="form.video_url"
                            type="url"
                            placeholder="https://www.youtube.com/watch?v=..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <p class="mt-1 text-sm text-gray-500">Enter a YouTube video URL</p>
                        <InputError :message="form.errors.video_url" class="mt-2" />
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">
                            Lesson Content
                        </label>
                        <textarea
                            id="content"
                            v-model="form.content"
                            rows="10"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                            placeholder="Enter lesson content (supports HTML)"
                        ></textarea>
                        <p class="mt-1 text-sm text-gray-500">You can use HTML tags for formatting</p>
                        <InputError :message="form.errors.content" class="mt-2" />
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="estimated_duration" class="block text-sm font-medium text-gray-700">
                            Duration (minutes)
                        </label>
                        <input
                            id="estimated_duration"
                            v-model="form.estimated_duration"
                            type="number"
                            min="1"
                            placeholder="e.g., 45"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <InputError :message="form.errors.estimated_duration" class="mt-2" />
                    </div>

                    <!-- Order Position -->
                    <div>
                        <label for="order_position" class="block text-sm font-medium text-gray-700">
                            Order
                        </label>
                        <input
                            id="order_position"
                            v-model="form.order_position"
                            type="number"
                            min="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <p class="mt-1 text-sm text-gray-500">Position of this lesson in the course</p>
                        <InputError :message="form.errors.order_position" class="mt-2" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3">
                        <button
                            type="button"
                            @click="router.visit(route('instructor.courses.show', lesson.course_id))"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Updating...' : 'Update Lesson' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </InstructorLayout>
</template>
