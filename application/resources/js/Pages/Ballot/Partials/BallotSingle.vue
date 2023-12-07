<template>
    <div class="flex flex-col gap-10 px-8 py-10 text-white bg-indigo-600 rounded-lg">
        <div>
            <div class="flex flex-row items-center gap-5">
                <BallotStatusBadge :ballot="ballot"></BallotStatusBadge>

                <div class="flex flex-row items-center justify-between gap-2 text-sm font-semibold text-white"
                    v-if="ballot.started_at">
                    <div class="text-gray-300">Starts</div>
                    <div class="text-md">{{ ballot.started_at }}</div>
                </div>
                <div class="flex flex-row items-center justify-between gap-2 text-sm font-semibold text-white"
                    v-if="ballot.ended_at">
                    <div class="text-gray-300">Ends</div>
                    <div class="text-md">{{ ballot.ended_at }}</div>
                </div>

                <Link :href="route('ballot.view', { ballot: ballot.hash })" v-if="context === 'list'"
                    class="rounded-full bg-indigo-100 px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm flex flex-row gap-2 ring-1 ring-inset ring-gray-300 hover:bg-indigo-100 ml-auto">
                <span>View</span>
                </Link>
            </div>

            <h1 class="flex flex-row items-center gap-2 title2 font-display">
                <span class="text-white">{{ ballot.title }}</span>
                <Line></Line>
            </h1>

            <div class="mt-3">
                {{ ballot.description }}
            </div>
        </div>

        <div class="flex flex-row justify-between gap-4">
            <div class="relative">
                <div v-for="question in ballot.questions" :key="question?.hash">
                    <BallotQuestionCard :question="question" :ballot="ballot"
                    :ballotResponse="question.type=='ranked'? null : ballotResponse$  " />
                </div>

                <ConnectWalletToVote v-if="!wallet" />

                <LoginToVote v-if="!loggedIn" :page-data="ballot" />

                <RegisterToVote v-if="loggedIn && !registeredToVote" :ballot="ballot" />
            </div>

            <div class="flex flex-col items-center justify-end h-full gap-5">
                <div v-for="question in ballot.questions" :key="question?.hash"
                class="relative bg-indigo-700 shadow-sm rounded-lg px-4 py-5 xl:px-6 xl:py-8 w-full lg:w-auto lg:min-w-[36rem] max-w-md flex flex-row h-full">
                    <QuestionChoicesChart :question="question" />
                </div>

                <span class="ml-auto text-right text-white title2">Live Results</span>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import Line from "@/Pages/Partials/Line.vue";
import { Link, usePage } from "@inertiajs/vue3";
import BallotStatusBadge from "@/Pages/Auth/Ballot/Partials/BallotStatusBadge.vue";
import BallotQuestionCard from "@/Pages/Ballot/Partials/BallotQuestionCard.vue";
import { useWalletStore } from "@/cardano/stores/wallet-store";
import { storeToRefs } from "pinia";
import BallotResponseData = App.DataTransferObjects.BallotResponseData;
import QuestionChoicesChart from "@/Pages/Auth/Question/Partials/QuestionChoicesChart.vue";
import { ref, computed } from "vue";
import LoginToVote from "@/Pages/Ballot/Partials/LoginToVote.vue";
import RegisterToVote from "./RegisterToVote.vue";
import ConnectWalletToVote from "@/Pages/Ballot/Partials/ConnectWalletToVote.vue";
import { useVoterStore } from "@/Pages/Voter/stores/voter-store";

const registeredToVote = ref(false);
const user = usePage().props.auth.user;
const props = withDefaults(defineProps<{
    ballot: BallotData;
    context?: string;
}>(), {
    context: 'full'
});

const voterStore = useVoterStore();
voterStore.loadRegistration(props.ballot.hash).then(() => {
    registeredToVote.value = !!voterStore.registeredForBallot(props.ballot.hash);
});
let ballotResponse$ = ref<null | BallotResponseData>(props.ballot?.user_response);

const walletStore = useWalletStore();
const { walletData: wallet } = storeToRefs(walletStore);

const loggedIn = computed( () => {
    if (!user?.hash) {
        return false;
    }

    if (!wallet) {
        return false;
    }
    return true;
});

</script>
