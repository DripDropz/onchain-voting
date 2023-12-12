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
                    <BallotListAdmin :ballots="ballots" @curr-page="(payload) => currPage = payload"
                        @per-page="(payload) => perPage = payload" />
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
            </div>
        </section>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import BallotListAdmin from "@/Pages/Auth/Ballot/Partials/BallotListAdmin.vue";
import SnapshotList from "@/Pages/Auth/Snapshot/Partials/SnapshotList.vue";
import Pagination from "@/types/pagination";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import { VARIABLES } from '@/types/variables'
import axios from 'axios';
import { ref, watch } from 'vue';

const props = defineProps<{
    status?: string;
    ballots: {
        data: BallotData[];
        links: [];
        meta: Pagination;
    };
    snapshots: SnapshotData[];
}>();

let currPage = ref<number | null>(props.ballots.meta.current_page);
let perPage = ref<number | null>(props.ballots.meta.per_page);

let ballots = ref(props.ballots);

watch([currPage,perPage], () => {
    query();
})


function query() {
    const data = {} as any;

    if (currPage.value) {
        data[VARIABLES.PAGE] = currPage.value;
    }
    if (perPage.value) {
        data[VARIABLES.PER_PAGE] = perPage.value;
    }
    router.get(
        route('admin.dashboard'),
        data,
        {preserveState: false, preserveScroll: true}
    );
}

</script>
