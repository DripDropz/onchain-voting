<template>
    <VoterLayout page="Create a petition" :crumbs="crumbs">
        <section class="w-full max-w-4xl mx-auto pt-12 px-4 space-y-6">
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
                        <span class="font-extrabold">Admin review &amp; goal-setting.</span>
                        An admin will review your petition, set the signature goal required for it to go public, and then approve or reject it.
                    </p>
                </li>
                <li class="flex items-start gap-4">
                    <span class="flex items-center justify-center shrink-0 w-9 h-9 rounded-full border-2 border-sky-500 text-sky-600 dark:text-sky-400 font-semibold text-sm">
                        2
                    </span>
                    <p class="dark:text-white pt-1.5">
                        <span class="font-extrabold">Once approved, you publish it</span>
                        and share it with your community to start collecting signatures.
                    </p>
                </li>
                <li class="flex items-start gap-4">
                    <span class="flex items-center justify-center shrink-0 w-9 h-9 rounded-full border-2 border-sky-500 text-sky-600 dark:text-sky-400 font-semibold text-sm">
                        3
                    </span>
                    <p class="dark:text-white pt-1.5">
                        <span class="font-extrabold">Reach the signature threshold</span>
                        set by the admin for your petition to become publicly visible on OpenChainVote.
                    </p>
                </li>
            </ol>

            <p class="bg-gray-100 dark:bg-gray-800 dark:text-gray-300 w-full px-4 py-4 rounded-lg text-sm leading-relaxed">
                <strong>Note:</strong> Signature goals are determined by an admin during the review process — you do not need to set them yourself.
                If your petition is approved but you want to make changes, you can revert it back to draft from your petition management page.
            </p>

            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 space-y-3">
                <h2 class="text-lg font-bold dark:text-white">Optional: Gated Signing Criteria</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Configure NFT/FT policy gating now so signers must hold the required asset before they can participate.
                </p>
                <Criteria :model="petition" :return-route="'petitions.create.stepThree'" />
            </div>

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
import Criteria from "@/shared/components/Criteria.vue";

const props = defineProps<{
    petition: PetitionData;
    crumbs?: [];
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
