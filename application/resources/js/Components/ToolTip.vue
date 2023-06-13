<template>
    <div v-if="tip"
        class="group flex items-center"
        :class="{
            'text-yellow-800': type === 'info',
            'text-teal-600': type === 'success',
            'text-red-500': type === 'error',
        }"
    >
        <InformationCircleIcon class="w-9 h-9" aria-hidden="true" />
        <span
            class="group-hover:opacity-100 transition-opacity bg-slate-200 px-1 text-sm text-gray-900 rounded-md opacity-0 ml-4 mx-auto dark:bg-gray-900 dark:text-white"
            >{{ tip }}</span
        >
    </div>
</template>

<script setup lang="ts">
import { InformationCircleIcon } from "@heroicons/vue/20/solid";
defineProps<{
    type: string;
    tip: string;
}>();
</script>

<style></style>
