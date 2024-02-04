<template>
    <AuthenticatedLayout title="Dashboard" :crumbs="crumbs">
        <section class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <div class="flex justify-between w-full">
                        <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
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

                    <BallotList :ballots="ballotsData"/>

                    <div v-if="ballotsPagination" class="flex flex-row items-center justify-between w-full py-4">
                        <div class="border-2 border-sky-600">
                            <p class="p-4 text-sm text-sky-600 dark:text-gray-300">
                                {{
                                    `Showing ${ballotsPagination?.from} to ${(ballotsPagination?.to < ballotsPagination?.total) ? ballotsPagination?.to : ballotsPagination?.total} of ${ballotsPagination?.total} results`
                                }}
                            </p>
                        </div>

                        <Paginator :pagination="ballotsPagination"
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
import BallotsQuery from '@/types/ballots-query';
import {VARIABLES} from '@/types/variables'
import {ref, watch} from 'vue';
import {storeToRefs} from 'pinia';
import {Link} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {PlusIcon} from "@heroicons/vue/20/solid";


const props = defineProps<{
    ballots: any;
    crumbs: [];
}>();

let ballotStore = useBallotStore();
ballotStore.loadBallots();
let {ballotsQueryData, ballotsData, ballotsPagination} = storeToRefs(ballotStore);

let currPage = ref<number | null>(null);
let perPage = ref<number | null>(null);
let ballotsQueryDataRef = ref<BallotsQuery | null>(null);

watch([currPage], () => {
    query();
})

watch([perPage], () => {
    currPage.value = null;
    query();
})

function query() {
    const data = getQueryData();
    ballotsQueryDataRef.value = data;
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

watch([ballotsQueryDataRef], () => {
    ballotsQueryData.value = ballotsQueryDataRef.value;
})


</script>

