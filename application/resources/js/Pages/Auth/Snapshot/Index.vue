<template>
    <AuthenticatedLayout title="Dashboard" :crumbs="crumbs">
        <section class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <div class="flex justify-between w-full">
                        <h2 class="mb-4 text-lg font-semibold leading-tight text-gray-800 xl:text-xl dark:text-gray-200">
                            Snapshots
                        </h2>

                        <div>
                            <Link :href="route('admin.snapshots.create')">
                                <PrimaryButton :theme="'primary'">
                                    New Snapshot
                                    <PlusIcon class="w-5 h-5"/>
                                </PrimaryButton>
                            </Link>
                        </div>
                    </div>

                    <SnapshotList :snapshots="snapshots"/>

                    <div v-if="snapshotsPagination" class="flex flex-row items-center justify-between w-full py-4">
                        <div class="border-2 border-sky-600">
                            <p class="p-4 text-sm text-sky-600 dark:text-gray-300">
                                {{
                                    `Showing ${snapshotsPagination?.from} to ${(snapshotsPagination?.to < snapshotsPagination?.total) ? snapshotsPagination?.to : snapshotsPagination?.total} of ${snapshotsPagination?.total} results`
                                }}
                            </p>
                        </div>

                        <Paginator :pagination="snapshotsPagination"
                                   @paginated="(payload: number) => currPage = payload"
                                   @perPageUpdated="(payload: number) => perPage = payload">
                        </Paginator>
                    </div>

                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>


<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BallotList from "@/Pages/Auth/Ballot/Partials/BallotList.vue"
import Paginator from '@/shared/components/Paginator.vue';
import {useBallotStore} from '@/stores/ballot-store';
import {VARIABLES} from '@/types/variables'
import {ref, watch} from 'vue';
import {storeToRefs} from 'pinia';
import {Link} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {PlusIcon} from "@heroicons/vue/20/solid";
import { useSnapshotStore } from '@/stores/snapshot-store';
import DataQuery from '@/types/data-query';
import SnapshotList from './Partials/SnapshotList.vue';


const props = defineProps<{
    ballots: any;
    crumbs: [];
}>();

let snapshotStore = useSnapshotStore();
snapshotStore.loadSnaphots();
let {snapshotsQueryData, snapshots, snapshotsPagination} = storeToRefs(snapshotStore);

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

