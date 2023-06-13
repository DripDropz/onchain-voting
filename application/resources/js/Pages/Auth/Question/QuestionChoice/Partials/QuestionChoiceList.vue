<template>
    <ul role="list" class="overflow-auto divide-y divide-gray-800 max-h-80">
        <QuestionChoiceListItem v-for="choice in choices" :key="choice.title" :choice="choice" />
    </ul>
</template>

<script setup lang="ts">
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import QuestionChoiceListItem from "@/Pages/Auth/Question/QuestionChoice/Partials/QuestionChoiceListItem.vue";

defineProps<{
    choices?: QuestionChoiceData[];
}>();
</script>
