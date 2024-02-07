<template>
    <VoterLayout page="Create a petition" :crumbs="crumbs">
        <section class="w-[50%] mx-auto pt-12 space-y-4">
            <div class="space-y-4 dark:text-white">
                <h1 class="text-5xl font-extrabold">
                    Write your petition's title
                </h1>
                <p class="text-xl">Tell people what you want to change.</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label
                        for="large-input"
                        class="block mb-2 font-medium text-xl dark:text-white"
                        >Petition title</label
                    >
                    <input
                        type="text"
                        v-model="form.title"
                        class="block w-full p-4 text-gray-900 border border-black dark::border-slate-700 rounded-lg sm:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    />
                </div>
                <div class="flex flex-row justify-end">
                    <div class="space-x-4">
                        <Link
                            :href="route('petitions.index')"
                            class="inline-flex justify-center rounded-md bg-white px-4 py-3 text-sm font-semibold shadow-sm hover:bg-gray-100 border border-black focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:col-start-2"
                        >
                            Back
                        </Link>
                        <button
                            @click.prevent="submit"
                            class="inline-flex justify-center rounded-md bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:col-start-2"
                        >
                            Continue
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </VoterLayout>
</template>
<script setup lang="ts">
import VoterLayout from "@/Layouts/VoterLayout.vue";
import AlertService from "@/shared/Services/alert-service";
import { Link, useForm } from "@inertiajs/vue3";
import PetitionData = App.DataTransferObjects.PetitionData;

const props = withDefaults(
    defineProps<{
        petition?: PetitionData,
        crumbs?: [],
    }>(),
    {
        petition: null,
    });

const form = useForm({
    title: props.petition.title,
    petition: props.petition.hash,
});

function submit(){
    if(!form.title){
        AlertService.show(["The petition's title is required."], "info");
        return;
    }

    form.post(route("petitions.store"));
}
</script>
