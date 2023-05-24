<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteBallotForm from "@/Pages/Auth/Ballot/Partials/DeleteBallotForm.vue";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import CreateUpdateSnapshotForm from "@/Pages/Auth/Snapshot/Partials/CreateUpdateSnapshotForm.vue";
import DeleteSnapshotForm from "@/Pages/Auth/Snapshot/Partials/DeleteSnapshotForm.vue";

defineProps<{
    snapshot: SnapshotData;
}>();
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Editing <b class="font-bold">{{snapshot.title}}</b> Snapshot</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <CreateUpdateSnapshotForm
                        :snapshot="snapshot"
                        class="w-full"
                    />
                </div>


                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <DeleteSnapshotForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
