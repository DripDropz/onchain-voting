<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Ballot Questions
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add questions to your ballot.
            </p>
        </div>

        <div class="flex flex-row gap-4 flex-wrap w-full">
            <div class="w-128" v-if="!!ballot.questions?.length">
                <BallotQuestionCard
                    v-for="question in ballot.questions"
                    :key="question.id"
                    :ballot="ballot"
                    :question="question" />
            </div>

            <NewBallotQuestionButton class="w-96" :ballot="ballot"/>
        </div>
    </div>
</template>
<script setup lang="ts">
import NewBallotQuestionButton from "@/Pages/Auth/Ballot/Partials/NewBallotQuestionButton.vue";
import BallotData = App.DataTransferObjects.BallotData;
import BallotQuestionCard from "@/Pages/Auth/Question/Partials/BallotQuestionCard.vue";

const props = defineProps<{
    ballot: BallotData;
}>();

</script>
