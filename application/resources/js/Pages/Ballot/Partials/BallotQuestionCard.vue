<template>
    <div class="relative">
        <h3 class="pr-24 mb-4 pointer-events-none title3 xl:mb-6">
            <span class="text-white">{{ question.title }}</span>
        </h3>

        <div class="absolute flex flex-col justify-center h-full right-1 top-1">
            <div v-if="voterPower" class="inline-flex items-center gap-x-1.5 rounded-full bg-white drop-shadow-lg px-2.5 py-1 text-xs font-medium text-indigo-700 mr-auto">
                Your Voting Power
                <span class="text-lg font-semibold xl:text-xl">
                    {{ voterPower }}
                </span>
            </div>
        </div>
    </div>
    <ul class="flex flex-col gap-3">
        <BallotQuestionChoice
            @selected="onChoiceSelected($event)"
            v-for="choice in question.choices"
            :key="choice.hash"
            :choice="choice"></BallotQuestionChoice>
    </ul>
    <div class="flex justify-center mt-8 lg:mt-10">
        <button type="button"
                :disabled="selectedChoice === null"
                :class="[selectedChoice === null ? 'bg-slate-500 hover:cursor-not-allowed' : 'bg-white hover:bg-indigo-300']"
                @click="submitVote"
                class="rounded-full px-8 lg:px-10 py-2.5 lg:py-3 text-md xl:text-xl font-semibold text-indigo-950 shadow-sm">
            Submit
        </button>
    </div>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import QuestionData = App.DataTransferObjects.QuestionData;
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import BallotQuestionChoice from "@/Pages/Ballot/Partials/BallotQuestionChoice.vue";
import {Ref, ref, watch, computed} from "vue";
import humanNumber from "@/utils/human-number";
import { useWalletStore } from "@/cardano/stores/wallet-store";
import { storeToRefs } from "pinia";
import { useVoterStore } from "@/Pages/Voter/stores/voter-store";

const walletStore = useWalletStore();
let {walletData: wallet} = storeToRefs(walletStore);

const voterStore = useVoterStore();
let {voterPowers} = storeToRefs(voterStore);

const emit = defineEmits<{
    (e: 'submitted', choice?: QuestionChoiceData): void
}>();

const props = defineProps<{
    ballot: BallotData;
    question: QuestionData;
}>();

const voterPower = computed( () => {
    if ( ! props.ballot?.hash || !voterPowers.value[props.ballot.hash] ) {
        return '-';
    };

    return humanNumber(voterPowers.value[props.ballot.hash], 5);
});

let selectedChoice: Ref<QuestionChoiceData | null> = ref<QuestionChoiceData | null>(null);

function onChoiceSelected(choice: QuestionChoiceData) {
    selectedChoice.value = {
        ...choice,
        selected: true,
        question: props.question
    } as QuestionChoiceData;

    // create a ballot response
    // handle on the backend and
    // add voter infor and power
}

function submitVote()
{
    if(selectedChoice.value === null) {
        return;
    }
    emit('submitted', selectedChoice.value);
}

watch(wallet, () => {
    if ( wallet.value?.stakeAddress && props.ballot?.hash ) {
        voterStore.loadVotingPower(wallet.value.stakeAddress, props.ballot?.hash);
    }
});

</script>
