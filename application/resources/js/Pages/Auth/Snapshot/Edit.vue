<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import SnapshotData = App.DataTransferObjects.SnapshotData;
import CreateUpdateSnapshotForm from "@/Pages/Auth/Snapshot/Partials/CreateUpdateSnapshotForm.vue";
import DeleteSnapshotForm from "@/Pages/Auth/Snapshot/Partials/DeleteSnapshotForm.vue";
import VotingPowerImporter from "@/Pages/Auth/Snapshot/Partials/VotingPowerImporter.vue";
import VotingPowerList from './Partials/VotingPowerList.vue';

defineProps<{
    snapshot: SnapshotData;
}>();
const votingPower = null; // @todo ref() votign power from snapshot store here
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Editing <b class="font-bold">{{snapshot.title}}</b> Snapshot</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <CreateUpdateSnapshotForm
                        :snapshot="snapshot"
                        class="w-full"
                    />
                </section>

                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <VotingPowerList v-if="votingPower"></VotingPowerList>
                    <VotingPowerImporter v-else class="max-w-xl" />
                </section>

                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <DeleteSnapshotForm class="max-w-xl" />
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
