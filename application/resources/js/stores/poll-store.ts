import {defineStore} from 'pinia';
import {ref, computed, watch} from 'vue';
import {useStorage} from "@vueuse/core";
import {InertiaForm} from "@inertiajs/vue3";
import PollData = App.DataTransferObjects.PollData;
import Pagination from "@/types/pagination";
import PollsQuery from '@/types/polls-query';
import AdminPollService from '@/Pages/Auth/Poll/Services/admin-poll-service';

export const usePollStore = defineStore('poll-store', () => {

    let formData = ref<object | null>(null);
    let poll = ref<PollData | null>(null);
    let step = ref<number>(1);
    let pollsData = ref<PollData[]>([]);
    let pollsPagination = ref<Pagination>();
    let pollsQueryData = ref<PollsQuery|null>({p:1, l:10})

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

    async function loadPolls() {
        getPolls();
    }

    watch(pollsQueryData, () => {
        getPolls(pollsQueryData.value);
    })

    async function getPolls(query?: (PollsQuery|null)) {
        await AdminPollService.getPolls(query)
        .then((paginatedResponse) => {
            pollsData.value = paginatedResponse.data;
            pollsPagination.value = paginatedResponse.meta;
        });
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
        uploadPollData,
        loadPolls,
    }
});