<template>
    <form class="relative" @submit.prevent="submit">
        <div
            class="overflow-visible border border-gray-300 rounded-lg shadow-sm dark:border-gray-700 focus-within:border-indigo-500 dark:focus-within:border-indigo-600 focus-within:ring-1 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-500">
            <label for="title" class="sr-only">Title</label>
            <input type="text" name="title" id="title" v-model="form.title"
                   class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-white dark:bg-gray-900 rounded-lg"
                   placeholder="Title"/>

            <label for="description" class="sr-only">Description</label>
            <textarea rows="4" name="description" id="description" v-model="form.description"
                      class="block w-full resize-none border-0 py-0 text-gray-900 rounded-lg dark:text-gray-100 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6 bg-white dark:bg-gray-900"
                      placeholder="Write a description..."/>
        </div>

        <div
            class="flex items-center justify-between py-3 mt-2">
            <div class="flex">

            </div>
            <div class="flex-shrink-0">
                <button type="submit"
                        class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Add
                </button>
            </div>
        </div>
    </form>
</template>
<script setup lang="ts">
import {useForm} from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import QuestionData = App.DataTransferObjects.QuestionData;
import {Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions} from '@headlessui/vue';
import {ChevronUpDownIcon, CheckIcon} from '@heroicons/vue/20/solid';
import setAlert from "@/utils/set-alert";
import {useGlobalAlert} from "@/store/global-alert-store";

const props = defineProps<{
    question: QuestionData;
    ballot?: BallotData;
}>();

const form = useForm({
    title: null,
    description: null,
});

console.log('ballot::', props.ballot);

const alertStore = useGlobalAlert();

function submit() {
    form.post(route('ballots.questions.choices.store', {ballot: props.ballot?.hash, question: props.question?.hash}), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            alertStore.showAlert(setAlert('Choice added successfully', 'success'));
        },
        onError: (errors) => {
            console.log(errors);
            Object.entries(errors).forEach(([key, value]) => {
                alertStore.showAlert(setAlert(value, 'info'));
            });
        },
    });
}
</script>
