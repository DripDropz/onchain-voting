<template>
    <div class="flex flex-col gap-4 min-h-80">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Ballot Snapshot
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Snapshot use for voting power.
            </p>
        </div>
        <div class="w-96">
            <NewSnapshotButton :ballot="ballot" v-if="!ballot?.snapshot" />
        </div>
        <SnapshotCard :snapshot="ballot?.snapshot" v-if="ballot?.snapshot" :ballot="ballot?.hash" :showUnlink="true"/>
    </div>
</template>
<script setup lang="ts">
import BallotData = App.DataTransferObjects.BallotData;
import SnapshotCard from "../../Snapshot/Partials/SnapshotCard.vue";
import NewSnapshotButton from "../../Snapshot/Partials/NewSnapshotButton.vue";

defineProps<{
    ballot: BallotData;
}>();
</script>
