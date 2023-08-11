<template>
    <div class="flex flex-row items-center justify-between">
        <div
            class="relative border-4 border-white rounded-lg px-4 py-5 xl:px-6 xl:py-8 w-full lg:w-auto lg:min-w-[40rem] max-w-md">
            <BallotConfirmation @change-choice="ballotResponse = null" :ballot="ballot" v-if="ballotResponse && registered" />

            <div>
                <div class="relative">
                    <h3 class="pr-32 mb-4 pointer-events-none title3 xl:mb-6">
                        <span class="text-white">{{ question.title }}</span>
                    </h3>

                    <div class="absolute flex flex-col justify-start h-full right-1 top-1">
                        <div v-if="voterPower"
                            class="inline-flex items-center gap-x-1.5 rounded-xl border border-indigo-900 bg-indigo-900 shadow-inner px-2.5 py-1 text-xs font-medium text-white mr-auto">
                            Your Voting Power
                            <span class="text-lg font-semibold xl:text-xl">
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
                        :class="[selectedChoice === null ? 'bg-slate-500 hover:cursor-not-allowed' : 'bg-white hover:bg-indigo-300']"
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
import { Ref, ref, watch, computed } from "vue";
import { useWalletStore } from "@/cardano/stores/wallet-store";
import { storeToRefs } from "pinia";
import { useVoterStore } from "@/Pages/Voter/stores/voter-store";
import { usePage } from "@inertiajs/vue3";
import BallotConfirmation from "@/Pages/Ballot/Partials/BallotConfirmation.vue";


const user = usePage().props.auth.user;

const walletStore = useWalletStore();
let { walletData: wallet } = storeToRefs(walletStore);

const voterStore = useVoterStore();


const emit = defineEmits<{
    (e: 'submitted', choice?: QuestionChoiceData): void
}>();

const props = defineProps<{
    ballot: BallotData;
    question: QuestionData;
    selectedChoice: QuestionChoiceData | null;
}>();

let ballotResponse = ref<null | BallotResponseData>(props.ballot?.user_response);

const voterPower = computed(() => voterStore.userVotingPower(props?.ballot?.hash));

let selectedChoice: Ref<QuestionChoiceData | null> = ref<QuestionChoiceData | null>(props.selectedChoice);

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

function submitVote() {
    if (selectedChoice.value === null) {
        return;
    }
    emit('submitted', selectedChoice.value);
}

watch(wallet, () => {
    if (wallet.value?.stakeAddress && props.ballot?.hash && user?.hash) {
        voterStore.loadVotingPower(wallet.value.stakeAddress, props.ballot?.hash);
    }
});

</script>
