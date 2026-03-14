<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <section class="py-6">
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
                <div class="flex justify-end mt-4" v-if="ballots.length > 0">
                    <Link :href="route('admin.ballots.index')"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="w-full">View all ballots</span>
                    <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                    </Link>
                </div>
                <div class="justify-center text-center rounded-lg border-2 border-dashed border-gray-300 p-4 mt-4" v-else>
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No Ballots</h3>
                    <div class="mt-4">
                        <Link :href="route('admin.ballots.create')">
                        <PrimaryButton :theme="'primary'">
                            Create New Ballot
                            <PlusIcon class="w-5 h-5" />
                        </PrimaryButton>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <div class="flex justify-between items-center w-full mb-4">
                        <div>
                            <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight">
                                Petitions Awaiting Review
                            </h2>
                            <p v-if="petitionCounts?.awaitingReview > 0" class="text-sm text-sky-600 dark:text-sky-400 mt-1">
                                {{ petitionCounts.awaitingReview }} petition{{ petitionCounts.awaitingReview === 1 ? '' : 's' }} need admin approval
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ petitions.length }} of {{ petitionCounts?.awaitingReview ?? 0 }} showing
                            </span>
                            <Link :href="route('admin.petitions.index')">
                                <PrimaryButton :theme="'secondary'" class="text-sm">
                                    Manage Petitions
                                    <ArrowRightIcon class="w-4 h-4 ml-1" />
                                </PrimaryButton>
                            </Link>
                        </div>
                    </div>
                    <PetitionListAdmin v-if="petitions.length > 0" :petitions="petitions" />
                </div>
                <div class="flex justify-end mt-4" v-if="petitions.length > 0 && petitionCounts?.awaitingReview > petitions.length">
                    <Link :href="route('admin.petitions.index')"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="w-full">View all {{ petitionCounts.awaitingReview }} petitions awaiting review</span>
                    <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                    </Link>
                </div>
                <div v-else-if="petitions.length === 0" class="justify-center text-center rounded-lg border-2 border-dashed border-gray-300 p-4">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No petitions awaiting review</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All petitions have been reviewed.</p>
                </div>
            </div>
        </section>

        <section class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <div class="flex justify-between items-center w-full mb-4">
                        <div>
                            <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight">
                                Polls Awaiting Review
                            </h2>
                            <p v-if="pollCounts?.awaitingReview > 0" class="text-sm text-sky-600 dark:text-sky-400 mt-1">
                                {{ pollCounts.awaitingReview }} poll{{ pollCounts.awaitingReview === 1 ? '' : 's' }} need admin approval
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ polls.length }} of {{ pollCounts?.awaitingReview ?? 0 }} showing
                            </span>
                            <Link :href="route('admin.polls.index')">
                                <PrimaryButton :theme="'secondary'" class="text-sm">
                                    Manage Polls
                                    <ArrowRightIcon class="w-4 h-4 ml-1" />
                                </PrimaryButton>
                            </Link>
                        </div>
                    </div>
                    <PollListAdmin v-if="polls.length > 0" :polls="polls" />
                </div>
                <div class="flex justify-end mt-4" v-if="polls.length > 0 && pollCounts?.awaitingReview > polls.length">
                    <Link :href="route('admin.polls.index')"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="w-full">View all {{ pollCounts.awaitingReview }} polls awaiting review</span>
                    <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                    </Link>
                </div>
                <div v-else-if="polls.length === 0" class="justify-center text-center rounded-lg border-2 border-dashed border-gray-300 p-4">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No polls awaiting review</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All polls have been reviewed.</p>
                </div>
            </div>
        </section>

        <section class="py-6">
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
                <div class="flex justify-end mt-4" v-if="snapshots.length > 0">
                    <Link :href="route('admin.snapshots.index')"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-500 rounded-lg bg-gray-50 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="w-full">View all snapshots</span>
                    <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                    </Link>
                </div>
                <div class="justify-center text-center rounded-lg border-2 border-dashed border-gray-300 p-4 mt-4" v-else>
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No Snapshots</h3>
                    <div class="mt-4">
                        <Link :href="route('admin.snapshots.create')">
                        <PrimaryButton :theme="'primary'">
                            New Snapshot
                            <PlusIcon class="w-5 h-5" />
                        </PrimaryButton>
                        </Link>
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
import { PlusIcon, ArrowRightIcon } from "@heroicons/vue/20/solid";

const props = defineProps<{
    snapshots: SnapshotData[];
    ballots: BallotData[];
    petitions: PetitionData[];
    polls: PollData[];
    petitionCounts?: {
        awaitingReview: number;
        total: number;
    };
    pollCounts?: {
        awaitingReview: number;
        total: number;
    };
}>();

</script>
