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
                        <!-- Tab bar and Sort Controls -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <!-- Tabs -->
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
                                                :class="currentTab === option.value
                                                    ? 'bg-sky-500 text-white'
                                                    : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                                            >
                                                {{ option.count }}
                                            </span>
                                        </button>
                                    </li>
                                </ul>

                                <!-- Sort Controls -->
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-500 dark:text-gray-400">Sort by:</label>
                                    <select
                                        v-model="sortBy"
                                        @change="handleSortChange"
                                        class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                    >
                                        <option value="created_at">Date Created</option>
                                        <option value="title">Title</option>
                                        <option value="status">Status</option>
                                    </select>
                                    <button
                                        @click="toggleSortOrder"
                                        class="p-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        :title="sortOrder === 'asc' ? 'Ascending' : 'Descending'"
                                    >
                                        <svg v-if="sortOrder === 'asc'" class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                        </svg>
                                        <svg v-else class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h5m4 0l4 4m0 0l4-4m-4 4v-12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <PetitionListAdmin v-if="petitions" :petitions="petitionsData" :currentTab="currentTab" />

                        <div v-if="petitionsData.length > 0"
                            class="flex flex-row items-center justify-between w-full py-4 mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Showing {{ petitionsPagination.from }} to {{ petitionsPagination.to < petitionsPagination.total ? petitionsPagination.to : petitionsPagination.total }} of {{ petitionsPagination.total }} results
                            </p>
                            <Paginator :pagination="petitionsPagination"
                                @paginated="(payload: number) => handlePageChange(payload)"
                                @perPageUpdated="(payload: number) => handlePerPageChange(payload)">
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
import PetitionData = App.DataTransferObjects.PetitionData;
import {Head, router} from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps<{
    currPage?: number,
    perPage?: number,
    filter: {
        status: string,
    },
    sort?: {
        sortBy: string,
        sortOrder: string,
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
    { name: 'Review', value: 'review', count: props.counts.pendingCount },
    { name: 'Active', value: 'active', count: props.counts.activeCount },
    { name: 'All',    value: 'all', count: props.counts.allPetitions },
];

// Initialize with default to 'review' (Review - pending + approved)
const currentTab = ref(props.filter?.status ?? 'review');

// Use props directly for data - don't use store for admin pages to avoid caching issues
const petitionsData = computed(() => props.petitions?.data || []);
const petitionsPagination = computed(() => props.petitions || null);

// Current page state
const currPage = ref<number>(props.petitions?.current_page || 1);
const perPage = ref<number>(props.petitions?.per_page || 10);

// Sort state
const sortBy = ref(props.sort?.sortBy ?? 'created_at');
const sortOrder = ref(props.sort?.sortOrder ?? 'desc');

const changeTab = (tabName: string) => {
    currentTab.value = tabName;
    currPage.value = 1; // Reset to first page on tab change
    reloadData();
};

const handleSortChange = () => {
    currPage.value = 1; // Reset to first page on sort change
    reloadData();
};

const toggleSortOrder = () => {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    handleSortChange();
};

const handlePageChange = (page: number) => {
    currPage.value = page;
    reloadData();
};

const handlePerPageChange = (perPageNum: number) => {
    perPage.value = perPageNum;
    currPage.value = 1; // Reset to first page
    reloadData();
};

const reloadData = () => {
    const params: any = {
        page: currPage.value || 1,
        perPage: perPage.value || 10,
        status: currentTab.value || 'review',
        sortBy: sortBy.value,
        sortOrder: sortOrder.value,
    };

    router.get(route('admin.petitions.index'), params, {
        preserveState: false,
        preserveScroll: true,
    });
};

const getTabClass = (tabName: string) => {
    if (currentTab.value === tabName) {
        return 'border-b-2 border-sky-500 text-sky-600 dark:text-sky-400 dark:border-sky-400';
    }
    return 'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600';
};
</script>
