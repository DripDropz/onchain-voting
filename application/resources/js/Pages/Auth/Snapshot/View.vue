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
                    <VotingPowerList v-if="votingPowers?.length > 0"/>
                    <VotingPowerImporterComponent v-else class="max-w-xl" :snapshot="snapshot"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ComputedRef } from "vue";
import { usePage } from "@inertiajs/vue3";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import VotingPowerData = App.DataTransferObjects.VotingPowerData;
import SnapshotCard from "@/Pages/Auth/Snapshot/Partials/SnapshotCard.vue";
import AlertService from '@/shared/Services/alert-service';
import VotingPowerList from './Partials/VotingPowerList.vue';
import VotingPowerImporterComponent from '@/Components/VotingPowerImporterComponent.vue';
import { useSnapshotStore } from '@/stores/snapshot-store';

const props = defineProps<{
    snapshot: SnapshotData;
}>();

let snapshotHash:ComputedRef<any> = computed(() => props.snapshot.hash);

if (usePage().props?.errors) {
    AlertService.show(Object.values(usePage().props.errors), 'info');
}

let snapshotStore = useSnapshotStore();
snapshotStore.loadVotingPowers(snapshotHash.value);
const votingPowers: ComputedRef<VotingPowerData[]> = computed(() => snapshotStore.votingPowersData);
</script>
