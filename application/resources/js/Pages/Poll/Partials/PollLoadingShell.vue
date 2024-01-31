<template>
    <div class="grid grid-cols-2 inner-container">
        <div v-for=" index in count"
            class="relative flex flex-col gap-4 px-10 py-4 m-4 border border-gray-800 rounded-lg dark:text-gray-200 dark:border-gray-200 animate-pulse">
            <div class="h-5 mb-4 rounded bg-slate-400 dark:bg-slate-700 w-80"></div>

            <div v-for=" index in 4" :key="index"
                class="flex flex-col border-gray-600 border-opacity-40 dark:border-gray-600">
                <div class="rounded w-120 h-9 bg-slate-400 dark:bg-slate-700"></div>
            </div>

            <div class="bottom-0 flex items-center justify-between py-2">
                <div class="w-12 h-5 mb-4 rounded bg-slate-400 dark:bg-slate-700"></div>
                <div class="w-24 mb-4 rounded h-9 bg-slate-400 dark:bg-slate-700"></div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
const props = withDefaults(defineProps<{
    count: number
}>(), {
    count: 4
});


</script>