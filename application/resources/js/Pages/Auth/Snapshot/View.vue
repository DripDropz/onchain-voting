<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import VotingPowerData = App.DataTransferObjects.VotingPowerData;
import SnapshotCard from "@/Pages/Auth/Snapshot/Partials/SnapshotCard.vue";
import AlertService from '@/shared/Services/alert-service';
import VotingPowerList from './Partials/VotingPowerList.vue';
import VotingPowerImporterComponent from '@/Components/VotingPowerImporterComponent.vue';
import SnapshotService from './Services/SnapshotService';

const props = defineProps<{
    snapshot: SnapshotData;
}>();

if (usePage().props?.errors) {
    AlertService.show(Object.values(usePage().props.errors), 'info');
}

let votingPowers = ref<VotingPowerData[]>([]);
if (props.snapshot.hash) {
    SnapshotService.getSnapshotVotingPowers(props.snapshot.hash)
    .then((paginatedResponse) => {
        votingPowers.value = paginatedResponse.data;
    });
}
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Viewing
                    <b>{{ snapshot.title }}</b> Snapshot</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <SnapshotCard :snapshot="snapshot" class="max-w-xl" />
                </div>

                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <VotingPowerList :powers="votingPowers" v-if="votingPowers?.length > 0"></VotingPowerList>
                    <VotingPowerImporterComponent v-else :snapshot="snapshot"/>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
