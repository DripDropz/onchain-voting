<template>
    <AuthenticatedLayout title="Dashboard" :crumbs="crumbs">
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
                                        {{ option.name }} ({{ getCountForTab(option.value) }})
                                    </a>
                                </li>
                            </ul>
                            <div>
                                <button
                                    class="px-8 py-2 mb-2 font-semibold text-black transition duration-300 ease-in-out border border-black rounded-lg dark:border-white dark:text-white hover:bg-gray-200">Settings</button>
                            </div>
                        </div>

                        <div v-if="currentTab === 'all'">
                            <div v-if="filteredPetitions.length > 0">
                                <PetitionListAdmin v-if="petitions" :petitions="all" :currentTab="currentTab" />
                            </div>
                            <div v-else class="flex flex-col items-center justify-center h-[500px] gap-16">
                                <p class="text-2xl font-bold text-center">No petitions</p>
                            </div>
                        </div>

                        <div v-else-if="currentTab === 'pending'">
                            <div>
                                <div v-if="filteredPetitions.length > 0">
                                    <PetitionListAdmin v-if="petitions" :petitions="pending" :currentTab="currentTab" />
                                </div>
                                <div v-else class="flex flex-col items-center justify-center h-[500px] gap-16">
                                    <p class="text-2xl font-bold text-center">No petitions</p>

                                    <Link :href="route('#')"
                                        class="inline-flex items-center px-8 py-2 font-semibold text-white border rounded-md shadow-sm gap-x-2 bg-sky-500 hover:bg-sky-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 border-sky-400">
                                    Login
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div v-if="filteredPetitions.length > 0">
                                <PetitionListAdmin v-if="petitions" :petitions="active" :currentTab="currentTab" />
                            </div>
                            <div v-else class="flex flex-row items-center justify-center">
                                <p class="text-2xl font-bold text-center">No active petitions.</p>
                            </div>
                        </div>




                        <div v-if="petitionsPagination" class="flex flex-row items-center justify-between w-full py-4">
                            <div class="border-2 border-sky-600">
                                <p class="p-4 text-sm text-sky-600 dark:text-gray-300">
                                    {{ `Showing ${petitionsPagination?.from} to ${(petitionsPagination?.to <
                                        petitionsPagination?.total) ? petitionsPagination?.to : petitionsPagination?.total}
                                                                            of ${petitionsPagination?.total} results` }} </p>
                            </div>
                            <Paginator :pagination="petitionsPagination"
                                @paginated="(payload: number) => currPage = payload"
                                @perPageUpdated="(payload: number) => perPage = payload">
                            </Paginator>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
    
<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PetitionListAdmin from "@/Pages/Auth/Petition/Partials/PetitionListAdmin.vue"
import Paginator from '@/shared/components/Paginator.vue';
import { usePetitionStore } from '@/stores/petition-store';
import PetitionsQuery from '@/types/petitions-query';
import { VARIABLES } from '@/types/variables'
import { ref, watch, computed } from 'vue';
import { storeToRefs } from 'pinia';
import PetitionData = App.DataTransferObjects.PetitionData;


const props = defineProps<{
    petitions: any;
    crumbs: [];
    currentTab?: string;
}>();

const currentTab = ref('all');

const all = computed(() => props.petitions);
const pending = computed(() => props.petitions.filter((petition: PetitionData) => petition.status === 'pending'));
const active = computed(() => props.petitions.filter((petition: PetitionData) => petition.status === 'published'));

const getCountForTab = (tabName: string) => {
    return filteredPetitions(tabName).length;
};

const filteredPetitions = (tabName: string) => {
    switch (tabName) {
        case 'all':
            return props.petitions;
        case 'pending':
            return pending.value;
        case 'active':
            return active.value;
        default:
            return [];
    }
};

const changeTab = (tabName: string) => {
    currentTab.value = tabName;
};
const menuOptions = [
    { name: 'All', value: 'all' },
    { name: 'Review', value: 'pending' },
    { name: 'Active', value: 'active' },
]

const getTabClass = (tabName: string) => {
    return {
        'border-b-2 border-sky-300 dark:border-sky-500 font-medium text-sky-300 dark:text-sky-300 focus:outline-none focus:border-sky-700 text-xl hover:cursor-pointer':
            currentTab.value === tabName,
        'border-b-2 border-transparent font-medium text-sky-300 hover:text-sky-500 text-slate-900 dark:hover:text-sky-300 dark:text-slate-200 hover:border-sky-500 hover:cursor-pointer dark:hover:border-sky-300 focus:text-sky-500 dark:focus:text-sky-300 focus:border-sky-500 dark:focus:border-sky-300 text-xl':
            currentTab.value !== tabName,
    };
};

let petitionStore = usePetitionStore();
petitionStore.loadPetitions();
let { petitionsQueryData, petitionsData, petitionsPagination } = storeToRefs(petitionStore);

let currPage = ref<number | null>(null);
let perPage = ref<number | null>(null);
let petitionQueryDataRef = ref<PetitionsQuery | null>(null);

watch([currPage], () => {
    query();
})

watch([perPage], () => {
    currPage.value = null;
    query();
})

function query() {
    const data = getQueryData();
    petitionQueryDataRef.value = data;
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

watch([petitionQueryDataRef], () => {
    petitionsQueryData.value = petitionQueryDataRef.value;
})


</script>