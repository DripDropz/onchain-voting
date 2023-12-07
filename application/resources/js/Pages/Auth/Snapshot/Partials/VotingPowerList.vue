<template>
    <div class="flex flex-col w-full justify-center gap-4">
        <div class="flex flex-row gap-16">
            <div class="flex flex-col w-1/3 justify-center gap-4">
                <header class="mb-2">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Voting Power
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Accounts and voting powers.
                    </p>
                </header>
            </div>
            <div v-if="percentageUploaded < 100" 
                class="w-2/3">
                <div class="flex justify-between mb-1">
                    <span class="text-base font-medium text-indigo-700 dark:text-indigo">Uploading...</span>
                    <span class="text-sm font-medium text-indigo-700 dark:text-indigo">{{ percentageUploaded.toFixed(2) }} %</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-indigo-600 h-2.5 rounded-full" :style="{ 'width': Math.floor(percentageUploaded) + '%' }" ></div>
                </div>
            </div>
        </div>
        <div class="text-gray-600 col-span-full dark:text-gray-400">
                <GlobalTableComponent :data="vPValues"
                        :pagination="votingPowerPagination" 
                        :columns="vPColumns"
                        @queryUpdated="(payload: {}) => queryDataRef = payload"
                        >
                    <template v-slot:header></template>
                    <template v-slot:body></template>
                    <template v-slot:footer></template>
                </GlobalTableComponent>
        </div>
    </div>
</template>

<script lang="ts" setup>
import GlobalTableComponent from '@/shared/components/GlobalTableComponent.vue';
import { computed, ref, watch } from 'vue';
import VotingPowerData = App.DataTransferObjects.VotingPowerData;
import SnapshotData = App.DataTransferObjects.SnapshotData;
import { useSnapshotStore } from '@/stores/snapshot-store';
import { storeToRefs } from 'pinia';
import votingPowerQuery from "@/types/voting-power-query";
import { ComputedRef } from 'vue';

let queryDataRef = ref<votingPowerQuery|null>(null);
const props = defineProps<{
    snapshot: SnapshotData;
}>();

let vPColumns = ['Stake Address (voter id)', 'Voting Power'];
let snapshotStore = useSnapshotStore();
let { queryData, votingPowersData, votingPowersPagination} = storeToRefs(snapshotStore);
let votingPowers = computed(() => votingPowersData.value);
let votingPowerPagination = computed(() => votingPowersPagination.value)
let expectedVPCount:ComputedRef<number> = computed(() => props.snapshot.metadata ? props.snapshot.metadata['row_count'] : 0);
let uploadedVPCount:ComputedRef<number> = computed(() => votingPowerPagination.value.total);
let percentageUploaded: ComputedRef<number> = computed(() => {
    return (uploadedVPCount.value / (+expectedVPCount.value)) * 100;
})

const vPValues = computed(() => 
    votingPowers.value.map((power: VotingPowerData) => ({
        voter_id: power.user?.voter_id,
        voting_power: power.voting_power,
    }))
);

watch([queryDataRef], () => {
    queryData.value = queryDataRef.value;
})
</script>
