<script setup lang="ts">
import ModalRoute from "@/Components/ModalRoute.vue";
import QuestionData = App.DataTransferObjects.QuestionData;
import BallotData = App.DataTransferObjects.BallotData;
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import {useForm} from '@inertiajs/vue3';
import TextareaInput from '@/Components/TextareaInput.vue';
import { useModal } from "momentum-modal"
import AlertService from '@/shared/Services/alert-service';

const props = defineProps<{
    question: QuestionData;
    ballot: BallotData;
    choice: QuestionChoiceData;
}>();

const { close } = useModal();

const form = useForm({
    title: props.choice.title,
    description: props.choice.description,
});

function submit() {
    form.post(route('admin.ballots.questions.choices.edit', {ballot: props.ballot?.hash, question: props.question?.hash, choice: props.choice?.hash}), {
        preserveScroll: false,
        preserveState: false,
        onSuccess: () => {
            AlertService.show(['Choice edited successfully'], 'success');
            close();
        },
        onError: (errors) => {
            AlertService.show(
                Object
                .entries(errors)
                .map(([key, value]) => value)
            );
        },
    });
}
</script>

<template>
    <ModalRoute>
        <div class="flex flex-col">
            <div class="bg-gray-50 p-6 dark:bg-gray-900">
                <h2 class="font-semibold text-lg 2xl:text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit current choice
                </h2>
            </div>

            <form class="max-w-7xl space-y-6 p-6" @submit.prevent="submit">
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-white">Current title</label>
                    <div class="mt-2">
                        <input v-model="form.title" type="text" name="title" id="title" class="bg-white block w-full p-2 text-gray-900 bg-sky-100 border-0 rounded-md resize-none text-md xl:text-lg dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6 dark:bg-gray-900">
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-white">Current description</label>
                    <div class="mt-2">
                        <TextareaInput class="bg-white" v-model="form.description" rows="4" name="description" id="description" placeholder="Write a description..." />
                    </div>
                </div>
                <div
                    class="flex items-center justify-between mt-2">
                    <div class="flex">

                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit"
                                class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </ModalRoute>
</template>
