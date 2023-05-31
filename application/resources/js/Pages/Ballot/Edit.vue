<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteBallotForm from "@/Pages/Ballot/Partials/DeleteBallotForm.vue";
import BallotData = App.DataTransferObjects.BallotData;
<<<<<<< Updated upstream:application/resources/js/Pages/Ballot/Edit.vue
import CreateUpdateBallotForm from "@/Pages/Ballot/Partials/CreateUpdateBallotForm.vue";
import BallotQuestions from "@/Pages/Ballot/Partials/BallotQuestions.vue";
=======
import CreateUpdateBallotForm from "@/Pages/Auth/Ballot/Partials/CreateUpdateBallotForm.vue";
import BallotQuestions from "@/Pages/Auth/Ballot/Partials/BallotQuestions.vue";
import BallotSnapshot from './Partials/BallotSnapshot.vue';
>>>>>>> Stashed changes:application/resources/js/Pages/Auth/Ballot/Edit.vue

defineProps<{
    ballot: BallotData;
}>();
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
<<<<<<< Updated upstream:application/resources/js/Pages/Ballot/Edit.vue
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Ballot</h2>
=======
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Editing <b class="font-bold">{{ballot.title}}</b> Ballot</h2>
>>>>>>> Stashed changes:application/resources/js/Pages/Auth/Ballot/Edit.vue
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <CreateUpdateBallotForm
                        :ballot="ballot"
                        class="max-w-xl"
                    />
                </div>

<<<<<<< Updated upstream:application/resources/js/Pages/Ballot/Edit.vue
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <BallotQuestions class="max-w-xl"  :ballot="ballot" />
=======
                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <BallotQuestions :questions="ballot?.questions" :ballot="ballot" />
>>>>>>> Stashed changes:application/resources/js/Pages/Auth/Ballot/Edit.vue
                </div>


                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <BallotSnapshot :ballot="ballot" />
                </div>

                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <DeleteBallotForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
