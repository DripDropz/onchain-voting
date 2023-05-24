<style lang="scss">

</style>
<template>
    <li @click="emit('selected', choice)"
        class="p-2 border-2 border-white rounded-full xl:p-3 hover:bg-indigo-400/50 hover:text-indigo-950 hover:cursor-pointer">
        {{ choice.title }}
    </li>
</template>
<script lang="ts" setup>
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;

const emit = defineEmits<{
    (e: 'selected', choice: QuestionChoiceData): void
}>();

const props = defineProps<{
    choice: QuestionChoiceData;
}>();
</script>
