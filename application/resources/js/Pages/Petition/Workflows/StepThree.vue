<template>
    <VoterLayout page="Create a petition">
        <section class="w-[50%] mx-auto pt-12 space-y-6">
            <div class="space-y-4 dark:text-white">
                <h1 class="text-5xl font-extrabold">Your petition is ready</h1>
                <p class="text-xl font-extrabold">Next up:</p>
            </div>
            <div class="text-xl space-y-4">
                <div class="flex space-x-4 items-center">
                    <span
                        class="flex items-center justify-center border-2 border-sky-500 text-sky-500 rounded-full h-9 w-9"
                        >1</span
                    >
                    <p class="dark:text-white">
                        <span class="font-extrabold"
                            >Create your petition.</span
                        >
                        You can edit your petition up until your first
                        signature.
                    </p>
                </div>
                <div class="flex space-x-4 items-center">
                    <span
                        class="flex items-center justify-center border-2 border-sky-500 text-sky-500 rounded-full h-9 w-9"
                        >2</span
                    >
                    <p class="dark:text-white">
                        <span class="font-extrabold">Share it</span>
                        with people you know or to online communities.
                    </p>
                </div>
                <p class="bg-gray-100 w-full px-4 py-5 rounded-lg">
                    Your petition will be visible on OpenChainvote.com after it
                    reaches 5 signatures.
                </p>
            </div>
            <div class="flex flex-row justify-end">
                <div class="space-x-4">
                    <button
                        class="inline-flex justify-center rounded-md bg-white px-4 py-3 text-sm font-extrabold shadow-sm hover:bg-gray-100 border border-black focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:col-start-2"
                    >
                        Preview petition
                    </button>
                    <button
                        @click.prevent="submit"
                        class="inline-flex justify-center rounded-md bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:col-start-2"
                    >
                        Create petition
                    </button>
                </div>
            </div>
        </section>
    </VoterLayout>
</template>
<script setup lang="ts">
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import AlertService from "@/shared/Services/alert-service";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
    petition: PetitionData;
}>();

function submit(){
    AlertService.show(["The petition has been created."], "success");
    setTimeout(() => { router.visit(route('petitions.index')) }, 1000)
}
</script>
