<template>
    <div class="flex flex-row items-center justify-between">
        <div
            class="relative border-4 border-indigo-100 min-h-96 rounded-lg px-4 py-5 xl:px-6 xl:py-8 w-full lg:w-auto lg:min-w-[40rem] max-w-md">
            <Spinner v-if="registeredToVote === null" />

            <BallotConfirmation @change-choice="clearResponses" :ballotResponse="ballotResponse$" :ballot="ballot"
                v-if="seeConfirmation" />

            <div>
                <div class="relative">
                    <h3 class="pr-40 mb-4 pointer-events-none title3 xl:mb-6">
                        <span class="text-white">{{ question.title }}</span>
                    </h3>

                    <div class="absolute flex flex-col justify-start h-full right-1 top-1">
                        <div v-if="voterPower"
                            class="relative inline-flex items-center px-2 py-1 mr-auto text-xs font-medium text-white bg-indigo-900 border border-indigo-900 shadow-inner -right-1 gap-x-1 rounded-xl">
                            Your Voting Power
                            <span class="font-semibold text-white text-md xl:text-lg">
                                {{ voterPower }}
                            </span>
                        </div>
                    </div>
                </div>
                <ul class="flex flex-col gap-3">
                    <BallotQuestionChoice v-if="question.type != 'ranked'" @selected="onChoiceSelected($event)"
                        v-for="choice in question.choices" :key="choice.hash"
                        :choice="{ ...choice, selected: choice.hash === selectedChoice?.hash }">
                    </BallotQuestionChoice>
                    <RankedBallotQuestionChoiceCard v-else :choices="rankedChoicesData"
                        @ranked="saveRankedChoices($event)" />
                </ul>
                <div class="flex justify-center mt-8 lg:mt-10">
                    <button type="button" :disabled="selectedChoice === null && !rankedChoices"
                        :class="[selectedChoice === null && rankedChoices == null ? 'bg-slate-500 hover:cursor-not-allowed' : 'bg-indigo-100 hover:bg-indigo-300']"
                        @click="submitVote"
                        class="rounded-full px-8 lg:px-10 py-2.5 lg:py-3 text-md xl:text-xl font-semibold text-indigo-950 shadow-sm">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import QuestionData = App.DataTransferObjects.QuestionData;
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import UserData = App.DataTransferObjects.UserData;
import BallotResponseData = App.DataTransferObjects.BallotResponseData;
import BallotQuestionChoice from "@/Pages/Ballot/Partials/BallotQuestionChoice.vue";
import Spinner from "@/Components/Spinner.vue";
import { Ref, ref, watch, computed } from "vue";
import { useWalletStore } from "@/cardano/stores/wallet-store";
import { storeToRefs } from "pinia";
import { useVoterStore } from "@/Pages/Voter/stores/voter-store";
import { usePage } from "@inertiajs/vue3";
import BallotConfirmation from "@/Pages/Ballot/Partials/BallotConfirmation.vue";
import BallotService from "../Services/ballot-service";
import RankedBallotQuestionChoiceCard from "./RankedBallotQuestionChoiceCard.vue";
import QuestionChoiceListItem from "@/Pages/Auth/Question/QuestionChoice/Partials/QuestionChoiceListItem.vue";

const props = defineProps<{
    ballot: BallotData;
    question: QuestionData;
    ballotResponse: BallotResponseData | null;
}>();

const ballotChoice$ = ref<QuestionChoiceData>(props.ballotResponse?.choice);
const ballotResponse$ = ref<BallotResponseData>(props?.ballotResponse);

const user = usePage().props.auth.user;
const registeredToVote: Ref<boolean | null> = ref(null);

const walletStore = useWalletStore();
let { walletData: wallet } = storeToRefs(walletStore);

let seeConfirmation = computed(() =>  ((ballotResponse$.value || rankedResponse$.value) && registeredToVote.value))

const voterPower = computed(() => {
    if (props?.ballot?.hash)
        return voterStore.userVotingPower(props?.ballot?.hash);
});
const voterStore = useVoterStore();
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

const emit = defineEmits<{
    (e: 'submitted', choice?: QuestionChoiceData): void
    (e: 'submitRankedChoices', rankedChoices): void
}>();

let selectedChoice: Ref<QuestionChoiceData | null> = ref<QuestionChoiceData | null>(ballotChoice$.value);

function onChoiceSelected(choice: QuestionChoiceData) {
    selectedChoice.value = {
        ...choice,
        selected: true,
        question: props.question
    } as QuestionChoiceData;
    
}


// ranked choice stuff
let rankedChoices = ref(null);
let rankedResponse = computed(() => {
    if (props.question.type == 'ranked') {
        return props.question.ranked_user_responses
    }
})

let rankedResponse$ = ref(rankedResponse.value);

let rankedChoicesData = computed(() => {
    if (props.question.type != 'ranked') {
        return null;
    } else if (!rankedResponse$.value) {
        return props.question.choices;
    } else {
        let updatedChoices = [...props.question.choices];

        rankedResponse$.value?.forEach((response) => {
            const choiceIndex = updatedChoices.findIndex(choice => choice.hash == response.choice.hash);
            if (choiceIndex !== -1) {
                updatedChoices[choiceIndex].selected = true;

                const choice = updatedChoices[choiceIndex];
                updatedChoices.splice(choiceIndex, 1);
                updatedChoices.splice(response.rank, 0, choice);
            }
        });
        return updatedChoices;
    }
});


function saveRankedChoices(choices) {
    if (props.question.type == 'ranked') {
        rankedChoices.value = choices;
    }
}

async function onSubmitQuestion(choice: QuestionChoiceData, rankedChoices) {
        console.log({ choice }, { rankedChoices });

    if (!(wallet.value?.stakeAddress && props.ballot?.hash)) return;

    if (choice) {
        const response = {
            choice_hash: choice.hash,
            ballot_hash: props.ballot.hash
        };
        const res = (await BallotService.saveBallotResponse(wallet.value?.stakeAddress, response));
        ballotResponse$.value = {
            ...res,
            choice: {
                ...res.choice,
                selected: true
            }
        };
    } else {
        const response = {
            choices: rankedChoices,
            ballot_hash: props.ballot.hash
        };

        const res = (await BallotService.saveBallotResponse(wallet.value?.stakeAddress, null, response));
        rankedResponse$.value = res;
        
    }
}

function submitVote() {
    if (selectedChoice.value === null && !rankedChoices.value) {
        return;
    }
    emit('submitted', selectedChoice.value);
    emit('submitRankedChoices', rankedChoices.value);
    onSubmitQuestion(selectedChoice.value, rankedChoices.value);
}

watch(wallet, () => {
    if (wallet.value?.stakeAddress && props.ballot?.hash && user?.hash) {
        voterStore.loadVotingPower(wallet.value.stakeAddress, props.ballot?.hash);
    }
});

function clearResponses(){
    rankedResponse$.value = null 
    ballotResponse$.value = null
}
</script>
