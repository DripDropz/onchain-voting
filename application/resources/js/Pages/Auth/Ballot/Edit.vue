<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteBallotForm from "@/Pages/Auth/Ballot/Partials/DeleteBallotForm.vue";
import BallotData = App.DataTransferObjects.BallotData;
import CreateUpdateBallotForm from "@/Pages/Auth/Ballot/Partials/CreateUpdateBallotForm.vue";
import BallotQuestions from "@/Pages/Auth/Ballot/Partials/BallotQuestions.vue";
import BallotSnapshot from './Partials/BallotSnapshot.vue';
import BallotPolicies from './Partials/BallotPolicies.vue';
import Nav from '../Breadcrumbs.vue';

const props = defineProps<{
    ballot: BallotData;
    addresses: {
        registrationPolicyAddress: string;
        votingPolicyAddress: string;
    };
    crumbs: []
}>();
</script>

<template>
    <Head title="Ballot" />

    <AdminLayout>
        <template #header>
            <Nav :crumbs="props.crumbs"/>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <CreateUpdateBallotForm :ballot="ballot" class="w-full" />
                </div>

                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <BallotQuestions :questions="ballot?.questions || []" :ballot="ballot" />
                </div>

                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <BallotSnapshot :ballot="ballot" />
                </div>


                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <BallotPolicies :ballot="ballot" :addresses="addresses"/>
                </div>

                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <DeleteBallotForm class="max-w-xl" :ballot="ballot" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
