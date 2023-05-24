<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteBallotForm from "@/Pages/Auth/Ballot/Partials/DeleteBallotForm.vue";
import BallotData = App.DataTransferObjects.BallotData;
import CreateUpdateBallotForm from "@/Pages/Auth/Ballot/Partials/CreateUpdateBallotForm.vue";
import BallotQuestions from "@/Pages/Auth/Ballot/Partials/BallotQuestions.vue";

defineProps<{
    ballot: BallotData;
}>();
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Editing <b class="font-bold">{{ballot.title}}</b> Ballot</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <CreateUpdateBallotForm
                        :ballot="ballot"
                        class="w-full"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <BallotQuestions :questions="ballot?.questions" :ballot="ballot" />
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <DeleteBallotForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
