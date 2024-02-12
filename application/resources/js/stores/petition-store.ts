import {defineStore} from 'pinia';
import {ref, watch, Ref} from 'vue';
import PetitionsQuery from '@/types/petitions-query';
import AdminPetitionService from '@/Pages/Auth/Petition/Services/admin-petition-service';
import { VARIABLES } from '@/types/variables';

interface CurrentModel<PetitionPagination, K, PP, CP, P> {
    data?: PetitionPagination
    filters?: K
    currPage?: CP
    perPage?: PP
}

export const usePetitionStore = defineStore('petition-store', () => {

    let formData = ref<object | null>(null);
    let step = ref<number>(1);
    let currentModel = ref(({} as CurrentModel<any, any, any, any, any>) || null);
    let params: Ref<{ [x: string]: any; } | null> = ref(null);

    function setModel<T, K, CP, PP, P> (model: CurrentModel<T, K, CP, PP, P> ) {
        currentModel.value = model
    }

    function uploadFormData(form: any) {
        formData.value = form;
    }

    function setStep(newStep: number) {
        step.value = newStep
    }

    function nextStep() {
        step.value = step.value + 1;
    }

    async function getPetitions(query?: (PetitionsQuery|null)) {
        await AdminPetitionService.getPetitions(query)
        .then((res) => {
            currentModel.value.data = res;
        });
    }
    
    async function getFilteredData() {
        await setParams();

        if (Object.keys(params.value).length > 0) {
            await setUrlHistory(params?.value);
            await getPetitions(params?.value)
        }
    }

    async function setUrlHistory(params: { [x: string]: any; } | null) {
        const searchParams = new URLSearchParams();

        for (const key in params) {
            const value = params[key];
            if (value == null || value == '') {
                continue
            }
            searchParams.append(key, value);
        }

        let searchUrl = searchParams.toString();
        const baseUrl = `${window.location.pathname}`;
        const newUrl = baseUrl + (!!searchUrl.length ? ('?' + searchUrl) : searchUrl);
        window.history.pushState(null, '', newUrl);
    }

    async function setParams() {
        const data: any = {};
    
        if (currentModel.value.currPage) {
            data[VARIABLES.PAGE] = currentModel.value.currPage;
        }
    
        if (currentModel.value.perPage) {
            data[VARIABLES.PER_PAGE] = currentModel.value.perPage;
        }
        if (currentModel.value.filters.status) {
            data['ss'] = currentModel.value.filters.status;
        }


        params.value = data;
        return data;
    }

    watch(() =>currentModel.value?.currPage, () => {
        getFilteredData();
    }, { deep: true})
    
    watch(() => currentModel.value?.perPage, () => {
        currentModel.value.currPage = null
        getFilteredData();
    }, { deep: true})

    watch(() => currentModel.value?.filters, () => {
        currentModel.value.currPage = null
        getFilteredData();
    }, { deep: true})

    return {
        formData,
        step,
        currentModel$: currentModel,
        setStep,
        nextStep,
        uploadFormData,
        setModel,
    }
});
