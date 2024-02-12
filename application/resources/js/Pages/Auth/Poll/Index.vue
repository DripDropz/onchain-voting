<template>
    <AdminLayout title="Dashboard" :crumbs="crumbs">
        <section>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <h2 class="mb-4 text-lg font-semibold leading-tight text-gray-800 xl:text-xl dark:text-gray-200 mt-9">
                        Polls
                    </h2>

                    <PollListAdmin :polls="pollsData" />

                    <div v-if="pollsPagination && polls.length > 0"
                        class="flex flex-row items-center justify-between w-full py-4">
                        <div class="border-2 border-sky-600">
                            <p class="p-4 text-sm text-sky-600 dark:text-gray-300">
                                {{ `Showing ${pollsPagination?.from} to ${(pollsPagination?.to < pollsPagination?.total) ?
                                    pollsPagination?.to : pollsPagination?.total} of ${pollsPagination?.total} results` }}
                                    </p>
                        </div>
                        <Paginator :pagination="pollsPagination" @paginated="(payload: number) => currPage = payload"
                            @perPageUpdated="(payload: number) => perPage = payload">
                        </Paginator>
                    </div>
                    <div class="justify-center text-center rounded-lg border-2 mt-5 border-dashed border-gray-300" v-else>
                        <div class="py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No polls created yet
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>

<script lang="ts" setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PollListAdmin from "@/Pages/Auth/Poll/Partials/PollListAdmin.vue"
import Paginator from '@/shared/components/Paginator.vue';
import { usePollStore } from '@/stores/poll-store';
import PollsQuery from '@/types/polls-query';
import { VARIABLES } from '@/types/variables'
import { ref, watch } from 'vue';
import { storeToRefs } from 'pinia';


const props = defineProps<{
    polls: any;
    crumbs: [];
}>();

let pollStore = usePollStore();
pollStore.getAdminPolls();
let { pollsQueryData, pollsData, pollsPagination } = storeToRefs(pollStore);

let currPage = ref<number | null>(null);
let perPage = ref<number | null>(null);
let pollQueryDataRef = ref<PollsQuery | null>(null);

watch([currPage], () => {
    query();
})

watch([perPage], () => {
    currPage.value = null;
    query();
})

function query() {
    const data = getQueryData();
    pollQueryDataRef.value = data;
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

watch([pollQueryDataRef], () => {
    pollsQueryData.value = pollQueryDataRef.value;
})


</script>
