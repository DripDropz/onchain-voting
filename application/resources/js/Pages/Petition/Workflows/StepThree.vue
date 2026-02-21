<template>
    <VoterLayout page="Create a petition">
        <section class="w-full max-w-2xl mx-auto pt-12 px-4 space-y-6">
            <WorkflowProgress :current-step="3" />

            <div class="space-y-2 dark:text-white">
                <h1 class="text-4xl font-extrabold">Petition saved as a draft</h1>
                <p class="text-lg font-semibold dark:text-gray-300">What happens next:</p>
            </div>

            <ol class="space-y-4">
                <li class="flex items-start gap-4">
                    <span class="flex items-center justify-center shrink-0 w-9 h-9 rounded-full border-2 border-sky-500 text-sky-600 dark:text-sky-400 font-semibold text-sm">
                        1
                    </span>
                    <p class="dark:text-white pt-1.5">
                        <span class="font-extrabold">Submit for admin review.</span>
                        An admin will approve or reject your petition before it goes public.
                    </p>
                </li>
                <li class="flex items-start gap-4">
                    <span class="flex items-center justify-center shrink-0 w-9 h-9 rounded-full border-2 border-sky-500 text-sky-600 dark:text-sky-400 font-semibold text-sm">
                        2
                    </span>
                    <p class="dark:text-white pt-1.5">
                        <span class="font-extrabold">Once approved, publish it</span>
                        and share it with your community to collect signatures.
                    </p>
                </li>
            </ol>

            <p class="bg-gray-100 dark:bg-gray-800 dark:text-gray-300 w-full px-4 py-4 rounded-lg text-sm leading-relaxed">
                Your petition will appear publicly on OpenChainVote once it reaches the visibility signature threshold set by an admin.
            </p>

            <div class="flex flex-row justify-end gap-3 pb-8">
                <button
                    @click.prevent="saveDraft"
                    class="inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    Save for later
                </button>
                <button
                    @click.prevent="submitForReview"
                    :disabled="submitting"
                    class="inline-flex items-center gap-2 justify-center rounded-lg bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span>{{ submitting ? "Submitting…" : "Submit for Review" }}</span>
                </button>
            </div>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import { ref } from "vue";
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import AlertService from "@/shared/Services/alert-service";
import { router, useForm } from "@inertiajs/vue3";
import WorkflowProgress from "../Partials/WorkflowProgress.vue";

const props = defineProps<{
    petition: PetitionData;
}>();

const submitting = ref(false);
const form = useForm({});

function saveDraft() {
    AlertService.show(["Petition saved as a draft."], "success");
    setTimeout(() => {
        router.visit(route("petitions.manage", { petition: props.petition.hash }));
    }, 800);
}

function submitForReview() {
    submitting.value = true;
    form.patch(route("petitions.submit", { petition: props.petition.hash }), {
        onSuccess: () => {
            AlertService.show(["Petition submitted for admin review."], "success");
            setTimeout(() => {
                router.visit(route("petitions.manage", { petition: props.petition.hash }));
            }, 800);
        },
        onError: () => {
            AlertService.show(["There was an error submitting your petition."], "error");
            submitting.value = false;
        },
        onFinish: () => {
            submitting.value = false;
        },
    });
}
</script>
