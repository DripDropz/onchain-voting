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
                    <BallotListAdmin v-if="ballots" :ballots="ballots" /> 

                </div>
                <div class="flex justify-end mt-8">
                        <Link :href="route('admin.ballots.index')" class="inline-flex items-center justify-center p-5 text-base font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-full">View all ballots</span>
                            <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </Link>
                    </div>
            </div>
        </section>

        <section class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                        Petitions
                    </h2>
                    <PetitionListAdmin v-if="petitions" :petitions="petitions" />
                </div>
                <div class="flex justify-end mt-8">
                        <Link :href="route('admin.petitions.index')" class="inline-flex items-center justify-center p-5 text-base font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-full">View all petitions</span>
                            <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </Link>
                    </div>
            </div>
        </section>

        <section class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                        Polls
                    </h2>
                    <PollListAdmin v-if="polls" :polls="polls" />
                </div>
                <div class="flex justify-end mt-8">
                        <Link :href="route('admin.polls.index')" class="inline-flex items-center justify-center p-5 text-base font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-full">View all polls</span>
                            <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </Link>
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
                <div class="flex justify-end mt-8">
                        <Link :href="route('admin.snapshots.index')" class="inline-flex items-center justify-center p-5 text-base font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-full">View all snapshots</span>
                            <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </Link>
                    </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import BallotListAdmin from "@/Pages/Auth/Ballot/Partials/BallotListAdmin.vue";
import PollListAdmin from "@/Pages/Auth/Poll/Partials/PollListAdmin.vue";
import BallotList from "@/Pages/Auth/Ballot/Partials/BallotList.vue"
import PetitionListAdmin from "@/Pages/Auth/Petition/Partials/PetitionListAdmin.vue";
import SnapshotList from "@/Pages/Auth/Snapshot/Partials/SnapshotList.vue";
import Pagination from "@/types/pagination";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import PetitionData = App.DataTransferObjects.PetitionData;
import PollData = App.DataTransferObjects.PollData;
import { Link } from "@inertiajs/vue3";

const props = defineProps<{
    snapshots: SnapshotData[];
    ballots: BallotData[];
    petitions: PetitionData[];
    polls: PollData[];
}>();

</script>
