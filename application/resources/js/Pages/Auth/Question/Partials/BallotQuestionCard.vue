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
                <QuestionStatusBadge :question="questionWithBallot"/>
                <div v-if="! questionWithBallot?.ballot?.live" class="flex flex-shrink-0 gap-2">
                    <Menu as="div" class="relative ml-auto">
                        <MenuButton class="-m-2.5 block p-2.5 text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Open options</span>
                            <EllipsisHorizontalIcon class="h-5 w-5" aria-hidden="true"/>
                        </MenuButton>
                        <transition enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">
                            <MenuItems
                                class="absolute right-0 z-10 mt-0.5 w-32 origin-top-right rounded-md bg-white dark:bg-gray-700 py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                                <MenuItem v-slot="{ active }">
                                    <Link
                                        :href="route( 'admin.ballots.questions.edit', { 'ballot': ballot?.hash, 'question': question?.hash})"
                                        :class="[active ? 'bg-gray-50 dark:bg-gray-900' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300']"
                                    >Edit<span class="sr-only">, {{ ballot?.title }}</span></Link>
                                </MenuItem>
                                <MenuItem v-slot="{ active }">
                                    <a @click.prevent="deleteQuestion()"
                                       :class="[active ? 'bg-red-50 dark:bg-red-900 cursor-pointer' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300']"
                                    >
                                        Delete<span class="sr-only">, {{ ballot?.title }}</span>
                                    </a>
                                </MenuItem>
                            </MenuItems>
                        </transition>
                    </Menu>

                </div>
            </div>

            <div class="flex flex-wrap items-center justify-start gap-4 sm:flex-nowrap">
                <div class="isolate inline-flex rounded-md shadow-sm">
                        <span type="button"
                              class="relative inline-flex items-center gap-x-1.5 rounded-l-md bg-white px-2 py-1 text-sm font-semibold text-slate-900 dark:text-gray-200 ring-1 ring-inset bg-slate-200 dark:bg-gray-700 ring-slate-300 dark:ring-gray-700">
                          Typee
                        </span>
                    <span
                        class="relative -ml-px inline-flex items-center rounded-r-md bg-white dark:bg-gray-500 px-2 py-1 text-sm font-semibold text-slate-900 dark:text-slate-100 ring-1 ring-inset ring-slate-300 dark:ring-gray-600">
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
                <QuestionChoiceList :choices="question.choices"/>

                <span v-if="! questionWithBallot.ballot?.live">
                    <NewQuestionChoiceButton :ballot="ballot" :question="questionWithBallot"/>
                </span>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import QuestionData = App.DataTransferObjects.QuestionData;
import QuestionChoiceList from "@/Pages/Auth/Question/QuestionChoice/Partials/QuestionChoiceList.vue";
import NewQuestionChoiceButton from "@/Pages/Auth/Question/QuestionChoice/Partials/NewQuestionChoiceButton.vue";
import BallotData = App.DataTransferObjects.BallotData;
import {Link, useForm} from "@inertiajs/vue3";
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import {EllipsisHorizontalIcon, PlusIcon} from '@heroicons/vue/20/solid';
import setAlert from "@/utils/set-alert";
import {useGlobalAlert} from "@/store/global-alert-store"
import QuestionStatusBadge from "./QuestionStatusBadge.vue";

const props = defineProps<{
    question: QuestionData;
    ballot: BallotData;
}>();

const questionWithBallot = {
    ballot: props.ballot,
    ...props.question
} as QuestionData;

const form = useForm({});

const alertStore = useGlobalAlert();
let deleteQuestion = () => form.delete(route('admin.ballots.questions.destroy', {
    'ballot': props.ballot?.hash,
    'question': props.question?.hash
}), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
        alertStore.showAlert(setAlert('Deletion successfully', 'success'));
    },
    onError: (errors) => {
        Object.entries(errors).forEach(([key, value]) => {
            alertStore.showAlert(setAlert(value, 'info'));
        });
    },
    onFinish: () => setTimeout(() => {
        window.location.reload()
    }, 1000 * 5)
});
</script>
