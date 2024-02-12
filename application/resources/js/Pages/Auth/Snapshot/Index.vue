<template>
    <AdminLayout title="Dashboard" :crumbs="crumbs">
        <section class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <div class="flex justify-between w-full">
                        <h2 class="mb-4 text-lg font-semibold leading-tight text-gray-800 xl:text-xl dark:text-gray-200">
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

                    <div v-if="snapshotsPagination && snapshots.length > 0"
                        class="flex flex-row items-center justify-between w-full py-4">
                        <div class="border-2 border-sky-600">
                            <p class="p-4 text-sm text-sky-600 dark:text-gray-300">
                                {{
                                    `Showing ${snapshotsPagination?.from} to ${(snapshotsPagination?.to <
                                        snapshotsPagination?.total) ? snapshotsPagination?.to : snapshotsPagination?.total} of
                                                                    ${snapshotsPagination?.total} results` }} </p>
                        </div>

                        <Paginator :pagination="snapshotsPagination" @paginated="(payload: number) => currPage = payload"
                            @perPageUpdated="(payload: number) => perPage = payload">
                        </Paginator>
                    </div>
                    <div class="justify-center text-center rounded-lg border-2 border-dashed border-gray-300" v-else>
                        <div class="py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
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
            </div>
        </section>
    </AdminLayout>
</template>


<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BallotList from "@/Pages/Auth/Ballot/Partials/BallotList.vue"
import Paginator from '@/shared/components/Paginator.vue';
import { useBallotStore } from '@/stores/ballot-store';
import { VARIABLES } from '@/types/variables'
import { ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { PlusIcon } from "@heroicons/vue/20/solid";
import { useSnapshotStore } from '@/stores/snapshot-store';
import DataQuery from '@/types/data-query';
import SnapshotList from './Partials/SnapshotList.vue';


const props = defineProps<{
    ballots: any;
    crumbs: [];
}>();

let snapshotStore = useSnapshotStore();
snapshotStore.loadSnaphots();
let { snapshotsQueryData, snapshots, snapshotsPagination } = storeToRefs(snapshotStore);

let currPage = ref<number | null>(null);
let perPage = ref<number | null>(null);
let snapshotsQueryDataRef = ref<DataQuery | null>(null);

watch([currPage], () => {
    query();
})

watch([perPage], () => {
    currPage.value = null;
    query();
})

function query() {
    const data = getQueryData();
    snapshotsQueryDataRef.value = data;
}

function getQueryData() {
    const data = <any>{};
    if (currPage.value) {
        data[VARIABLES.PAGE] = currPage.value;
    }
    if (perPage.value) {
        data[VARIABLES.PER_PAGE] = perPage.value;
    }

    return data;
}

watch([snapshotsQueryDataRef], () => {
    snapshotsQueryData.value = snapshotsQueryDataRef.value;
})


</script>

