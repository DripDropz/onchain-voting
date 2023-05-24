<script lang="ts" setup>
import QuestionData = App.DataTransferObjects.QuestionData;
import {computed} from "vue";

const props = defineProps<{
    question: QuestionData;
}>();

let status = computed(() => {
    switch (props.question.status) {
        case 'published':
            return 'Published';
        default:
            return props.question.status;
    }
});

let theme = computed(() => {
    switch (props.question.status) {
        case 'published':
            return 'indigo';
        default:
            return 'gray';
    }
});
</script>
<template>
    <span class="flex items-center gap-x-1.5">
        <span class="flex-none rounded-full p-1" :class="[`bg-${theme}-500/20`]">
            <div class="h-1.5 w-1.5 rounded-full" :class="[`bg-${theme}-500`]" />
        </span>
        <span class="text-xs leading-5 text-gray-300 capitalize">
            {{ status }}
        </span>
    </span>
</template>
