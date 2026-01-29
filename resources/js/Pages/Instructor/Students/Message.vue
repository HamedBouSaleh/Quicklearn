<template>
    <Head :title="`Message ${student.name}`" />
    <InstructorLayout>
        <div class="p-6 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-4">Message {{ student.name }}</h1>
            <div class="bg-white rounded shadow p-4 mb-4 h-96 overflow-y-auto flex flex-col-reverse">
                <div v-for="msg in messages" :key="msg.id" class="mb-2">
                    <div :class="msg.sender_id === $page.props.auth.user.id ? 'text-right' : 'text-left'">
                        <span class="inline-block px-3 py-2 rounded-lg" :class="msg.sender_id === $page.props.auth.user.id ? 'bg-blue-100 text-blue-900' : 'bg-gray-100 text-gray-900'">
                            {{ msg.content }}
                        </span>
                        <div class="text-xs text-gray-400 mt-1">{{ new Date(msg.created_at).toLocaleString() }}</div>
                    </div>
                </div>
            </div>
            <form @submit.prevent="sendMessage">
                <div class="flex gap-2">
                    <input v-model="form.content" type="text" class="flex-1 border rounded px-3 py-2" placeholder="Type your message..." />
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded">Send</button>
                </div>
                <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
            </form>
        </div>
    </InstructorLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';

const props = defineProps({
    student: Object,
    messages: Array
});

const form = useForm({
    content: ''
});

function sendMessage() {
    form.post(route('students.message.send', props.student.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('content');
        }
    });
}
</script>
