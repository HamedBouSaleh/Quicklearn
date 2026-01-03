<script setup>
import { Head, router } from '@inertiajs/vue3';

// Redirect to student dashboard on mount
router.visit(route('student.dashboard'));
</script>

<template>
    <Head title="Dashboard" />
</template>
