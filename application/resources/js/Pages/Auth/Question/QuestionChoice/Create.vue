<script setup lang="ts">
import ModalRoute from "@/Components/ModalRoute.vue";
import QuestionData = App.DataTransferObjects.QuestionData;
import CreateUpdateQuestionChoiceForm
    from "@/Pages/Auth/Question/QuestionChoice/Partials/CreateUpdateQuestionChoiceForm.vue";
import BallotData = App.DataTransferObjects.BallotData;

defineProps<{
    question: QuestionData;
    ballot: BallotData;
}>();
</script>

<template>
    <ModalRoute>
        <div class="flex flex-col">
            <div class="bg-gray-50 p-6 dark:bg-gray-900">
                <h2 class="font-semibold text-lg 2xl:text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Add a choice to {{question?.title}}
                </h2>
            </div>

            <div class="max-w-7xl space-y-6 p-6">
                <CreateUpdateQuestionChoiceForm
                    :ballot="ballot"
                    :question="question" />
            </div>
        </div>
    </ModalRoute>
</template>
