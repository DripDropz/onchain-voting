<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Ballot Snapshot
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Snapshot use for voting power.
            </p>
        </div>

        <div class="flex flex-row flex-wrap w-full gap-4">
            <SnapshotCard v-if="ballot?.snapshot" />

            <span  v-if="ballot && !ballot?.live">
                <AddBallotSnapshotButton class="w-96" :ballot="ballot" />
            </span>
        </div>
    </div>
</template>
<script setup lang="ts">
import AddBallotSnapshotButton from "@/Pages/Auth/Ballot/Partials/AddBallotSnapshotButton.vue";
import BallotData = App.DataTransferObjects.BallotData;
import SnapshotCard from "../../Snapshot/Partials/SnapshotCard.vue";

const props = defineProps<{
    ballot: BallotData;
}>();
</script>
