<template>
    <AuthenticatedLayout title="Dashboard" :crumbs="crumbs">
    <section>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="sm:rounded-lg">
                <h2 class="font-semibold text-lg xl:text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4 mt-9">
                    Petitions
                </h2>
                <PetitionListAdmin :petitions="petitionsData" class="mt-4"/>
                <div v-if="petitionsPagination" class="flex flex-row items-center justify-between w-full py-4">
                    <div class="border-2 border-sky-600">
                        <p class="p-4 text-sm text-sky-600 dark:text-gray-300">
                            {{ `Showing ${petitionsPagination?.from} to ${(petitionsPagination?.to <
                                petitionsPagination?.total) ? petitionsPagination?.to : petitionsPagination?.total} of
                                                                ${petitionsPagination?.total} results` }} </p>
                    </div>
                    <Paginator :pagination="petitionsPagination" @paginated="(payload: number) => currPage = payload"
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
    import PetitionListAdmin from "@/Pages/Auth/Petition/Partials/PetitionListAdmin.vue"
    import Paginator from '@/shared/components/Paginator.vue';
    import { usePetitionStore } from '@/stores/petition-store';
    import PetitionsQuery from '@/types/petitions-query';
    import { VARIABLES } from '@/types/variables'
    import { ref, watch } from 'vue';
    import { storeToRefs } from 'pinia';
    
    
    const props = defineProps<{
        petitions: any;
        crumbs: [];
    }>();
    
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