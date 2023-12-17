<template>
    <div class="flex flex-row justify-end gap-8">
        <div class="w-28 relative top-3 flex flex-col items-center gap-1">
            <Multiselect :placeholder="perPageRef.toString()" v-model="perPage" :can-clear="false"
                :options="[5, 6, 10, 20, 40, 60, 80, 100]" :mode="'single'" :classes="{
                    container: 'multiselect border-0 px-1 py-1 flex-wrap rounded-sm text-gray-900 dark:text-gray-100 bg-sky-100 dark:bg-gray-900',
                    containerActive: 'shadow-none shadow-transparent box-shadow-none',
                    option: 'border-0 px-4 py-1 bg-gray-0 dark:bg-gray-800',
                    optionSelected: 'bg-sky-100 dark:text-gray-100 dark:bg-gray-900',
                    optionSelectedPointed: 'pointer-events-none  bg-sky-100 dark:bg-gray-900',
                    optionPointed: 'bg-sky-100 dark:bg-gray-900'
                }" />
            <p class="text-slate-400 text-sm">{{ 'Per Page' }}</p>
        </div>

        <div class="relative top-3">
            <nav class="flex items-center">
                <!-- previous -->
                <div class="flex">
                    <a href="#" @click.prevent="currPage = prev?.page" :class="{
                        'opacity-50 cursor-not-allowed': !prev?.page,
                        'hover:border-sky-500 hover:text-sky-500': prev?.page
                    }"
                        class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-slate-500">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M18 10a.75.75 0 01-.75.75H4.66l2.1 1.95a.75.75 0 11-1.02 1.1l-3.5-3.25a.75.75 0 010-1.1l3.5-3.25a.75.75 0 111.02 1.1l-2.1 1.95h12.59A.75.75 0 0118 10z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ 'Previous' }}
                    </a>
                </div>
                <!-- pages -->
                <div class="hidden md:flex" v-if="totalPages > 7">
                    <template v-for="page in pages">
                        <span
                            class="inline-flex items-center border-t-2 border-sky-600 px-4 pt-4 text-sm font-medium text-sky-600"
                            v-if="page.active">
                            {{ page.label }}
                        </span>
                        <span href="#" v-else-if="page.label === '...'"
                            class="inline-flex items-center px-4 pt-4 text-sm font-medium text-slate-500">
                            {{ page.label }}
                        </span>
                        <a href="#" v-else="" @click.prevent="currPage = page.page"
                            class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-slate-500 hover:border-sky-500 hover:text-sky-500">
                            {{ page.label }}
                        </a>
                    </template>
                </div>
                <div v-else>
                    <template v-for="page in pages">
                        <span class="inline-flex items-center px-4 pt-4 text-sm font-medium border-sky-600 text-sky-600"
                            v-if="page.active">
                            {{ page.label }}
                        </span>
                        <a href="#" v-else @click.prevent="currPage = page.page"
                            class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-slate-500 hover:border-sky-500 hover:text-sky-500">
                            {{ page.label }}
                        </a>
                    </template>
                </div>
                <!-- next -->
                <div class="flex">
                    <a href="#" @click.prevent="currPage = next.page" :class="{
                        'opacity-40 cursor-not-allowed': !next?.page,
                        'hover:border-sky-500 hover:text-sky-500': next?.page
                    }"
                        class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-slate-500">
                        {{ 'Next' }}
                        <svg class="ml-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M2 10a.75.75 0 01.75-.75h12.59l-2.1-1.95a.75.75 0 111.02-1.1l3.5 3.25a.75.75 0 010 1.1l-3.5 3.25a.75.75 0 11-1.02-1.1l2.1-1.95H2.75A.75.75 0 012 10z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </nav>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { defineEmits, computed, ref, watch, ComputedRef, onMounted } from "vue";
import Multiselect from '@vueform/multiselect';
import Pagination from "@/types/pagination";
import { onUpdated } from "vue";

const props = defineProps<{
    pagination: Pagination;
}>();

let currPageRef = computed(() => props.pagination.current_page);
let perPageRef = computed(() => props.pagination.per_page);
let totalPages = computed(() => props.pagination.last_page);

let currPage = ref<any>(currPageRef.value);
let perPage = ref<any>(perPageRef.value);

const emit = defineEmits<{
    (e: 'paginated', page: number): void
    (e: 'per-page-updated', perPage: number): void
}>();

watch(currPage, () => {
    emit('paginated', currPage.value);
});

watch(perPage, () => {
    currPage.value = 1;
    perPage.value != perPageRef.value ? emit('per-page-updated', perPage.value) : null;
});

const prev = computed(() => {
    let previousPage = props.pagination.current_page - 1 > 0 ? props.pagination.current_page - 1 : null;

    if (previousPage) {
        return {
            available: true,
            label: previousPage?.toString(),
            page: previousPage
        };
    } else {
        return null;
    }
});

const next = computed(() => {
    const nextPage = props.pagination.current_page != props.pagination.last_page ? props.pagination.current_page + 1 : null

    if (nextPage) {
        return {
            available: true,
            label: nextPage?.toString(),
            page: nextPage
        };
    } else {
        return null;
    }
});

let pages = computed(() => {
    let pagesNumbersArr = ref<(string|number)[]>([]);

    switch (totalPages.value > 7) {
        case true:
            if (currPageRef.value <= 4) {
                pagesNumbersArr.value = [1, 2, 3, 4, '...', totalPages.value-1, totalPages.value];
            } else if (5 <= currPageRef.value && currPageRef.value  <= 100000) {
                if (totalPages.value - currPageRef.value >= 3) {
                    pagesNumbersArr.value = [1, '...', currPageRef.value-1, currPageRef.value, currPageRef.value+1, '...', totalPages.value];
                } else {
                    pagesNumbersArr.value = [1, '...', totalPages.value-3, totalPages.value-2, totalPages.value-1, totalPages.value];
                }
            }
            break;

        case false:
            pagesNumbersArr.value = Array.from({ length: props.pagination.last_page }, (_, index) => index + 1);
            break;
    }

    return pagesNumbersArr.value.map((page) => ({
        active: page == props.pagination.current_page ? true : false,
        label: page.toString(),
        page: +page
    }));
});

onUpdated(() => {
    perPage.value = perPageRef.value;
    currPage.value = currPageRef.value;
})
</script>
