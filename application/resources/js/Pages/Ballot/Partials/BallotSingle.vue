<template>
    <div class="flex flex-col gap-10 px-8 py-10 text-white bg-indigo-600 rounded-lg">
        <div>
            <div class="flex flex-row items-center gap-5">
                <BallotStatusBadge :ballot="ballot"></BallotStatusBadge>

                <div class="flex flex-row items-center justify-between gap-2 text-sm font-semibold text-white" v-if="ballot.started_at">
                    <div class="text-gray-300">Starts</div>
                    <div class="text-md">{{ ballot.started_at }}</div>
                </div>
                <div class="flex flex-row items-center justify-between gap-2 text-sm font-semibold text-white" v-if="ballot.ended_at">
                    <div class="text-gray-300">Ends</div>
                    <div class="text-md">{{ ballot.ended_at }}</div>
                </div>

                <Link :href="route('ballots.view', {ballot: ballot.hash})" v-if="context === 'list'"
                      class="rounded-full bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm flex flex-row gap-2 ring-1 ring-inset ring-gray-300 hover:bg-indigo-200 ml-auto">
                    <span>View</span>
                </Link>
            </div>

            <h1 class="flex flex-row items-center gap-2 title2 font-display">
                <span class="text-white">{{ ballot.title }}</span>
                <Line></Line>
            </h1>
            <div class="mt-3">
                {{ballot.description}}
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="relative border-4 border-white rounded-lg px-4 py-5 xl:px-6 xl:py-8 w-full lg:w-auto lg:min-w-[40rem]">
                <ConnectWalletToVote v-if="!wallet"></ConnectWalletToVote>
                <LoginToVote v-if="wallet && !!wallet && !user?.hash"></LoginToVote>
                <div v-for="question in ballot.questions" :key="question.hash">
                    <BallotQuestionCard @submitted="onSubmitQuestion($event)" :question="question" :ballot="ballot"></BallotQuestionCard>
                </div>
            </div>
            <div class="items-center title2">
                <span class="text-white">Live Results</span>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import Line from "@/Pages/Partials/Line.vue";
import {Link, usePage} from "@inertiajs/vue3";
import BallotStatusBadge from "@/Pages/Auth/Ballot/Partials/BallotStatusBadge.vue";
import BallotQuestionCard from "@/Pages/Ballot/Partials/BallotQuestionCard.vue";
import ConnectWalletToVote from "@/Pages/Ballot/Partials/ConnectWalletToVote.vue";
import LoginToVote from "@/Pages/Ballot/Partials/LoginToVote.vue";
import {useWalletStore} from "@/cardano/stores/wallet-store";
import {storeToRefs} from "pinia";
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import VoterService from "@/Pages/Voter/Services/voter-service";

const user = usePage().props.auth.user;
const props = withDefaults(defineProps<{
    ballot: BallotData;
    context?: string;
}>(), {
    context: 'full'
});
console.log('ballot::', props.ballot);
const walletStore = useWalletStore();
const {walletData: wallet} = storeToRefs(walletStore);

let onSubmitQuestion = async (choice: QuestionChoiceData) => {
    if ( !(wallet.value?.stakeAddress && choice.hash && props.ballot.hash) ) {
        return;
    }

    const response = {
        choice_hash: choice.hash,
        ballot_hash: props.ballot.hash
    };

    const ballotResponse = await VoterService.saveBallotResponse(wallet.value?.stakeAddress, response);
    console.log('ballotResponse::', ballotResponse);
}

</script>
