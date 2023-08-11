<template>
    <div v-if="tip"
        class="group flex items-center relative"
        :class="{
            'text-yellow-800': type === 'info',
            'text-teal-600': type === 'success',
            'text-red-500': type === 'error',
        }"
    >
        <span class="group-hover:opacity-100 transition-opacity bg-slate-200 px-1 text-sm text-gray-900 rounded-md opacity-0 mx-auto dark:bg-gray-900 dark:text-white absolute right-10 whitespace-nowrap lowercase">{{ tip }}</span>
        <InformationCircleIcon class="w-4 h-4" aria-hidden="true" />
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
