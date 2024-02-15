<template>
    <div
        class="absolute top-0 left-0 z-20 flex items-center justify-center w-full h-full p-8 text-white rounded-lg bg-sky-800/90">
        <div class="w-4/5 h-full p-1 rounded-lg bg-sky-700">
            <Spinner v-if="savingResponse" class="absolute top-0 left-0 z-30" color="yellow" size="10"/>

            <div class="flex flex-col justify-start h-full gap-4 text-center">
                <h3
                    class="w-full p-3 mx-auto text-xl font-bold text-center border-b-4 border-white border-double md:text-2xl xl:text-3xl">
                    {{ !!voteRecordedOnChain ? 'Vote Successfully recorded' : 'Vote confirmation' }}
                </h3>

                <template v-if="!voteSaved || !voteRecordedOnChain || hasUnansweredQuestions">
                    <div class="relative">
                        <p class="p-3 text-sm font-semibold text-white">
                            <span v-if="!voteSaved">
                                Your vote is not yet saved.
                            </span>
                            <span v-else-if="!voteRecordedOnChain">
                                Your vote is not yet recorded on chain.
                            </span>
                        </p>

                        <div class="mt-4">
                            <p class="text-xs">Your Selection(s)</p>
                            <p v-for="choice in userResponse.choices"
                               class="p-3 font-semibold text-white text-md xl:text-2xl">
                                {{ choice?.title }}
                            </p>
                        </div>

                        <div>
                            {{ confirmationCount }}/7 confirmations on-chain.
                        </div>

                        <div class="flex flex-col items-center justify-center gap-2 absolute w-full">
                            <svg role="status" v-if="confirmationCount<7"
                                 class="relative z-30 w-6 h-6 text-gray-200 dark:text-gray-600 fill-yellow-400 animate-spin"
                                 viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                    fill="currentFill"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="flex justify-center gap-3 p-3 mt-auto">
                        <div class="flex flex-col justify-center">
                            <button v-if="!voteRecordedOnChain" @click="emit('change-choice')"
                                    class="px-2 py-1 text-xs border border-white border-dashed rounded-lg lg:px-3 xl:text-sm hover:bg-sky-900/90">
                                Change
                            </button>
                        </div>

                        <button v-if="!voteSaved && !voteRecordedOnChain" @click="emit('save-choice')" type="button"
                                class="rounded-full px-4 lg:px-6 py-0.5 xl:py-1 text-md xl:text-xl font-semibold text-sky-950 shadow-sm bg-sky-100 hover:bg-sky-300">
                            Save
                        </button>

                        <button v-if="voteSaved && !voteRecordedOnChain && !hasUnansweredQuestions "
                                @click="emit('record-onchain')" type="button"
                                :disabled="confirmationCount < 7 && !hasRegistration"
                                class="rounded-full px-4 lg:px-6 py-0.5 xl:py-1 text-md xl:text-xl font-semibold text-sky-950 shadow-sm"
                                :class="[(confirmationCount < 7 || !hasRegistration)?'bg-slate-400 cursor-not-allowed':'bg-sky-100 hover:bg-sky-300']">
                            Record ballot on chain
                        </button>

                        <span v-if="voteSaved && !voteRecordedOnChain && hasUnansweredQuestions"
                              class="py-0.5 xl:py-1 text-sm xl:text-md font-semibold text-sky-100">
                            Answer all questions to submit vote on chain.
                        </span>
                    </div>
                </template>

                <template v-else>
                    <p class="p-3 text-sm font-semibold text-white">
                        Your vote have been successfully recorded on chain.
                    </p>

                    <div class="mt-6">
                        <a target="_blank" :href="`${config?.explorer}/${submittedVote}`"
                           class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-sky-900 hover:bg-sky-800">
                            View Transaction
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import {computed, ref} from 'vue';
import BallotData = App.DataTransferObjects.BallotData;
import BallotResponseData = App.DataTransferObjects.BallotResponseData;
import Spinner from '@/Components/Spinner.vue';
import {useConfigStore} from "@/stores/config-store";
import {storeToRefs} from "pinia";
import {useVoterStore} from '@/Pages/Voter/stores/voter-store';


const props = defineProps<{
    ballot: BallotData;
    userResponse: BallotResponseData;
}>();

const configStore = useConfigStore();
const voterStore = useVoterStore();
const {config} = storeToRefs(configStore);
const {confirmationCount, voterRegistrations} = storeToRefs(voterStore);
voterStore.frostConfirm(props.ballot.hash);

const savingResponse = ref(false);
let hasRegistration = computed(() => !!voterRegistrations.value?.[props.ballot.hash].registration);

const voteSaved = computed(() => !!props.userResponse.hash && props.userResponse?.choices?.length > 0);
const submittedVote = computed(() => props.userResponse.submit_tx);
const voteRecordedOnChain = computed(() => !!submittedVote.value);
const hasUnansweredQuestions = computed(() => props.ballot.user_responses.length < props.ballot.questions.length);

const emit = defineEmits<{
    (e: 'change-choice'): void
    (e: 'save-choice'): void
    (e: 'record-onchain'): void
}>();

</script>
