<script setup lang="ts">
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;

const props = defineProps<{
    choice: QuestionChoiceData;
}>();
</script>

<template>
    <div class="flex justify-between gap-x-6 py-5">
        <div class="flex gap-x-4">
            <div class="min-w-0 flex-auto">
                <p class="text-sm font-semibold leading-6 text-white">{{ choice.title }}</p>
                <p class="mt-1 truncate text-xs leading-5 text-gray-400">{{ choice.description }}</p>
            </div>
        </div>
<!--        <div class="hidden sm:flex sm:flex-col sm:items-end">-->
<!--            <p class="text-sm leading-6 text-white">{{ choice.hash }}</p>-->
<!--        </div>-->
    </div>
</template>
