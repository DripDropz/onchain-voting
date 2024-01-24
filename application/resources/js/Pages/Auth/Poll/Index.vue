<template>
    <AuthenticatedLayout title="Dashboard" :crumbs="crumbs">
    <section>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="sm:rounded-lg">
                <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4 mt-9">
                    Polls
                </h2>

                <PollListAdmin :polls="pollsData" />

                <div v-if="pollsPagination" class="flex flex-row items-center justify-between w-full py-4">
                    <div class="border-2 border-sky-600">
                        <p class="p-4 text-sm text-sky-600 dark:text-gray-300">
                            {{ `Showing ${pollsPagination?.from} to ${(pollsPagination?.to <
                                pollsPagination?.total) ? pollsPagination?.to : pollsPagination?.total} of
                                                                ${pollsPagination?.total} results` }} </p>
                    </div>
                    <Paginator :pagination="pollsPagination" @paginated="(payload: number) => currPage = payload"
                        @perPageUpdated="(payload: number) => perPage = payload">
                    </Paginator>
                </div>

            </div>
        </div>
    </section>
    </AuthenticatedLayout>
    </template>

    <script lang="ts" setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
    pollStore.loadPolls();
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
