<template>
    <div :class="[
        'bg-white rounded-lg shadow-md overflow-hidden',
        hover && 'hover:shadow-lg transition-shadow duration-300',
        padding && paddingClass
    ]">
        <div v-if="$slots.header" :class="['border-b border-gray-200', headerPadding && 'px-6 py-4']">
            <slot name="header" />
        </div>
        
        <div :class="[padding && 'p-6']">
            <slot />
        </div>
        
        <div v-if="$slots.footer" :class="['border-t border-gray-200', footerPadding && 'px-6 py-4']">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    padding: {
        type: Boolean,
        default: true
    },
    hover: {
        type: Boolean,
        default: false
    },
    headerPadding: {
        type: Boolean,
        default: true
    },
    footerPadding: {
        type: Boolean,
        default: true
    }
});

const paddingClass = computed(() => props.padding ? 'p-6' : '');
</script>
