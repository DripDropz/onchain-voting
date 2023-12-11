<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ 'Viewing ' }}<b>{{ snapshot.title }}</b>{{ ' Snapshot' }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <SnapshotCard :snapshot="snapshot" class="max-w-xl" />
                </div>

                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <VotingPowerList v-if="votingPowers?.length > 0" :snapshot="snapshot"/>
                    <VotingPowerImporterComponent v-else class="max-w-xl" :snapshot="snapshot"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ComputedRef} from "vue";
import { usePage } from "@inertiajs/vue3";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import VotingPowerData = App.DataTransferObjects.VotingPowerData;
import SnapshotCard from "@/Pages/Auth/Snapshot/Partials/SnapshotCard.vue";
import AlertService from '@/shared/Services/alert-service';
import VotingPowerList from './Partials/VotingPowerList.vue';
import VotingPowerImporterComponent from '@/Components/VotingPowerImporterComponent.vue';
import { useSnapshotStore } from '@/stores/snapshot-store';
import { storeToRefs } from 'pinia';

const props = defineProps<{
    snapshot: SnapshotData;
}>();

let snapshotHash:ComputedRef<any> = computed(() => props.snapshot.hash);

if (usePage().props?.errors) {
    AlertService.show(Object.values(usePage().props.errors), 'info');
}

let snapshotStore = useSnapshotStore();
snapshotStore.loadVotingPowers(snapshotHash.value);
let { votingPowersData, votingPowersPagination} = storeToRefs(snapshotStore);
const votingPowers: ComputedRef<VotingPowerData[]> = computed(() => votingPowersData.value);
let votingPowerPagination = computed(() => votingPowersPagination.value)
let expectedVPCount:ComputedRef<number> = computed(() => props.snapshot.metadata ? props.snapshot.metadata['row_count'] : 0);
let uploadedVPCount:ComputedRef<number> = computed(() => votingPowerPagination?.value?.total ?? 0);

let getPercentageUploaded = ($expected: number, uploaded: number) => {
    return ((uploaded / $expected) * 100) ?? 0;
}
const percentageLoader = setInterval(() => {
    let percentage = getPercentageUploaded(expectedVPCount.value, uploadedVPCount.value)
    if (percentage < 100 && expectedVPCount.value > 0) {
        setTimeout(async    () => {
            await snapshotStore.loadVotingPowers(props.snapshot.hash);
        }, 1000);
    } else {
        clearInterval(percentageLoader);
    }
}, 1000*5);
</script>
