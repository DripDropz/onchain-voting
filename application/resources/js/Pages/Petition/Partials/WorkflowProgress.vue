<template>
    <div class="w-full mb-8">
        <div class="flex items-center justify-between relative">
            <!-- Connector line (behind the circles) -->
            <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-0.5 bg-gray-200 dark:bg-gray-700 mx-6 z-0" />

            <div
                v-for="(step, index) in steps"
                :key="step.label"
                class="relative z-10 flex flex-col items-center gap-2"
            >
                <!-- Circle -->
                <div
                    class="flex items-center justify-center w-9 h-9 rounded-full border-2 font-semibold text-sm transition-colors"
                    :class="circleClass(index)"
                >
                    <CheckIcon v-if="index + 1 < currentStep" class="w-4 h-4" />
                    <span v-else>{{ index + 1 }}</span>
                </div>
                <!-- Label -->
                <span
                    class="text-xs font-medium whitespace-nowrap"
                    :class="index + 1 === currentStep
                        ? 'text-sky-600 dark:text-sky-400'
                        : index + 1 < currentStep
                            ? 'text-gray-500 dark:text-gray-400'
                            : 'text-gray-400 dark:text-gray-600'"
                >
                    {{ step.label }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { CheckIcon } from "@heroicons/vue/20/solid";

const props = defineProps<{
    currentStep: number;
}>();

const steps = [
    { label: "Title" },
    { label: "Description" },
    { label: "Review" },
];

const circleClass = (index: number) => {
    const step = index + 1;
    if (step < props.currentStep) {
        return "bg-sky-500 border-sky-500 text-white";
    }
    if (step === props.currentStep) {
        return "bg-white dark:bg-gray-900 border-sky-500 text-sky-600 dark:text-sky-400";
    }
    return "bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-600";
};
</script>
