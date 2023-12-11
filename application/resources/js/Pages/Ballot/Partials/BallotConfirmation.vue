<template>
    <div
        class="absolute top-0 left-0 z-20 flex items-center justify-center w-full h-full p-8 text-white rounded-lg bg-indigo-800/90">
        <div class="w-4/5 h-full p-1 bg-indigo-700 rounded-lg">
            <Spinner v-if="submittingVote" class="absolute top-0 left-0 z-30" color="yellow" size="10" />

            <div class="flex flex-col justify-start h-full gap-4 text-center">
                <h3
                    class="w-full p-3 mx-auto text-xl font-bold text-center border-b-4 border-white border-double md:text-2xl xl:text-3xl">
                    {{ !!submittedvote ? 'Vote Successfully recorded' : 'Confirm your vote' }}
                </h3>

                <template v-if="!submittedvote">
                    <div class="">
                        <p class="p-3 text-sm font-semibold text-white">
                            You are about to submit your vote on chain.
                        </p>

                        <div class="mt-4">
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
                            class="rounded-full px-6 lg:px-8 py-2 lg:py-2.5 text-md xl:text-xl font-semibold text-indigo-950 shadow-sm bg-indigo-100 hover:bg-indigo-300">
                            Sign & Submit Onchain
                        </button>
                    </div>
                </template>
                <template v-else>
                    <p class="p-3 text-sm font-semibold text-white">
                        Your vote have been successfully recorded on chain.
                    </p>

                    <div class="mt-6">
                        <a target="_blank" :href="`${config?.explorer}/${submittedvote}`"
                            class="px-4 py-2 text-sm font-semibold text-white bg-indigo-900 rounded-lg hover:bg-indigo-800">
                            View Transaction
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';
import BallotData = App.DataTransferObjects.BallotData;
import BallotResponseData = App.DataTransferObjects.BallotResponseData;
import { storeToRefs } from 'pinia';
import BallotService from '../Services/ballot-service';
import Spinner from '@/Components/Spinner.vue';
import { useConfigStore } from '@/stores/config-store';

const props = defineProps<{
    ballot: BallotData;
    ballotResponse: BallotResponseData;
}>();

const configStore = useConfigStore();
const { config } = storeToRefs(configStore);

const ballotResponse = ref<null | BallotResponseData>(props.ballotResponse);
const choiceHash = ballotResponse.value?.choice.hash;
const choices = props.ballot?.questions?.[0]?.choices || [];
const choice = (choices.find(choice => choice.hash == choiceHash));
const submittingVote = ref(false);
const submittedvote = ref(null);
let IsRankedVote = computed(() => props.ballot.questions[0].type == 'ranked');
let choicesRanked = computed(() => {
    return props.ballot.questions[0].ranked_user_responses.map((res) => {
        return res.choice.hash
    })
})

let vote = computed<string[]>(() => {
    if (IsRankedVote.value) {
        return choicesRanked.value;
    } else {
        return [choice?.hash];
    }
})

if (ballotResponse?.value?.submit_tx) {
    submittedvote.value = ballotResponse?.value?.submit_tx;
}

const emit = defineEmits<{
    (e: 'change-choice'): void
}>();

const onChangeChoice = () => {
    emit('change-choice');
}

let submitToChain = async () => {
    submittingVote.value = true;
    if (props.ballot.hash && vote.value) {
        const data = await BallotService.submitVote(props.ballot.hash, vote.value);
        submittedvote.value = data?.tx;
    }
    submittingVote.value = false;
}
</script>
