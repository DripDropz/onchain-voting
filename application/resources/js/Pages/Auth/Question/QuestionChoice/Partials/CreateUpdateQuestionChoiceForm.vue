<template>
    <form class="relative" @submit.prevent="submit">
        <div
            class="overflow-visible border border-gray-300 rounded-lg shadow-sm dark:border-gray-700 focus-within:border-sky-500 dark:focus-within:border-sky-600 focus-within:ring-1 focus-within:ring-sky-500 dark:focus-within:ring-sky-500">
            <label for="title" class="sr-only">Title</label>
            <input type="text" name="title" id="title" v-model="form.title"
                   class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-sky-100 dark:bg-gray-900 rounded-lg"
                   placeholder="Title" />

            <label for="description" class="sr-only">Description</label>
            <TextareaInput v-model="form.description" rows="4" name="description" id="description" placeholder="Write a description..." />
        </div>

        <div
            class="flex items-center justify-between py-3 mt-2">
            <div class="flex">

            </div>
            <div class="flex-shrink-0">
                <button type="submit"
                        class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
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
import AlertService from '@/shared/Services/alert-service';
import { useModal } from "momentum-modal"
import TextareaInput from '@/Components/TextareaInput.vue';

const props = defineProps<{
    question: QuestionData;
    ballot: BallotData;
}>();

const form = useForm({
    title: '',
    description: '',
});

const { close } = useModal();

function submit() {
    form.post(route('admin.ballots.questions.choices.store', {ballot: props.ballot?.hash, question: props.question?.hash}), {
        preserveScroll: false,
        preserveState: false,
        onSuccess: () => {
            AlertService.show(['Choice added successfully'], 'success');
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
