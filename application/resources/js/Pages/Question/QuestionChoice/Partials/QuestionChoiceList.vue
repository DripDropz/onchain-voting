<template>
    <ul role="list" class="divide-y divide-gray-800 max-h-80 overflow-auto">
        <QuestionChoiceListItem v-for="choice in choices" :key="choice.title" :choice="choice" />
    </ul>
</template>

<script setup lang="ts">
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import QuestionChoiceListItem from "@/Pages/Question/QuestionChoice/Partials/QuestionChoiceListItem.vue";

const props = defineProps<{
    choices: QuestionChoiceData[];
}>();
</script>
