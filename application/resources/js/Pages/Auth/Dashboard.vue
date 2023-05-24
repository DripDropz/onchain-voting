<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-semibold text-xl xl:text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h1>
        </template>

        <section class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                        Ballots
                    </h2>
                    <BallotListAdmin :ballots="ballots" />
                </div>
            </div>
        </section>

        <section class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                        Snapshots
                    </h2>
                    <SnapshotList :snapshots="snapshots" />
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import BallotListAdmin from "@/Pages/Auth/Ballot/Partials/BallotListAdmin.vue";
import SnapshotList from "@/Pages/Auth/Snapshot/Partials/SnapshotList.vue";
import SnapshotData = App.DataTransferObjects.SnapshotData;

const props = defineProps<{
    status?: string;
    ballots: BallotData[];
    snapshots: SnapshotData[];
}>();
</script>
