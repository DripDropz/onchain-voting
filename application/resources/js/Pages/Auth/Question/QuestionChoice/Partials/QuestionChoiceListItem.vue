<script setup lang="ts">
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;

const props = defineProps<{
    choice: QuestionChoiceData;
}>();
</script>

<template>
    <li>
        <div class="flex justify-between py-5 gap-x-6 px-2 rounded-sm">
            <div class="flex gap-x-4">
                <div class="flex-auto min-w-0">
                    <p
                        class="text-sm font-semibold leading-6 text-black dark:text-white"
                    >
                        {{ choice.title }}
                    </p>
                    <p class="mt-1 text-xs leading-5 text-gray-600 dark:text-white overflow-hidden">
                        {{ choice.description }}
                    </p>
                </div>
            </div>
            <!--        <div class="hidden sm:flex sm:flex-col sm:items-end">-->
            <!--            <p class="text-sm leading-6 text-white">{{ choice.hash }}</p>-->
            <!--        </div>-->
        </div>
    </li>
</template>
<style scoped>
.drag {
     @apply bg-gray-900;
     @apply dark:bg-indigo-600;

}
.ghost {
    background: lightgray;
}
 .ghost > div {
    visibility: hidden;
 }
</style>
