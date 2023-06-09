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

        <div v-if="!ballot?.snapshot" class="flex flex-row flex-wrap w-1/2 h-8 gap-4">
            <div class="flex flex-row flex-shrink-0 w-2/3">
                <div class="inline-flex w-full shadow-slate-300">
                    <SnapshotPicker @snapshot="getSelectedSnapshot($event)" :key="reset" />
                </div>
            </div>
            <button v-if="selectedRef" @click.stop="addSnapshot()"
                class="inline-flex items-center h-8 px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span class="flex items-center justify-between ">
                    Add Snapshot
                </span>
            </button>
        </div>
        <SnapshotCard :snapshot="ballot?.snapshot" v-if="ballot?.snapshot" />
    </div>
</template>
<script setup lang="ts">
import BallotData = App.DataTransferObjects.BallotData;
import SnapshotCard from "../../Snapshot/Partials/SnapshotCard.vue";
import SnapshotPicker from "../../Snapshot/Partials/SnapshotPicker.vue";
import BallotService from "../Services/ballot-service";
import { ref, Ref } from "vue";
import AlertService from "@/shared/Services/alert-service";
import { router } from "@inertiajs/core";

const props = defineProps<{
    ballot: BallotData;
}>();

let selectedRef: Ref<string | null> = ref(null)
let getSelectedSnapshot = (snapshot: string | null) => {
    selectedRef.value = snapshot;
}
let reset = ref(0)

let addSnapshot = async () => {
    if (!selectedRef.value?.[0] || !props.ballot.hash) return;
    const data = {
        snapshot: selectedRef.value?.[0],
        ballot: props.ballot.hash,
    }
    await BallotService.linkSnapshot(data);
    AlertService.show(['Snapshot added successfully'], 'success');
    router.reload();
}
</script>
