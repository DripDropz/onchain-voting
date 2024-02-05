import { defineStore } from 'pinia';
import { ref, watch } from 'vue';
import { InertiaForm } from "@inertiajs/vue3";
import BallotData = App.DataTransferObjects.BallotData;
import Pagination from "@/types/pagination";
import AdminBallotService from '@/Pages/Auth/Ballot/Services/admin-ballot-service';
import DataQuery from '@/types/data-query';

export const useBallotStore = defineStore('ballot-store', () => {

    let formData = ref<object | null>(null);
    let ballot = ref<BallotData | null>(null);
    let step = ref<number>(1);
    let ballots = ref<BallotData[]>([]);
    let ballotsData = ref<BallotData[]>([]);
    let ballotsPagination = ref<Pagination>();
    let ballotsQueryData = ref<DataQuery | null>({ p: 1, l: 10 })

    function uploadFormData(form: any) {
        formData.value = form;
    }

    function uploadBallotData(form: InertiaForm<any>) {
        ballot.value = form;
    }

    function setStep(newStep: number) {
        step.value = newStep
    }

    function nextStep() {
        step.value = step.value + 1;
    }

    async function loadBallots() {
        getBallots();
    }

    async function loadAllBallots() {
        if (!ballots.value.length) {
            ballots.value = await AdminBallotService.getAllBallots()
        }
    }

    watch(ballotsQueryData, () => {
        getBallots(ballotsQueryData.value);
    })

    async function getBallots(query?: (DataQuery | null)) {
        await AdminBallotService.getBallots(query)
            .then((paginatedResponse) => {
                ballotsData.value = paginatedResponse.data;
                ballotsPagination.value = paginatedResponse.meta;
            });
    }

    return {
        formData,
        ballot,
        step,
        ballotsData,
        ballotsPagination,
        ballotsQueryData,
        setStep,
        nextStep,
        uploadFormData,
        uploadBallotData,
        loadBallots,
        ballots,
        loadAllBallots
    }
});




