<template>
    <VoterLayout page="Create a petition" :crumbs="crumbs">
        <section class="w-full max-w-2xl mx-auto pt-12 px-4 space-y-6">
            <WorkflowProgress :current-step="1" />

            <div class="space-y-2 dark:text-white">
                <h1 class="text-4xl font-extrabold">Write your petition's title</h1>
                <p class="text-lg text-gray-500 dark:text-gray-400">Tell people what you want to change.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label
                        for="petition-title"
                        class="block mb-2 font-medium text-base dark:text-white"
                    >
                        Petition title
                    </label>
                    <input
                        id="petition-title"
                        type="text"
                        v-model="form.title"
                        placeholder="e.g. Improve public transport in our city"
                        class="block w-full p-4 text-gray-900 border border-gray-300 dark:border-slate-700 rounded-lg sm:text-md focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-sky-500 dark:focus:border-sky-500"
                    />
                </div>

                <div class="flex flex-row justify-end gap-3 pb-4">
                    <Link
                        :href="route('petitions.index')"
                        class="inline-flex justify-center rounded-lg bg-white dark:bg-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm font-semibold shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        @click.prevent="submit"
                        class="inline-flex justify-center rounded-lg bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                    >
                        Continue →
                    </button>
                </div>
            </div>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import VoterLayout from "@/Layouts/VoterLayout.vue";
import AlertService from "@/shared/Services/alert-service";
import { Link, useForm } from "@inertiajs/vue3";
import WorkflowProgress from "../Partials/WorkflowProgress.vue";
import PetitionData = App.DataTransferObjects.PetitionData;

const props = withDefaults(
    defineProps<{
        petition?: PetitionData;
        crumbs?: [];
    }>(),
    {
        petition: null,
    }
);

const form = useForm({
    title: props.petition?.title ?? "",
    petition: props.petition?.hash ?? "",
});

function submit() {
    if (!form.title) {
        AlertService.show(["The petition's title is required."], "info");
        return;
    }
    form.post(route("petitions.store"));
}
</script>
