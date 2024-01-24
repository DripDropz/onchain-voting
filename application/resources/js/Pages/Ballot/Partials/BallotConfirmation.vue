<template>
    <div
        class="absolute top-0 left-0 z-20 flex items-center justify-center w-full h-full p-8 text-white rounded-lg bg-sky-800/90">
        <div class="w-4/5 h-full p-1 bg-sky-700 rounded-lg">
            <Spinner v-if="savingResponse" class="absolute top-0 left-0 z-30" color="yellow" size="10" />

            <div class="flex flex-col justify-start h-full gap-4 text-center">
                <h3
                    class="w-full p-3 mx-auto text-xl font-bold text-center border-b-4 border-white border-double md:text-2xl xl:text-3xl">
                    {{ !!voteRecordedOnChain ? 'Vote Successfully recorded' : 'Vote confirmation' }}
                </h3>

                <template v-if="!voteSaved || !voteRecordedOnChain || hasUnansweredQuestions">
                    <div class="">
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
                            <p v-for="choice in userResponse.choices" class="p-3 font-semibold text-white text-md xl:text-2xl">
                              {{ choice?.title }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-center gap-3 p-3 mt-auto">
                        <div class="flex flex-col justify-center">
                            <button  v-if="!voteRecordedOnChain" @click="emit('change-choice')"
                                class="px-2 lg:px-3 py-1 text-xs xl:text-sm border border-white border-dashed rounded-lg hover:bg-sky-900/90">
                                Change
                            </button>
                        </div>

                        <button v-if="!voteSaved && !voteRecordedOnChain" @click="emit('save-choice')" type="button"
                                class="rounded-full px-4 lg:px-6 py-0.5 xl:py-1 text-md xl:text-xl font-semibold text-sky-950 shadow-sm bg-sky-100 hover:bg-sky-300">
                            Save
                        </button>

                        <button v-if="voteSaved && !voteRecordedOnChain && !hasUnansweredQuestions" @click="emit('record-onchain')" type="button"
                                class="rounded-full px-4 lg:px-6 py-0.5 xl:py-1 text-md xl:text-xl font-semibold text-sky-950 shadow-sm bg-sky-100 hover:bg-sky-300">
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
                            class="px-4 py-2 text-sm font-semibold text-white bg-sky-900 rounded-lg hover:bg-sky-800">
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

const props = defineProps<{
    ballot: BallotData;
    userResponse: BallotResponseData;
}>();

const configStore = useConfigStore();
const { config } = storeToRefs(configStore);

const savingResponse = ref(false);
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
