<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import InstructorLayout from '@/Layouts/InstructorLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const userRole = computed(() => page.props.auth.user?.role?.toLowerCase() || 'student');
const LayoutComponent = computed(() => {
    if (userRole.value === 'instructor') return InstructorLayout;
    if (userRole.value === 'admin') return AdminLayout;
    return StudentLayout;
});
</script>

<template>
    <Head title="Profile" />

    <component :is="LayoutComponent">
        <div class="max-w-5xl mx-auto px-4 py-8">
            <div class="mb-10">
                <h2 class="text-4xl font-black text-gray-900 mb-2">Profile Settings</h2>
                <p class="text-gray-600">Manage your account settings and preferences</p>
            </div>
            <div class="space-y-8">
                <div class="p-8 bg-white shadow-lg rounded-2xl border border-gray-100">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-8 bg-white shadow-lg rounded-2xl border border-gray-100">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="p-8 bg-white shadow-lg rounded-2xl border border-gray-100">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </component>
</template>
