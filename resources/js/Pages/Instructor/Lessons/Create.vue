<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

const props = defineProps({
    course: Object
});

const form = useForm({
    title: '',
    content: '',
    lesson_type: 'Video',
    video_url: '',
    estimated_duration: '',
    order_position: '',
    attachments: [],
    has_quiz: false
});

const selectedFiles = ref([]);

const handleFileChange = (event) => {
    const files = Array.from(event.target.files);
    form.attachments = files;
    selectedFiles.value = files.map(file => ({
        name: file.name,
        size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
        type: file.name.split('.').pop().toUpperCase()
    }));
};

const removeFile = (index) => {
    const files = Array.from(form.attachments);
    files.splice(index, 1);
    form.attachments = files;
    selectedFiles.value.splice(index, 1);
};

const submitForm = () => {
    form.post(route('instructor.lessons.store', props.course.id), {
        forceFormData: true,
        onSuccess: () => {
            // Redirect handled by backend
        }
    });
};
</script>

<template>
    <Head title="Add Lesson" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Add Lesson to {{ course.title }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
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

                            <!-- File Attachments -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Attachments (PDF, PowerPoint, Videos, etc.)
                                </label>
                                <input
                                    type="file"
                                    multiple
                                    accept=".pdf,.ppt,.pptx,.doc,.docx,.mp4,.zip"
                                    @change="handleFileChange"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                <p class="mt-1 text-sm text-gray-500">Supported: PDF, PowerPoint, Word, MP4, ZIP (Max 50MB each)</p>
                                <InputError :message="form.errors.attachments" class="mt-2" />
                                
                                <!-- Selected Files List -->
                                <div v-if="selectedFiles.length > 0" class="mt-3 space-y-2">
                                    <div v-for="(file, index) in selectedFiles" :key="index" 
                                         class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-medium text-gray-600 bg-gray-200 px-2 py-1 rounded">
                                                {{ file.type }}
                                            </span>
                                            <span class="text-sm text-gray-700">{{ file.name }}</span>
                                            <span class="text-xs text-gray-500">({{ file.size }})</span>
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeFile(index)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
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
                                    Order (Optional)
                                </label>
                                <input
                                    id="order_position"
                                    v-model="form.order_position"
                                    type="number"
                                    min="1"
                                    placeholder="Leave empty to add at the end"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <p class="mt-1 text-sm text-gray-500">Specify the position of this lesson in the course</p>
                                <InputError :message="form.errors.order_position" class="mt-2" />
                            </div>

                            <!-- Add Quiz Option -->
                            <div class="border-t pt-6">
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="form.has_quiz"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm font-medium text-gray-700">Add a quiz to this lesson</span>
                                </label>
                                <p class="mt-1 ml-6 text-sm text-gray-500">You can create quiz questions after adding the lesson</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end space-x-3">
                                <button
                                    type="button"
                                    @click="router.visit(route('instructor.courses.show', course.id))"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Adding...' : 'Add Lesson' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
