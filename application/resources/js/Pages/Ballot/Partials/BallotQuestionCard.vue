<template>
    <h3 class="mb-4 title3 xl:mb-6">{{ question.title }}</h3>
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
                :class="[selectedChoice === null ? 'bg-slate-500' : 'bg-white hover:bg-indigo-300']"
                @click="emit('submitted', selectedChoice)"
                class="rounded-full  px-8 lg:px-10 py-2.5 lg:py-3 text-md xl:text-xl font-semibold text-indigo-950 shadow-sm">
            Submit
        </button>
    </div>
</template>
<script lang="ts" setup>
import QuestionData = App.DataTransferObjects.QuestionData;
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import BallotQuestionChoice from "@/Pages/Ballot/Partials/BallotQuestionChoice.vue";
import {Ref, ref} from "vue";

const emit = defineEmits<{
    (e: 'submitted', choice?: QuestionChoiceData): void
}>();

const props = defineProps<{
    question: QuestionData;
}>();

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

</script>
