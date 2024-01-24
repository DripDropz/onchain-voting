<template>
    <div class="flex flex-row items-center justify-between relative">
        <div
            class="relative border-4 border-sky-100 min-h-96 rounded-lg px-4 py-5 xl:px-6 xl:py-8 w-full lg:w-auto lg:min-w-[40rem] max-w-md">
            <Spinner v-if="working" />

            <BallotConfirmation @change-choice="clearResponses"
                                @save-choice="emit('save-response', userResponse$)"
                                @record-onchain="emit('record-onchain')"
                                :userResponse="userResponse$"
                                :ballot="ballot"
                                v-if="seeConfirmation" />
            <div>
                <div class="relative">
                    <h3 class="pr-40 mb-4 pointer-events-none title3 xl:mb-4">
                        <span class="text-white">{{ question.title }}</span>
                    </h3>

                    <div>
                      <p>{{question.description}}</p>
                    </div>
                </div>
                <ul class="flex flex-col gap-3 ballot-choices max-h-72 overflow-y-auto mt-4">
                    <BallotQuestionChoice
                        v-if="question.type != 'ranked'"
                        :isBallotOpen = "isBallotOpen"
                        @selected="onChoiceSelected($event)"
                        v-for="choice in question.choices" :key="choice.hash"
                        :choice="{ ...choice, selected: selectedChoices?.includes(choice.hash) }">
                    </BallotQuestionChoice>

<!--                    <RankedBallotQuestionChoiceCard v-else :choices="rankedChoicesData"-->
<!--                        @ranked="saveRankedChoices($event)" />-->
                </ul>
<!--                <div class="flex justify-center mt-8 lg:mt-10">-->
<!--                    <button v-if="isBallotOpen"  type="button" :disabled="selectedChoice === null && !rankedChoices"-->
<!--                        :class="[selectedChoice === null && rankedChoices == null ? 'bg-slate-500 hover:cursor-not-allowed' : 'bg-sky-100 hover:bg-sky-300']"-->
<!--                        @click="submitVote"-->
<!--                        class="rounded-full px-8 lg:px-10 py-2.5 lg:py-3 text-md xl:text-xl font-semibold text-sky-950 shadow-sm">-->
<!--                        Save-->
<!--                    </button>-->
<!--                    <div v-else class="caret-sky-500">-->
<!--                        Voting closed-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import QuestionData = App.DataTransferObjects.QuestionData;
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import BallotResponseData = App.DataTransferObjects.BallotResponseData;
import BallotQuestionChoice from "@/Pages/Ballot/Partials/BallotQuestionChoice.vue";
import Spinner from "@/Components/Spinner.vue";
import {computed, ref, Ref, watch} from "vue";
import {useWalletStore} from "@/cardano/stores/wallet-store";
import {storeToRefs} from "pinia";
import {usePage} from "@inertiajs/vue3";
import BallotConfirmation from "@/Pages/Ballot/Partials/BallotConfirmation.vue";

const props = defineProps<{
    ballot: BallotData;
    question: QuestionData;
    userResponse: BallotResponseData | null
    registeredToVote: boolean | null,
    working: boolean
}>();

const isBallotOpen = ref(props?.ballot.open);
const userResponse$ = ref<BallotResponseData>(props?.userResponse);

const user = usePage().props.auth.user;

let seeConfirmation = computed(() =>  (
    (userResponse$.value?.choices.length > 0) && ( props.registeredToVote || !!props.userResponse?.submit_tx))
);

const emit = defineEmits<{
    (e: 'save-response', response?: BallotResponseData): void
    (e: 'record-onchain'): void
}>();

const selectedChoices = computed(() => {
    return userResponse$.value?.choices.map((choice) => choice.hash);
});

function onChoiceSelected(choices: QuestionChoiceData[]) {
    userResponse$.value = {
        ...userResponse$.value,
        choices
    };
}

watch(props, () => {
    isBallotOpen.value = props?.ballot.open;
    userResponse$.value = props?.userResponse;
}, {deep: true});

function clearResponses() {
    userResponse$.value = null
}
</script>
