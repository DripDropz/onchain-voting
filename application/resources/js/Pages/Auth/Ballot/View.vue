<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import BallotQuestions from "@/Pages/Auth/Ballot/Partials/BallotQuestions.vue";
import BallotCard from "@/Pages/Auth/Ballot/Partials/BallotCard.vue";
import { usePage } from "@inertiajs/vue3";
import AlertService from '@/shared/Services/alert-service';
import BallotSnapshot from './Partials/BallotSnapshot.vue';
import BallotPublishChecklist from './Partials/BallotPublishChecklist.vue';
import Nav from '../Breadcrumbs.vue';
import BallotPolicies from './Partials/BallotPolicies.vue';

const props = defineProps<{
    ballot: BallotData;
    addresses: {
        registrationPolicyAddress: string;
        votingPolicyAddress: string;
    };
    crumbs: []
}>();

AlertService.show(Object.values(usePage().props.errors), 'info');
</script>

<template>
    <Head title="Ballot" />

    <AdminLayout>
        <template #header>
            <Nav :crumbs="props.crumbs"/>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="flex gap-8 p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <BallotCard :ballot="ballot" class="w-1/2 max-w-xl" />
                    <div class="w-1/2 ">
                        <BallotPublishChecklist :ballot="ballot" class="" />
                    </div>
                </div>

                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg" id="ballot-questions">
                    <BallotQuestions class="" :ballot="ballot" :questions="ballot?.questions || []" />
                </div>

                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg" >
                    <BallotSnapshot :ballot="ballot" />
                </div>
                <div id="policy-section" class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <BallotPolicies :ballot="ballot" :addresses="addresses"/>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
