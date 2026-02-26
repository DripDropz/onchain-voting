<template>
    <VoterLayout page="Create a poll" :crumbs="crumbs">
        <section class="w-full max-w-3xl mx-auto pt-12 px-4 space-y-6">
            <WorkflowProgress :current-step="1" />

            <div class="space-y-2 dark:text-white">
                <h1 class="text-4xl font-extrabold">Write your poll question</h1>
                <p class="text-lg text-gray-500 dark:text-gray-400">Add the options people can vote on.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="poll-question" class="block mb-2 font-medium text-base dark:text-white">
                        Poll question
                    </label>
                    <input
                        id="poll-question"
                        type="text"
                        v-model="form.question"
                        placeholder="e.g. Should we fund this proposal?"
                        class="block w-full p-4 text-gray-900 border border-gray-300 dark:border-slate-700 rounded-lg sm:text-md focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white"
                    />
                </div>

                <div class="space-y-2">
                    <label class="block font-medium text-base dark:text-white">Voting options</label>
                    <div v-for="(option, index) in form.options" :key="index" class="flex items-center gap-2">
                        <input
                            type="text"
                            v-model="form.options[index]"
                            class="block w-full p-3 text-gray-900 border border-gray-300 dark:border-slate-700 rounded-lg sm:text-md focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white"
                            :placeholder="`Option ${index + 1}`"
                        />
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-lg border border-red-300 px-3 py-3 text-red-500 hover:bg-red-50 dark:border-red-500/40 dark:hover:bg-red-500/10"
                            @click.prevent="removeOption(index)"
                        >
                            <XMarkIcon class="w-4 h-4" />
                        </button>
                    </div>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-sky-300 px-3 py-2 text-sm font-semibold text-sky-600 hover:bg-sky-50 dark:border-sky-500/40 dark:text-sky-300 dark:hover:bg-sky-500/10"
                        @click.prevent="addOption"
                    >
                        <PlusIcon class="w-4 h-4" />
                        Add option
                    </button>
                </div>

                <label class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-slate-700 px-4 py-3">
                    <span class="font-medium dark:text-white">Publish result on-chain</span>
                    <input type="checkbox" v-model="form.publishOnchain" class="rounded border-gray-300 text-sky-500 focus:ring-sky-500" />
                </label>

                <div class="flex flex-row justify-end gap-3 pb-4">
                    <Link
                        :href="route('polls.index')"
                        class="inline-flex justify-center rounded-lg bg-white dark:bg-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm font-semibold shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        @click.prevent="submit"
                        class="inline-flex justify-center rounded-lg bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600"
                    >
                        Continue →
                    </button>
                </div>
            </div>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import { computed } from "vue";
import VoterLayout from "@/Layouts/VoterLayout.vue";
import AlertService from "@/shared/Services/alert-service";
import { Link, useForm } from "@inertiajs/vue3";
import { PlusIcon, XMarkIcon } from "@heroicons/vue/20/solid";
import WorkflowProgress from "../Partials/WorkflowProgress.vue";
import PollData = App.DataTransferObjects.PollData;

const props = withDefaults(
    defineProps<{
        poll?: PollData | null;
        crumbs?: [];
    }>(),
    {
        poll: null,
    }
);

const startingOptions = computed(() => {
    const options = props.poll?.question?.choices?.map((choice) => choice.title) ?? [];
    return options.length ? options : ["", ""];
});

const form = useForm({
    question: props.poll?.question?.title ?? "",
    options: [...startingOptions.value],
    publishOnchain: props.poll?.publish_on_chain ?? false,
    poll: props.poll?.hash ?? "",
});

function addOption() {
    form.options.push("");
}

function removeOption(index: number) {
    if (form.options.length <= 1) {
        return;
    }
    form.options.splice(index, 1);
}

function submit() {
    const normalizedOptions = form.options.map((option) => option.trim()).filter((option) => option.length > 0);

    if (!form.question.trim() || normalizedOptions.length < 1) {
        AlertService.show(["Question and at least one voting option are required."], "info");
        return;
    }

    form.options = normalizedOptions;
    form.post(route("polls.store"));
}
</script>
