<template>
    <div class="border border-slate-200 bg-white dark:bg-gray-900 w-full rounded-xl dark:border-gray-700">
        <div class="dark:bg-gray-800 px-3 sm:p-4 rounded-t-xl">
            <div class="flex flex-wrap items-center justify-between sm:flex-nowrap">
                <div class="">
                    <div class="flex flex-col gap-0 items-start mb-3">
                        <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-gray-100">
                            {{ question.title }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-gray-300">
                            <a href="#">{{ question.description }}</a>
                        </p>
                    </div>
                </div>
                <div class="flex flex-shrink-0">
                    <button type="button"
                            class="relative ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-800 hover:bg-slate-50 dark:bg-gray-700 dark:hover:bg-indigo-600 dark:text-gray-200 ">
                        <PencilSquareIcon class="-ml-0.5 mr-1.5 h-4 w-4 text-slate-400 dark:text-gray-100" aria-hidden="true"/>
                        <span>Update</span>
                    </button>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-start gap-4 sm:flex-nowrap">
                <div class="isolate inline-flex rounded-md shadow-sm">
                        <span type="button" class="relative inline-flex items-center gap-x-1.5 rounded-l-md bg-white px-2 py-1 text-sm font-semibold text-slate-900 dark:text-gray-200 ring-1 ring-inset bg-slate-200 dark:bg-gray-700 ring-slate-300 dark:ring-gray-700">
                          Type
                        </span>
                        <span class="relative -ml-px inline-flex items-center rounded-r-md bg-white dark:bg-gray-500 px-2 py-1 text-sm font-semibold text-slate-900 dark:text-slate-100 ring-1 ring-inset ring-slate-300 dark:ring-gray-600">
                            {{ question.type }}
                        </span>
                  </div>
            </div>


        </div>

        <div class="px-3 sm:p-4 rounded-b-xl">
            <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-gray-100">
                Choices
            </h3>
            <div class="flex flex-col gap-1 mt-4">
                <QuestionChoiceList :choices="question.choices" />

                <NewQuestionChoiceButton :question="questionWithBallot" />
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import {PencilSquareIcon} from '@heroicons/vue/20/solid'
import QuestionData = App.DataTransferObjects.QuestionData;
import QuestionChoiceList from "@/Pages/Auth/Question/QuestionChoice/Partials/QuestionChoiceList.vue";
import NewQuestionChoiceButton from "@/Pages/Auth/Question/QuestionChoice/Partials/NewQuestionChoiceButton.vue";
import BallotData = App.DataTransferObjects.BallotData;
import BallotStatusBadge from "@/Pages/Auth/Ballot/Partials/BallotStatusBadge.vue";

const props = defineProps<{
    question: QuestionData;
    ballot?: BallotData;
}>();

const questionWithBallot = {
    ballot: props.ballot,
    ...props.question
}as QuestionData;

</script>
