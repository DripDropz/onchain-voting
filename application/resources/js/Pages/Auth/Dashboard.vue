<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <section class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <div class="flex flex-row justify-between w-full">
                        <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                            Ballots
                        </h2>
                        <div v-if="ballots.length > 0">
                            <Link :href="route('admin.ballots.create')">
                            <PrimaryButton :theme="'primary'">
                                New Ballot
                                <PlusIcon class="w-5 h-5" />
                            </PrimaryButton>
                            </Link>
                        </div>
                    </div>

                    <BallotListAdmin v-if="ballots" :ballots="ballots" />

                </div>
                <div class="flex justify-end mt-8" v-if="ballots.length > 0">
                    <Link :href="route('admin.ballots.index')"
                        class="inline-flex items-center justify-center p-5 text-base font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="w-full">View all ballots</span>
                    <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                    </Link>
                </div>
                <div class="justify-center text-center rounded-lg border-2 border-dashed border-gray-300" v-else>
                    <div class="py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No Ballots</h3>
                        <div class="mt-6 pb-3">
                            <Link :href="route('admin.ballots.create')">
                            <PrimaryButton :theme="'primary'">
                                Create New Ballot
                                <PlusIcon class="w-5 h-5" />
                            </PrimaryButton>
                            </Link>
                        </div>
                    </div>
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
                <div class="flex justify-end mt-8" v-if="petitions.length > 0">
                    <Link :href="route('admin.petitions.index')"
                        class="inline-flex items-center justify-center p-5 text-base font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="w-full">View all petitions</span>
                    <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                    </Link>
                </div>
                <div class="justify-center text-center rounded-lg border-2 border-dashed border-gray-300" v-else>
                    <div class="p-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No polls created yet</h3>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <div class="flex justify-between w-full">
                        <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                            Polls
                        </h2>
                    </div>
                    <PollListAdmin v-if="polls" :polls="polls" />
                </div>
                <div class="flex justify-end mt-8" v-if="polls.length > 0">
                    <Link :href="route('admin.polls.index')"
                        class="inline-flex items-center justify-center p-5 text-base font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="w-full">View all polls</span>
                    <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                    </Link>
                </div>
                <div class="justify-center text-center rounded-lg border-2 border-dashed border-gray-300" v-else>
                    <div class="p-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No polls created yet</h3>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <div class="flex justify-between w-full">
                        <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                            Snapshots
                        </h2>

                        <div v-if="snapshots.length > 0">
                            <Link :href="route('admin.snapshots.create')">
                            <PrimaryButton :theme="'primary'">
                                New Snapshot
                                <PlusIcon class="w-5 h-5" />
                            </PrimaryButton>
                            </Link>
                        </div>
                    </div>
                    <SnapshotList :snapshots="snapshots" />
                </div>
                <div class="flex justify-end mt-8" v-if="snapshots.length > 0">
                    <Link :href="route('admin.snapshots.index')"
                        class="inline-flex items-center justify-center p-5 text-base font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="w-full">View all snapshots</span>
                    <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                    </Link>
                </div>
                <div class="justify-center text-center rounded-lg border-2 border-dashed border-gray-300" v-else>
                    <div class="py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No Snapshots</h3>
                        <div class="mt-6 pb-3">
                            <Link :href="route('admin.snapshots.create')">
                            <PrimaryButton :theme="'primary'">
                                New Snapshot
                                <PlusIcon class="w-5 h-5" />
                            </PrimaryButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>
<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import BallotListAdmin from "@/Pages/Auth/Ballot/Partials/BallotListAdmin.vue";
import PollListAdmin from "@/Pages/Auth/Poll/Partials/PollListAdmin.vue";
import PetitionListAdmin from "@/Pages/Auth/Petition/Partials/PetitionListAdmin.vue";
import SnapshotList from "@/Pages/Auth/Snapshot/Partials/SnapshotList.vue";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import PetitionData = App.DataTransferObjects.PetitionData;
import PollData = App.DataTransferObjects.PollData;
import { Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { PlusIcon } from "@heroicons/vue/20/solid";

const props = defineProps<{
    snapshots: SnapshotData[];
    ballots: BallotData[];
    petitions: PetitionData[];
    polls: PollData[];
}>();

</script>
