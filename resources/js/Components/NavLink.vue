<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'flex items-center px-4 py-3 text-sm font-medium leading-5 text-gray-900 bg-blue-50 border-l-4 border-blue-600 focus:outline-none transition duration-150 ease-in-out'
        : 'flex items-center px-4 py-3 text-sm font-medium leading-5 text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-300 focus:outline-none focus:text-gray-900 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
