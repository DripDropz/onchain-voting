import { defineStore } from 'pinia';
import { ref, computed, watch, onMounted, Ref } from 'vue';
import { InertiaForm, usePage } from "@inertiajs/vue3";
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
    let currentUserId = ref<number | null>(null);
    let activePublicRequestKey: Ref<string | null> = ref(null);
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
        'draft':{
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

    // Watch for user changes and reset store when user changes
    const page = usePage();
    watch(() => page.props.auth?.user?.id, (newUserId, oldUserId) => {
        if (newUserId !== oldUserId) {
            resetAllContexts();
            currentUserId.value = newUserId ?? null;
        }
    }, { immediate: true });

    function resetAllContexts() {
        const contexts = ['browse', 'active', 'pending', 'draft', 'answered'];
        contexts.forEach(context => {
            if (publicPoll.value[0]?.[context]) {
                publicPoll.value[0][context].polls = [];
                publicPoll.value[0][context].nextCursor = null;
                publicPoll.value[0][context].hasMorePages = null;
            }
        });
        activePublicRequestKey.value = null;
    }

    function resetContext(context: string) {
        if (publicPoll.value[0]?.[context]) {
            publicPoll.value[0][context].polls = [];
            publicPoll.value[0][context].nextCursor = null;
            publicPoll.value[0][context].hasMorePages = null;
        }
    }

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

            // Create a unique request key to prevent duplicate simultaneous requests
            const requestKey = JSON.stringify({
                context,
                nextCursor: data.nextCursor ?? null,
                hasMorePages: data.hasMorePages ?? null,
                params: params ?? null,
                userId: currentUserId.value,
            });

            // Skip if this exact request is already in flight
            if (loadingMore.value && activePublicRequestKey.value === requestKey) {
                return;
            }
            activePublicRequestKey.value = requestKey;

            await PublicPollService.fetchPolls(data)
                .then((res) => {
                    publicPoll.value[0][context].hasMorePages = res.hasMorePages;
                    publicPoll.value[0][context].nextCursor = res.nextCursor;
                    
                    // Deduplicate polls by hash before adding
                    const existingHashes = new Set(publicPoll.value[0][context].polls.map(p => p.hash));
                    const newPolls = res.polls.filter(p => !existingHashes.has(p.hash));
                    
                    publicPoll.value[0][context].polls = [...publicPoll.value[0][context].polls, ...newPolls];
                }).finally(() => {
                    loadingMore.value = false;
                    if (activePublicRequestKey.value === requestKey) {
                        activePublicRequestKey.value = null;
                    }
                })
        } catch (error) {
            loadingMore.value = false;
            activePublicRequestKey.value = null;
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

    function removePoll(hash: string) {
        const context = currentContext.value;
        if (publicPoll.value[0][context]) {
            publicPoll.value[0][context].polls = publicPoll.value[0][context].polls.filter(
                (poll) => poll.hash !== hash
            );
        }
    }

    async function reloadContext(context: string, params: any) {
        resetContext(context);
        loadingMore.value = true;
        await loadPublicPolls(context, params);
    }

    function clearAdminPolls() {
        pollsData.value = [];
        pollsPagination.value = undefined;
        pollsQueryData.value = { p: 1, l: 10 };
    }

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
        singlePublicPoll,
        removePoll,
        reloadContext,
        resetContext,
        resetAllContexts,
        clearAdminPolls,
    }
});
