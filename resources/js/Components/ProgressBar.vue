<template>
    <div class="w-full">
        <div class="flex justify-between items-center mb-1">
            <span v-if="showLabel" class="text-sm font-medium text-gray-700">{{ label }}</span>
            <span v-if="showPercentage" class="text-sm font-medium text-gray-700">{{ progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div 
                class="h-2.5 rounded-full transition-all duration-300"
                :class="colorClass"
                :style="{ width: `${progress}%` }"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    progress: {
        type: Number,
        required: true,
        validator: (value) => value >= 0 && value <= 100
    },
    label: {
        type: String,
        default: 'Progress'
    },
    showLabel: {
        type: Boolean,
        default: false
    },
    showPercentage: {
        type: Boolean,
        default: true
    },
    color: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'green', 'yellow', 'red'].includes(value)
    }
});

const colorClass = computed(() => {
    const colors = {
        blue: 'bg-blue-600',
        green: 'bg-green-600',
        yellow: 'bg-yellow-500',
        red: 'bg-red-600'
    };
    return colors[props.color] || colors.blue;
});
</script>
