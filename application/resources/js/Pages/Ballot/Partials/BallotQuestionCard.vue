<template>
    <div class="flex flex-row items-center justify-between">
        <div
            class="relative border-4 border-indigo-100 min-h-96 rounded-lg px-4 py-5 xl:px-6 xl:py-8 w-full lg:w-auto lg:min-w-[40rem] max-w-md">
            <Spinner v-if="registeredToVote === null" />

            <BallotConfirmation @change-choice="ballotResponse$ = null" :ballotResponse="ballotResponse$" :ballot="ballot"
            v-if="!!ballotResponse$?.submit_tx || (ballotResponse$ && registeredToVote)" />

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
                    <BallotQuestionChoice @selected="onChoiceSelected($event)" v-for="choice in question.choices"
                        :key="choice.hash" :choice="{ ...choice, selected: choice.hash === selectedChoice?.hash }">
                    </BallotQuestionChoice>
                </ul>
                <div class="flex justify-center mt-8 lg:mt-10">
                    <button type="button" :disabled="selectedChoice === null"
                        :class="[selectedChoice === null ? 'bg-slate-500 hover:cursor-not-allowed' : 'bg-indigo-100 hover:bg-indigo-300']"
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

const props = defineProps<{
    ballot: BallotData;
    question: QuestionData;
    ballotResponse: BallotResponseData | null;
}>();

const ballotChoice$ = ref<QuestionChoiceData>(props.ballotResponse?.choice);
const ballotResponse$ = ref<BallotResponseData>(props?.ballotResponse);

const user = usePage().props.auth.user;
const registeredToVote: Ref<boolean|null> = ref(null);

const walletStore = useWalletStore();
let { walletData: wallet } = storeToRefs(walletStore);

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
}>();

let selectedChoice: Ref<QuestionChoiceData | null> = ref<QuestionChoiceData | null>(ballotChoice$.value);

function onChoiceSelected(choice: QuestionChoiceData) {
    selectedChoice.value = {
        ...choice,
        selected: true,
        question: props.question
    } as QuestionChoiceData;
}

async function onSubmitQuestion(choice: QuestionChoiceData) {
    if (!(wallet.value?.stakeAddress && choice.hash && props.ballot.hash)) {
        return;
    }

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
}

function submitVote() {
    if (selectedChoice.value === null) {
        return;
    }
    emit('submitted', selectedChoice.value);
    onSubmitQuestion(selectedChoice.value);
}

watch(wallet, () => {
    if (wallet.value?.stakeAddress && props.ballot?.hash && user?.hash) {
        voterStore.loadVotingPower(wallet.value.stakeAddress, props.ballot?.hash);
    }
});
</script>
