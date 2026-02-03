import { defineStore } from 'pinia';
import { ref, computed, watch, onMounted, Ref } from 'vue';
import { InertiaForm } from "@inertiajs/vue3";
import PollData = App.DataTransferObjects.PollData;
import Pagination from "@/types/pagination";
import PollsQuery from '@/types/polls-query';
import AdminPollService from '@/Pages/Auth/Poll/Services/admin-poll-service';
import PublicPollService from '@/Pages/Poll/services/public-poll-service';
import AlertService from '@/shared/Services/alert-service';

export const usePollStore = defineStore('poll-store', () => {

    let formData = ref<object | null>(null);
    let poll = ref<PollData | null>(null);
    let singlePublicPoll = ref<PollData | null>(null);
    let step = ref<number>(1);
    let pollsData = ref<PollData[]>([]);
    let pollsPagination = ref<Pagination>();
    let pollsQueryData = ref<PollsQuery | null>({ p: 1, l: 10 });
    let loadingMore = ref(false)
    let currentContext = ref('browse');
    let publicPoll: Ref<{
        [context: string]: {
            polls: PollData[];
            nextCursor: string;
            hasMorePages: boolean;
        }
    }[]> = ref([{
        'browse':{
            polls: [],
            nextCursor: null,
            hasMorePages: null,

        },
        'active': {
            polls: [],
            nextCursor: null,
            hasMorePages: null,
        },
        'pending':{
            polls: [],
            nextCursor: null,
            hasMorePages: null,
        },
        'answered':{
            polls: [],
            nextCursor: null,
            hasMorePages: null,
        },
    }]);

    function uploadFormData(form: any) {
        formData.value = form;
    }

    function uploadPollData(form: InertiaForm<any>) {
        poll.value = form;
    }

    function setStep(newStep: number) {
        step.value = newStep
    }

    function nextStep() {
        step.value = step.value + 1;
    }

    function setContext(context){
        currentContext.value = context;
    }

    watch(pollsQueryData, () => {
        getAdminPolls(pollsQueryData.value).then();
    });

    async function getAdminPolls(query?: (PollsQuery | null)) {
        await AdminPollService.getPolls(query)
            .then((paginatedResponse) => {
                pollsData.value = paginatedResponse.data;
                pollsPagination.value = paginatedResponse.meta;
            });
    }

    async function loadPublicPolls(context = 'browse', params = null) {
        try {
            const data = {
                hasMorePages: publicPoll.value[0][context]?.hasMorePages ?? null,
                nextCursor: publicPoll.value[0][context]?.nextCursor ?? null,
                ...params
            }

            await PublicPollService.fetchPolls(data)
                .then((res) => {
                    publicPoll.value[0][context].hasMorePages = res.hasMorePages;
                    publicPoll.value[0][context].nextCursor = res.nextCursor;
                    publicPoll.value[0][context].polls = [...publicPoll.value[0][context].polls ,...res.polls];
                }).finally(() => {
                    loadingMore.value = false
                })
        } catch (error) {
            loadingMore.value = false
            AlertService.show(['error'], 'error ')
        }
    }

    async function loadPublicPoll(hash: string) {
        try {
            await PublicPollService.fetchPoll(hash)
            .then((res) => {
                singlePublicPoll.value = res;
            })
        } catch (error) {
            AlertService.show(['error'], 'error ')
        }
    }

    const showMore = computed(() => {
        return publicPoll?.value[0][currentContext.value]?.nextCursor && publicPoll?.value[0][currentContext.value]?.hasMorePages;
    });

    return {
        formData,
        poll,
        step,
        pollsData,
        pollsPagination,
        pollsQueryData,
        setStep,
        nextStep,
        uploadFormData,
        loadingMore,
        loadPublicPolls,
        loadPublicPoll,
        showMore,
        getAdminPolls,
        publicPoll,
        currentContext,
        setContext,
        singlePublicPoll
    }
});
