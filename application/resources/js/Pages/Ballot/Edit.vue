<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UpdateBallotForm from "@/Pages/Ballot/Partials/UpdateBallotForm.vue";
import DeleteBallotForm from "@/Pages/Ballot/Partials/DeleteBallotForm.vue";
import BallotData = App.DataTransferObjects.BallotData;

defineProps<{
    ballot: BallotData;
}>();
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Ballot</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <UpdateBallotForm
                        :ballot="ballot"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <DeleteBallotForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
