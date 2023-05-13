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

        <NewBallotQuestionButton  :ballot="ballot" />
    </div>
</template>
<script setup lang="ts">
import NewBallotQuestionButton from "@/Pages/Ballot/Partials/NewBallotQuestionButton.vue";
import BallotData = App.DataTransferObjects.BallotData;

defineProps<{
    ballot: BallotData;
}>();
</script>
