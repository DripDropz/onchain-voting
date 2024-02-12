<template>
    <AdminLayout title="Dashboard" :crumbs="crumbs">
        <Head title="Petitions" />

        <section>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4 mt-9">
                        Petitions
                    </h2>

                    <div class="w-full">
                        <div
                            class="flex flex-row items-center justify-between border border-t-0 border-l-0 border-r-0 border-b-1">
                            <ul class="flex flex-row items-center justify-between gap-8 mb-2">
                                <li v-for="option in menuOptions" :key="option.name">
                                    <a @click="changeTab(option.value)" :class="getTabClass(option.value)">
                                        {{ option.name }} ({{ option.count }})
                                    </a>
                                </li>
                            </ul>
<!--                            <div>-->
<!--                                <button-->
<!--                                    class="px-8 py-2 mb-2 font-semibold text-black transition duration-300 ease-in-out border border-black rounded-lg dark:border-white dark:text-white hover:bg-gray-200">Settings</button>-->
<!--                            </div>-->
                        </div>

                        <PetitionListAdmin v-if="petitions" :petitions="currentModel$.data.data" :currentTab="currentModel$.filters.status" />

                        <div v-if="currentModel$.data.data.length > 0"
                            class="flex flex-row items-center justify-between w-full py-4">
                            <div class="border-2 border-sky-600">
                                <p class="p-4 text-sm text-sky-600 dark:text-gray-300">
                                    {{ `Showing ${currentModel$.data.from} to ${(currentModel$.data.to <
                                        currentModel$.data.total) ? currentModel$.data.to : currentModel$.data.total} of
                                                                            ${currentModel$.data.total} results` }} </p>
                            </div>
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
    { name: 'All', value: 'all', count: props.counts.allPetitions },
    { name: 'Review', value: 'r', count: props.counts.pendingCount },
    { name: 'Active', value: 'a', count: props.counts.activeCount }
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
    return {
        'border-b-2 border-sky-300 dark:border-sky-500 font-medium text-sky-300 dark:text-sky-300 focus:outline-none focus:border-sky-700 text-xl hover:cursor-pointer':
            currentModel$.value.filters.status === tabName,
        'border-b-2 border-transparent font-medium text-sky-300 hover:text-sky-500 text-slate-900 dark:hover:text-sky-300 dark:text-slate-200 hover:border-sky-500 hover:cursor-pointer dark:hover:border-sky-300 focus:text-sky-500 dark:focus:text-sky-300 focus:border-sky-500 dark:focus:border-sky-300 text-xl':
           currentModel$.value.filters.status !== tabName,
    };
};

</script>
