import { defineStore } from 'pinia';
import { ref, watch, Ref, computed } from 'vue';
import PetitionsQuery from '@/types/petitions-query';
import AdminPetitionService from '@/Pages/Auth/Petition/Services/admin-petition-service';
import { VARIABLES } from '@/types/variables';
import PetitionData = App.DataTransferObjects.PetitionData;
import PublicPetitionService from '@/Pages/Petition/services/public-petition-service';
import AlertService from '@/shared/Services/alert-service';

interface CurrentModel<PetitionPagination, K, PP, CP, P> {
    data?: PetitionPagination
    filters?: K
    currPage?: CP
    perPage?: PP
}

export const usePetitionStore = defineStore('petition-store', () => {

    let formData = ref<object | null>(null);
    let petition = ref<PetitionData | null>(null);
    let singlePublicPetition = ref<PetitionData | null>(null);
    let step = ref<number>(1);
    let currentModel = ref(({} as CurrentModel<any, any, any, any, any>) || null);
    let params: Ref<{ [x: string]: any; } | null> = ref(null);
    let currentContext = ref('browse');
    let loadingMore = ref(false);
    let publicPetition: Ref<{
        [context: string]: {
            petitions: PetitionData[];
            nextCursor: string;
            hasMorePages: boolean
        }
    }[]> = ref([{
        'browse': {
            petitions: [],
            nextCursor: null,
            hasMorePages: null
        },
        'draft': {
            petitions: [],
            nextCursor: null,
            hasMorePages: null,
        },
        'active': {
            petitions: [],
            nextCursor: null,
            hasMorePages: null,
        },
        'pending': {
            petitions: [],
            nextCursor: null,
            hasMorePages: null,
        },
        'signed': {
            petitions: [],
            nextCursor: null,
            hasMorePages: null,
        },
    }]);

    function setModel<T, K, CP, PP, P>(model: CurrentModel<T, K, CP, PP, P>) {
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

    async function getPetitions(query?: (PetitionsQuery | null)) {
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

    async function loadPublicPetitions(context = 'browse', params = null) {
        try {
            const data = {
                hasMorePages: publicPetition.value[0][context]?.hasMorePages ?? null,
                nextCursor: publicPetition.value[0][context]?.nextCursor ?? null,
                ...params
            }
            if (context === 'browse') {
                data['statusfilter'] = ['published'];
            }

            await PublicPetitionService.fetchPetitions(data)
                .then((res) => {
                    publicPetition.value[0][context].hasMorePages = res.hasMorePages;
                    publicPetition.value[0][context].nextCursor = res.nextCursor;
                    publicPetition.value[0][context].petitions = [...publicPetition.value[0][context].petitions, ...res.petitions];
                }).finally(() => {
                    loadingMore.value = false
                })
        } catch (error) {
            loadingMore.value = false
            AlertService.show(['error'], 'error ')
        }
    }

    async function loadPublicPetition(hash: string) {
        try {
            await PublicPetitionService.fetchPetition(hash)
                .then((res) => {
                    singlePublicPetition.value = res;
                })
        } catch (error) {
            AlertService.show(['error'], 'error ')
        }
    }

    const showMore = computed(() => {
        return publicPetition?.value[0][currentContext.value]?.nextCursor && publicPetition?.value[0][currentContext.value]?.hasMorePages;
    })

    function setContext(context: any) {
        currentContext.value = context;
    }

    watch(() => currentModel.value?.currPage, () => {
        getFilteredData().then();
    }, { deep: true })

    watch(() => currentModel.value?.perPage, () => {
        currentModel.value.currPage = null
        getFilteredData().then();
    }, { deep: true })

    watch(() => currentModel.value?.filters, () => {
        currentModel.value.currPage = null
        getFilteredData().then();
    }, { deep: true })

    return {
        formData,
        petition,
        step,
        currentModel$: currentModel,
        setStep,
        nextStep,
        uploadFormData,
        setModel,
        loadPublicPetitions,
        publicPetition,
        loadingMore,
        currentContext,
        showMore,
        setContext,
        singlePublicPetition,
    }
});
