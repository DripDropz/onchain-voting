<template>
    <div class="flex flex-col gap-10 px-8 py-10 text-white rounded-lg bg-sky-600">
        <div>
            <div class="relative flex flex-row items-center gap-5">
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

                <div class="absolute top-0 right-0 flex flex-col justify-start">
                    <div v-if="voterPower"
                         class="relative inline-flex items-center px-2 py-0.5 mr-auto text-xs font-medium text-white bg-sky-900 border border-sky-900 shadow-inner  gap-x-1 rounded-xl">
                        Your Voting Power
                        <span class="font-semibold text-white text-md xl:text-lg">
                            {{ voterPower }}
                        </span>
                    </div>
                </div>

                <Link :href="route('ballot.view', { ballot: ballot.hash })" v-if="context === 'list'"
                      class="rounded-full bg-sky-100 px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm flex flex-row gap-2 ring-1 ring-inset ring-gray-300 hover:bg-sky-100 ml-auto">
                    <span>View</span>
                </Link>
            </div>

            <h1 class="flex flex-row items-center gap-2 title2 font-display">
                <span class="text-white">{{ ballot.title }}</span>
                <Line :classes="['bg-white']"></Line>
            </h1>

            <div class="mt-3">
                {{ ballot.description }}
            </div>
        </div>

        <div class="flex flex-col gap-16">
            <div class="relative flex flex-row items-start justify-between gap-4"
                 :class="{
            'bg-sky-700/50 rounded-t-2xl shadow-inner p-8 -m-8': index % 2 !== 0
           }"
                 v-for="(question, index) in ballot$.questions" :key="question?.hash">
                <div class="relative">
                    <BallotQuestionCard :question="question" :ballot="ballot$"
                                        @save-response="saveBallotResponse($event)"
                                        @record-onchain="submitToChain()"
                                        :registeredToVote="registeredToVote"
                                        :working="working"
                                        :userResponse="userResponses$[question.hash]"/>

                    <ConnectWalletToVote v-if="!wallet && isBallotOpen"/>

                    <LoginToVote v-if="!loggedIn && isBallotOpen" :page-data="ballot$"/>

                    <RegisterToVote v-if="loggedIn && !registeredToVote && !voteRecordedOnChain && isBallotOpen"
                                    :ballot="ballot$"
                                    :hasVotingPower="(voterPower !== '-')"/>

                    <ConfirmingOnChain v-if="!confirmedOnChain && registeredToVote"/>
                </div>

                <div class="flex flex-col h-full gap-10 px-4 pt-1">
                    <div class="flex items-center gap-1 p-2 ml-auto border-4 border-white rounded-lg">
                        <Tooltip iconSize="6">
                            <template #trigger>
                                <span>How to Vote</span>
                            </template>

                            <template #content>
                                <div class="p-3 bg-sky-700">
                                    <h3 class="text-2xl font-bold">To Vote </h3>
                                    <ol class="flex flex-col gap-2 ml-5 text-lg text-white list-decimal list-outside xl:text-xl">
                                        <li class="text-gray-300">Select Choice</li>
                                        <li class="text-gray-300">Hit Save. You will be given the choice to change your
                                            vote or
                                            submit on change
                                        </li>
                                        <li class="text-gray-300">Submit on chain and Sign Tx.</li>
                                    </ol>
                                </div>
                            </template>
                        </Tooltip>
                    </div>

                    <div class="flex flex-col items-center justify-end gap-4">
                        <div
                            class="relative bg-sky-700 shadow-sm rounded-lg py-5 xl:px-6 xl:py-8 w-full lg:w-auto lg:min-w-[36rem] max-w-md flex flex-row h-auto">
                            <QuestionChoicesChart :question="question"/>
                        </div>

                        <span class="ml-auto text-right text-white title2">
                            {{ isBallotOpen ? 'Live Results' : 'Final Results' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import Line from "@/Pages/Partials/Line.vue";
import {Link, router, usePage} from "@inertiajs/vue3";
import BallotStatusBadge from "@/Pages/Auth/Ballot/Partials/BallotStatusBadge.vue";
import BallotQuestionCard from "@/Pages/Ballot/Partials/BallotQuestionCard.vue";
import {useWalletStore} from "@/cardano/stores/wallet-store";
import {storeToRefs} from "pinia";
import BallotResponseData = App.DataTransferObjects.BallotResponseData;
import QuestionChoicesChart from "@/Pages/Auth/Question/Partials/QuestionChoicesChart.vue";
import {ref, computed, watch} from "vue";
import LoginToVote from "@/Pages/Ballot/Partials/LoginToVote.vue";
import RegisterToVote from "./RegisterToVote.vue";
import Tooltip from "./Tooltip.vue";
import ConnectWalletToVote from "@/Pages/Ballot/Partials/ConnectWalletToVote.vue";
import {useVoterStore} from "@/Pages/Voter/stores/voter-store";
import BallotService from "@/Pages/Ballot/Services/ballot-service";
import ConfirmingOnChain from "@/Pages/Ballot/Partials/ConfirmingOnChain.vue";

const registeredToVote = ref(false);
const user = usePage().props.auth.user;
const props = withDefaults(defineProps<{
    ballot: BallotData;
    context?: string;
}>(), {
    context: 'full'
});

const voterStore = useVoterStore();
const {voterRegistrations} = storeToRefs(voterStore);
const {confirmedOnChain} = storeToRefs(voterStore);

voterStore.loadRegistration(props.ballot.hash).then(() => {
    registeredToVote.value = !!voterStore.registeredForBallot(props.ballot.hash);
});

const ballot$ = ref<BallotData>(props.ballot);
const userResponses$ = ref<Record<string, BallotResponseData>>(
    props?.ballot?.user_responses?.reduce((acc, response) => {
        acc[response.question?.hash] = response;
        return acc;
    }, {})
);
const isBallotOpen = ref(props?.ballot.open);
const savingResponse = ref(false);
const voteRecordedOnChain = Object.values(userResponses$.value).some((response) => !!response?.submit_tx);

const walletStore = useWalletStore();
const {walletData: wallet} = storeToRefs(walletStore);

voterStore.loadVotingPower(user?.voter_id, props.ballot.hash);
const voterPower = computed(() => {
    if (props?.ballot?.hash)
        return voterStore.userVotingPower(props?.ballot?.hash);
});

const loggedIn = computed(() => {
    if (!user?.hash) {
        return false;
    }
    return wallet;
});

const working = computed(() => {
    return savingResponse.value;
});

if (props.ballot.hash) {
    const ballotHash = props.ballot.hash;
    voterStore.loadRegistration(ballotHash).then(() => {
        registeredToVote.value = !!voterStore.registeredForBallot(ballotHash);
    }).catch((e) => {
        console.log(e);
        registeredToVote.value = false;
    });
} else {
    registeredToVote.value = false;
}

watch([() => voterRegistrations.value], () => {
    registeredToVote.value = !!voterStore.registeredForBallot(props.ballot.hash);
    voterStore.onChainConfirmation(props.ballot.hash);
}, {deep: true})

async function saveBallotResponse(response: BallotResponseData) {
    savingResponse.value = true;
    const res = (await BallotService.saveBallotResponse(
        wallet.value?.stakeAddress,
        response.choices.map((choice) => choice.hash),
        props.ballot.hash
    ));
    if (res.status == 201) {
        ballot$.value = res.data;
        userResponses$.value = ballot$.value?.user_responses?.reduce((acc, response) => {
            acc[response.question?.hash] = response;
            return acc;
        }, {});
        savingResponse.value = false;
    }
}

// ranked choice stuff
// let rankedChoices = ref(null);
// let rankedResponse = computed(() => {
//     if (props.question.type == 'ranked') {
//         return props.question.ranked_user_responses
//     }
// })
//
// let rankedResponse$ = ref(rankedResponse.value);
//
// let rankedChoicesData = computed(() => {
//     if (props.question.type != 'ranked') {
//         return null;
//     } else if (!rankedResponse$.value) {
//         return props.question.choices;
//     } else {
//         let updatedChoices = [...props.question.choices];
//
//         rankedResponse$.value?.forEach((response) => {
//             const choiceIndex = updatedChoices.findIndex(choice => choice.hash == response.choice.hash);
//             if (choiceIndex !== -1) {
//                 updatedChoices[choiceIndex].selected = true;
//
//                 const choice = updatedChoices[choiceIndex];
//                 updatedChoices.splice(choiceIndex, 1);
//                 updatedChoices.splice(response.rank, 0, choice);
//             }
//         });
//         return updatedChoices;
//     }
// });

async function submitToChain() {
    savingResponse.value = true;
    await BallotService.submitVote(props.ballot.hash);
    savingResponse.value = false;
    router.get(route('ballot.view',{ballot:props.ballot.hash}));
}


// function saveRankedChoices(choices) {
//     if (props.question.type == 'ranked') {
//         rankedChoices.value = choices;
//     }
// }


function submitVote() {
    // if (selectedChoice.value === null && !rankedChoices.value) {
    //     return;
    // }
    // emit('submitted', selectedChoice.value);
    // emit('submitRankedChoices', rankedChoices.value);
    // onSubmitQuestion(selectedChoice.value, rankedChoices.value);
}
</script>
