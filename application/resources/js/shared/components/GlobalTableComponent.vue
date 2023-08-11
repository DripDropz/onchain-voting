<template>
    <div class="w-full flex items-center justify-end mb-4 gap-2">
        <div class="text-xs w-[240px] lg:w-[330px] lg:text-base">
            <Multiselect
                placeholder="Sort"
                value-prop="value"
                label="label"
                v-model="selectedSortRef"
                :options="sorts"
                :classes="{
                    container: 'multiselect border-0 p-0.5 flex-wrap rounded-sm text-gray-900 dark:text-gray-100 bg-gray-200 dark:bg-gray-900',
                    containerActive: 'shadow-none shadow-transparent box-shadow-none',
                    option: 'border-0 px-4 py-2 bg-gray-0 dark:bg-gray-800',
                    optionSelected: 'bg-gray-200 dark:text-gray-100 dark:bg-gray-900',
                    optionSelectedPointed: 'pointer-events-none  bg-gray-200 dark:bg-gray-900',
                    optionPointed: 'bg-gray-200 dark:bg-gray-900'
                }"
            />
        </div>
    </div>
    <table class="w-full table-auto text-gray-600 dark:text-gray-100">
        <slot name="header" :columns="props.columns">
            <thead>
                <tr >
                    <th v-for="item in columns" :key="item"
                            class="p-2 border border-slate-300">
                        <td>{{ item }}</td>
                    </th>
                </tr>
            </thead>
        </slot>
        <slot name="body" :rows="props.data">
            <tbody>
                <tr v-for="(row, index) in props.data" :key="index">
                    <td v-for="colValue in row" class="p-2 border border-slate-300">
                        {{ colValue }} 
                    </td>
                </tr>
            </tbody>
        </slot>
    </table>
    <slot name="footer">
        <div class="w-full pt-4 flex flex-row justify-between items-center">
            <div class="border-2 border-indigo-600">
                <p class="text-sm text-gray-900 dark:text-gray-300 p-4">
                    {{ `Showing ${props.pagination.from} to ${(props.pagination.to < props.pagination.total) ? props.pagination.to : props.pagination.total} of ${props.pagination.total} results` }}
                </p>
            </div>
            <Paginator :pagination="pagination"
                @paginated="(payload: number) => currPage = payload"
                @perPageUpdated="(payload: number) => perPage = payload">
            </Paginator>
        </div>
    </slot>
</template>
<script setup lang="ts">
import Paginator from '@/shared/components/Paginator.vue';
import { VARIABLES } from '@/types/variables'
import { ComputedRef, computed, ref, watch } from 'vue';
import Pagination from "@/types/pagination";
import Multiselect from '@vueform/multiselect';


/// props and class properties
const props = withDefaults(
    defineProps<{
        data: any;
        columns: string[];
        pagination: Pagination;
        sorts?: ({label:string, value:string})[],
        sort?: string,
    }>(), {
        sorts: () => [
            {
                label: 'Voting Power: High to Low',
                value: 'voting_power:desc',
            },
            {
                label: 'Voting Power: Low to High',
                value: 'voting_power:asc',
            },
        ]
    });

let pagination: ComputedRef<Pagination> = computed(() => props.pagination)
let currPage = ref<number|null>(null);
let perPage = ref<number|null>(null);
let selectedSortRef = ref<string|null|undefined>(props.sort);


const emit = defineEmits<{
    (e: 'query-updated', perPage: {}): void
}>();

watch([currPage], () => {
    query();
})

watch([perPage, selectedSortRef], () => {
    currPage.value = null;
    query();
})

function query() {
    const data = getQueryData();
    emit('query-updated', data);
   
}

function getQueryData() {
    const data = <any>{};
    if (currPage.value) {
        data[VARIABLES.PAGE] = currPage.value;
    }
    if (perPage.value) {
        data[VARIABLES.PER_PAGE] = perPage.value;
    }
    if (!!selectedSortRef.value && selectedSortRef.value.length > 3) {
        data[VARIABLES.SORTS] = selectedSortRef.value;
    }
    
    return data;
}
</script>