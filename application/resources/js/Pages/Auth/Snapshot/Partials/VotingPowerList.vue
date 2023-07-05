<template>
    <div class="flex flex-col justify-center gap-4">
        <header class="mb-2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Voting Power
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Accounts and voting powers.
            </p>
        </header>

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
import { useSnapshotStore } from '@/stores/snapshot-store';
import { storeToRefs } from 'pinia';
import votingPowerQuery from "@/types/voting-power-query";

let queryDataRef = ref<votingPowerQuery|null>(null);

let vPColumns = ['Stake Address (voter id)', 'Voting Power'];
let snapshotStore = useSnapshotStore();
let { queryData, votingPowersData, votingPowersPagination} = storeToRefs(snapshotStore);
let votingPowers = computed(() => votingPowersData.value);
let votingPowerPagination = computed(() => votingPowersPagination.value)

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
