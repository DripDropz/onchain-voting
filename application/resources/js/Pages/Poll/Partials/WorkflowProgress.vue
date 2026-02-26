<template>
    <div class="w-full mb-8 overflow-hidden rounded-2xl border border-slate-200/80 bg-white/80 p-4 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/60 sm:p-6">
        <div class="relative grid grid-cols-3 gap-2 sm:gap-4">
            <div class="pointer-events-none absolute left-5 right-5 top-5">
                <div class="relative h-1 rounded-full bg-slate-200 dark:bg-slate-700/70">
                    <div
                        class="h-1 rounded-full bg-gradient-to-r from-sky-500 to-cyan-400 transition-all duration-300"
                        :style="{ width: progressBarWidth }"
                    ></div>
                </div>
            </div>

            <div
                v-for="(step, index) in steps"
                :key="step.label"
                class="relative z-10 flex flex-col items-center text-center"
            >
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-semibold transition-all duration-200"
                    :class="circleClass(index)"
                >
                    <CheckIcon v-if="index + 1 < currentStep" class="w-4 h-4" />
                    <span v-else>{{ index + 1 }}</span>
                </div>
                <span class="mt-2 text-sm font-semibold tracking-wide" :class="labelClass(index)">
                    {{ step.label }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { CheckIcon } from "@heroicons/vue/20/solid";

const props = defineProps<{
    currentStep: number;
}>();

const steps = [
    { label: "Question" },
    { label: "Details" },
    { label: "Review" },
];

const progressBarWidth = computed(() => {
    if (props.currentStep <= 1) {
        return "0%";
    }

    const maxTransitions = steps.length - 1;
    const progress = ((props.currentStep - 1) / maxTransitions) * 100;
    return `${Math.min(Math.max(progress, 0), 100)}%`;
});

const circleClass = (index: number) => {
    const step = index + 1;
    if (step < props.currentStep) {
        return "bg-sky-500 border-sky-500 text-white shadow-md shadow-sky-500/40";
    }
    if (step === props.currentStep) {
        return "bg-white dark:bg-slate-900 border-sky-400 text-sky-600 dark:text-sky-300 ring-4 ring-sky-100 dark:ring-sky-500/20";
    }
    return "bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-slate-400 dark:text-slate-500";
};

const labelClass = (index: number) => {
    const step = index + 1;
    if (step === props.currentStep) {
        return "text-sky-700 dark:text-sky-300";
    }
    if (step < props.currentStep) {
        return "text-slate-700 dark:text-slate-200";
    }
    return "text-slate-400 dark:text-slate-500";
};
</script>
