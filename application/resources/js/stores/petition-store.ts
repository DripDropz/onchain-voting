import {defineStore} from 'pinia';
import {ref, computed, watch} from 'vue';
import {useStorage} from "@vueuse/core";
import {InertiaForm} from "@inertiajs/vue3";
import PetitionData = App.DataTransferObjects.PetitionData;
import Pagination from "@/types/pagination";
import PetitionsQuery from '@/types/petitions-query';
import AdminPetitionService from '@/Pages/Auth/Petition/Services/admin-petition-service';

export const usePetitionStore = defineStore('petition-store', () => {

    let formData = ref<object | null>(null);
    let petition = ref<PetitionData | null>(null);
    let step = ref<number>(1);
    let petitionsData = ref<PetitionData[]>([]);
    let petitionsPagination = ref<Pagination>();
    let petitionsQueryData = ref<PetitionsQuery|null>({p:1, l:10})

    function uploadFormData(form: any) {
        formData.value = form;
    }

    function uploadPetitionData(form: InertiaForm<any>) {
        petition.value = form;
    }

    function setStep(newStep: number) {
        step.value = newStep
    }

    function nextStep() {
        step.value = step.value + 1;
    }

    async function loadPetitions() {
        getPetitions();
    }

    watch(petitionsQueryData, () => {
        getPetitions(petitionsQueryData.value);
    })

    async function getPetitions(query?: (PetitionsQuery|null)) {
        await AdminPetitionService.getPetitions(query)
        .then((paginatedResponse) => {
            petitionsData.value = paginatedResponse.data;
            petitionsPagination.value = paginatedResponse.meta;
        });
    }

    return {
        formData,
        petition,
        step,
        petitionsData,
        petitionsPagination,
        petitionsQueryData,
        setStep,
        nextStep,
        uploadFormData,
        uploadPetitionData,
        loadPetitions,
    }
});
