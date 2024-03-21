<template>
    <div v-if="count" v-for="index in count" :key=index
        class="relative flex flex-row gap-4 border border-gray-800 rounded-lg dark:text-gray-200 dark:border-gray-200 my-8 animate-pulse">
        <div class="w-[75%] px-3 py-4">
            <div class="h-9 mb-4 rounded bg-slate-400 dark:bg-slate-700 w-[50%]"></div>
            <div class="">
                <div class="rounded h-4 bg-slate-400 dark:bg-slate-700 mb-2"></div>
                <div class="rounded h-4 bg-slate-400 dark:bg-slate-700 mb-2"></div>
                <div class="rounded h-4 bg-slate-400 dark:bg-slate-700 mb-2"></div>
            </div>
        </div>
        <ul
            class="bg-slate-400 dark:bg-slate-700 w-[25%] flex flex-row items-center rounded-tr-lg rounded-br-lg justify-center">
            <li class="w-auto ocv-link"></li>
        </ul>
    </div>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    count: number
}>(), {
    count: 4
});

</script>
