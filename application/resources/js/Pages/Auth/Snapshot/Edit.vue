<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Editing <b class="font-bold">{{snapshot.title}}</b> Snapshot</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <CreateUpdateSnapshotForm :snapshot="snapshot" class="w-full" />
                </section>

                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <VotingPowerList :powers="votingPowers" v-if="votingPowers?.length > 0"></VotingPowerList>
                    <VotingPowerImporterComponent v-else :snapshot="snapshot"/>
                </section>

                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <DeleteSnapshotForm class="max-w-xl" />
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from "vue";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import VotingPowerData = App.DataTransferObjects.VotingPowerData;
import CreateUpdateSnapshotForm from "@/Pages/Auth/Snapshot/Partials/CreateUpdateSnapshotForm.vue";
import DeleteSnapshotForm from "@/Pages/Auth/Snapshot/Partials/DeleteSnapshotForm.vue";
import VotingPowerList from './Partials/VotingPowerList.vue';
import SnapshotService from './Services/SnapshotService';
import VotingPowerImporterComponent from '@/Components/VotingPowerImporterComponent.vue';

const props = defineProps<{
    snapshot: SnapshotData;
}>();

let votingPowers = ref<VotingPowerData[]>([]);
if (props.snapshot.hash) {
    SnapshotService.getSnapshotVotingPowers(props.snapshot.hash)
    .then((paginatedResponse) => {
        votingPowers.value = paginatedResponse.data;
    });
}
</script>
