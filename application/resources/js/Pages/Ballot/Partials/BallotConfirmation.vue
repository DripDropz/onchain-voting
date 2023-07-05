<template>
    <div
        class="absolute top-0 left-0 z-40 flex items-center justify-center w-full h-full p-8 text-white rounded-lg bg-indigo-800/90">
        <div class="w-4/5 h-full p-1 bg-indigo-700 rounded-lg">
            <div class="flex flex-col justify-start h-full gap-4 text-center">
                <h3
                    class="w-full p-3 mx-auto text-2xl font-bold text-center border-b-4 border-white border-double md:text-3xl xl:text-4xl">
                    Confirm your vote
                </h3>

                <div class="">
                    <p class="p-3 text-sm font-semibold text-white">
                        You are about to submit your vote on chain.
                    </p>

                    <div class="mt-6">
                        <p class="text-xs">Your Selection</p>
                        <p class="p-3 font-semibold text-white text-md xl:text-2xl">
                            {{ ballotResponse?.choice?.title }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-center gap-3 p-3 mt-auto">
                    <div class="flex flex-col justify-center">
                        <button @click="onChangeChoice"
                            class="px-2 py-0.5 text-xs xl:text-sm border border-white border-dashed rounded-lg hover:bg-indigo-900/90">Change</button>
                    </div>

                    <button @click="submitToChain" type="button"
                        class="rounded-full px-6 lg:px-8 py-2 lg:py-2.5 text-md xl:text-xl font-semibold text-indigo-950 shadow-sm bg-white hover:bg-indigo-300">
                        Sign & Submit Onchain
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue';
import BallotData = App.DataTransferObjects.BallotData;
import BallotResponseData = App.DataTransferObjects.BallotResponseData;
import VoterService from "@/Pages/Voter/Services/voter-service";
import { useWalletStore } from '@/cardano/stores/wallet-store';
import { storeToRefs } from 'pinia';


const props = defineProps<{
    ballot: BallotData;
}>();

const walletStore = useWalletStore();
const { walletData: wallet } = storeToRefs(walletStore);

let ballotResponse = ref<null | BallotResponseData>(props.ballot?.user_response);
let choiceHash = ballotResponse.value?.choice.hash;
let choices = props.ballot.questions?.[0]?.choices;
let choiceIndex = (choices.findIndex(choice => choice.hash == choiceHash)) + 1

const emit = defineEmits<{
    (e: 'change-choice'): void
}>();

const onChangeChoice = () => {
    emit('change-choice');
}

let submitToChain = () => {
    let data = { 
        ballot_id: props.ballot.hash,
        choices: choiceIndex,
        address: wallet.value?.address
    }
    
    VoterService.submitVote(wallet.value?.stakeAddress, data)
}
</script>
