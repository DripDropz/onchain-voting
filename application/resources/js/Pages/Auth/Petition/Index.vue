<template>
    <AdminLayout title="Dashboard" :crumbs="crumbs">
        <Head title="Petitions" />

        <section>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-6 mt-9">
                        Petitions
                    </h2>

                    <div class="w-full">
                        <!-- Tab bar -->
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <ul class="flex flex-wrap gap-1 -mb-px">
                                <li v-for="option in menuOptions" :key="option.name">
                                    <button
                                        @click="changeTab(option.value)"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-t-lg transition-colors focus:outline-none"
                                        :class="getTabClass(option.value)"
                                    >
                                        {{ option.name }}
                                        <span
                                            class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-semibold"
                                            :class="currentModel$.filters.status === option.value
                                                ? 'bg-sky-500 text-white'
                                                : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                                        >
                                            {{ option.count }}
                                        </span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <PetitionListAdmin v-if="petitions" :petitions="currentModel$.data.data" :currentTab="currentModel$.filters.status" />

                        <div v-if="currentModel$.data.data.length > 0"
                            class="flex flex-row items-center justify-between w-full py-4 mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Showing {{ currentModel$.data.from }} to {{ currentModel$.data.to < currentModel$.data.total ? currentModel$.data.to : currentModel$.data.total }} of {{ currentModel$.data.total }} results
                            </p>
                            <Paginator :pagination="currentModel$.data"
                                @paginated="(payload: number) => currentModel$.currPage = payload"
                                @perPageUpdated="(payload: number) => currentModel$.perPage = payload">
                            </Paginator>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>

<script lang="ts" setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PetitionListAdmin from "@/Pages/Auth/Petition/Partials/PetitionListAdmin.vue"
import Paginator from '@/shared/components/Paginator.vue';
import { usePetitionStore } from '@/stores/petition-store';
import { storeToRefs } from 'pinia';
import PetitionData = App.DataTransferObjects.PetitionData;
import {Head} from "@inertiajs/vue3";

const props = defineProps<{
    currPage?: number,
    perPage?: number,
    filter: {
        status: string,
    },
    counts: any,
    petitions: {
            links: [],
            total: number,
            to: number,
            from: number,
            data: PetitionData[]
        },
    crumbs: [],
}>();

const menuOptions = [
    { name: 'All',    value: 'all', count: props.counts.allPetitions },
    { name: 'Review', value: 'r',   count: props.counts.pendingCount },
    { name: 'Active', value: 'a',   count: props.counts.activeCount },
];

let petitionStore = usePetitionStore();
petitionStore.setModel({
    data: props.petitions,
    filters: props.filter,
    currPage: props.currPage,
    perPage: props.perPage,
})
let { currentModel$ } = storeToRefs(petitionStore);
currentModel$.value.filters.status = 'all';

const changeTab = (tabName: string) => {
    currentModel$.value.filters.status = tabName;
};

const getTabClass = (tabName: string) => {
    if (currentModel$.value.filters.status === tabName) {
        return 'border-b-2 border-sky-500 text-sky-600 dark:text-sky-400 dark:border-sky-400';
    }
    return 'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600';
};
</script>
