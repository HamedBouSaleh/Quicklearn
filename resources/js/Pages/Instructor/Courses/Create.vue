<script setup>
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';
import Card from '@/Components/Card.vue';
import InputError from '@/Components/InputError.vue';
import { ref, computed } from 'vue';

const form = useForm({
    title: '',
    short_description: '',
    long_description: '',
    category: '',
    custom_category: '',
    difficulty: '',
    estimated_duration: '',
    thumbnail: null,
    is_published: false,
    instructor_bio: ''
});

const thumbnailPreview = ref(null);
const characterCount = computed(() => form.short_description.length);

const handleThumbnailChange = (event) => {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        form.thumbnail = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            thumbnailPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeThumbnail = () => {
    form.thumbnail = null;
    thumbnailPreview.value = null;
};

const submit = () => {
    // Use custom category if "Other" is selected
    if (form.category === 'Other' && form.custom_category) {
        form.category = form.custom_category;
    }
    form.post(route('instructor.courses.store'));
};
</script>

<template>
    <InstructorLayout>
        <Head title="Create Course" />

        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <Link :href="route('instructor.courses.index')" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Courses
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Create New Course</h1>
                <p class="mt-2 text-gray-600">Fill in the details to create a new course</p>
            </div>

            <Card>
                <form @submit.prevent="submit">
                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Course Title *
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., Introduction to Web Development"
                        />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>

                    <!-- Short Description -->
                    <div class="mb-6">
                        <label for="short_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Short Description *
                        </label>
                        <textarea
                            id="short_description"
                            v-model="form.short_description"
                            required
                            maxlength="500"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="A brief overview of the course"
                        ></textarea>
                        <div class="flex justify-between mt-1">
                            <InputError :message="form.errors.short_description" />
                            <span class="text-sm text-gray-500">{{ characterCount }}/500</span>
                        </div>
                    </div>

                    <!-- Long Description -->
                    <div class="mb-6">
                        <label for="long_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Long Description *
                        </label>
                        <textarea
                            id="long_description"
                            v-model="form.long_description"
                            required
                            rows="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Detailed description of what students will learn"
                        ></textarea>
                        <InputError :message="form.errors.long_description" class="mt-2" />
                    </div>

                    <!-- Category -->
                    <div class="mb-6">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category *
                        </label>
                        <select
                            id="category"
                            v-model="form.category"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select a category</option>
                            <option value="Programming">Programming</option>
                            <option value="Design">Design</option>
                            <option value="Business">Business</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Data Science">Data Science</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Science">Science</option>
                            <option value="Arts">Arts</option>
                            <option value="Languages">Languages</option>
                            <option value="Health & Fitness">Health & Fitness</option>
                            <option value="Music">Music</option>
                            <option value="Photography">Photography</option>
                            <option value="Other">Other (Specify below)</option>
                        </select>
                        <InputError :message="form.errors.category" class="mt-2" />
                        
                        <!-- Custom Category Input (shown when "Other" is selected) -->
                        <div v-if="form.category === 'Other'" class="mt-3">
                            <label for="custom_category" class="block text-sm font-medium text-gray-700 mb-2">
                                Specify Category *
                            </label>
                            <input
                                id="custom_category"
                                v-model="form.custom_category"
                                type="text"
                                required
                                placeholder="e.g., Robotics, Culinary Arts, Finance"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p class="mt-1 text-sm text-gray-500">Enter your custom category name</p>
                        </div>
                    </div>

                    <!-- Difficulty -->
                    <div class="mb-6">
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">
                            Difficulty Level *
                        </label>
                        <select
                            id="difficulty"
                            v-model="form.difficulty"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select difficulty</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                        <InputError :message="form.errors.difficulty" class="mt-2" />
                    </div>

                    <!-- Estimated Duration -->
                    <div class="mb-6">
                        <label for="estimated_duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Estimated Duration (minutes)
                        </label>
                        <input
                            id="estimated_duration"
                            v-model="form.estimated_duration"
                            type="number"
                            min="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., 180"
                        />
                        <InputError :message="form.errors.estimated_duration" class="mt-2" />
                    </div>

                    <!-- Thumbnail Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Course Thumbnail
                        </label>
                        <div v-if="thumbnailPreview" class="mb-4">
                            <img :src="thumbnailPreview" alt="Thumbnail preview" class="w-48 h-32 object-cover rounded-lg" />
                            <button
                                type="button"
                                @click="removeThumbnail"
                                class="mt-2 text-sm text-red-600 hover:text-red-800"
                            >
                                Remove image
                            </button>
                        </div>
                        <input
                            type="file"
                            accept="image/*"
                            @change="handleThumbnailChange"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        />
                        <p class="mt-1 text-sm text-gray-500">Supported formats: JPG, PNG, GIF (Max 2MB)</p>
                        <InputError :message="form.errors.thumbnail" class="mt-2" />
                    </div>

                    <!-- Instructor Bio -->
                    <div class="mb-6">
                        <label for="instructor_bio" class="block text-sm font-medium text-gray-700 mb-2">
                            About You (Instructor Bio)
                        </label>
                        <textarea
                            id="instructor_bio"
                            v-model="form.instructor_bio"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Introduce yourself to your students. Share your expertise, experience, and what makes you qualified to teach this course."
                        ></textarea>
                        <p class="mt-1 text-sm text-gray-500">This will be displayed to students enrolled in your course</p>
                        <InputError :message="form.errors.instructor_bio" class="mt-2" />
                    </div>

                    <!-- Publish Status -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                v-model="form.is_published"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-700">Publish this course immediately</span>
                        </label>
                        <p class="mt-1 text-sm text-gray-500">Students can only enroll in published courses</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4 pt-4 border-t">
                        <Link
                            :href="route('instructor.courses.index')"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Course' }}
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </InstructorLayout>
</template>
