<script setup lang="ts">
import BallotData = App.DataTransferObjects.BallotData;
import ModalRoute from "@/Components/ModalRoute.vue";
import CreateUpdateQuestionForm from "@/Pages/Question/Partials/CreateUpdateQuestionForm.vue";
import QuestionData = App.DataTransferObjects.QuestionData;

defineProps<{
    ballot?: BallotData;
    question: QuestionData;
}>();
</script>

<template>
    <ModalRoute>
        <div class="flex flex-col">
            <div class="bg-gray-50 p-6 dark:bg-gray-900">
                <h2 class="font-semibold text-lg 2xl:text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Create {{ballot.title}} Ballot Questions
                </h2>
            </div>

            <div class="max-w-7xl space-y-6 p-6">
                <CreateUpdateQuestionForm
                    :ballot="ballot"
                    :question="question"
                    class="max-w-xl"
                />
            </div>
        </div>
    </ModalRoute>
</template>
