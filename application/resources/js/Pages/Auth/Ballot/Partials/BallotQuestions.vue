<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Ballot Questions
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add questions to your ballot.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 w-full gap-6">
            <div class="w-full" v-if="!!questions?.length"  v-for="question in questions">
                <BallotQuestionCard
                    :key="question.hash"
                    :ballot="ballot"
                    :question="question" />
            </div>

            <div>
                <NewBallotQuestionButton class="w-96" :ballot="ballot" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import NewBallotQuestionButton from "@/Pages/Auth/Ballot/Partials/NewBallotQuestionButton.vue";
import BallotData = App.DataTransferObjects.BallotData;
import BallotQuestionCard from "@/Pages/Auth/Question/Partials/BallotQuestionCard.vue";
import QuestionData = App.DataTransferObjects.QuestionData;

const props = defineProps<{
    ballot: BallotData;
    questions: QuestionData[];
}>();
</script>
