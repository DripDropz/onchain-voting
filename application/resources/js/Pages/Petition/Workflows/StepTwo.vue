<template>
    <VoterLayout page="Create a petition">
        <section class="w-full max-w-2xl mx-auto pt-12 px-4 space-y-6">
            <WorkflowProgress :current-step="2" />

            <div class="space-y-2 dark:text-white">
                <h1 class="text-4xl font-extrabold">Tell us about your petition</h1>
                <p class="text-lg text-gray-500 dark:text-gray-400">
                    You can always edit your petition, even after publishing.
                </p>
            </div>

            <div class="space-y-4">
                <v-md-editor v-model="form.description" height="400px" lang="en-US" />

                <div class="flex flex-row justify-end gap-3 pb-8">
                    <Link
                        :href="route('petitions.create', { petition: petition.hash })"
                        class="inline-flex justify-center rounded-lg bg-white dark:bg-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm font-semibold shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600"
                    >
                        ← Back
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
import PetitionData = App.DataTransferObjects.PetitionData;
import { Link, router, useForm } from "@inertiajs/vue3";
import AlertService from "@/shared/Services/alert-service";
import enUS from "@kangc/v-md-editor/lib/lang/en-US";
import VueMarkdownEditor from "@kangc/v-md-editor";
import WorkflowProgress from "../Partials/WorkflowProgress.vue";

const props = defineProps<{
    petition: PetitionData;
}>();

VueMarkdownEditor.lang.use("en-US", enUS);

const form = useForm({
    description: props.petition.description,
});

function submit() {
    if (form.description === "") {
        AlertService.show(["The petition's description is required."], "info");
        return;
    }

    form.patch(route("petitions.update", { petition: props.petition.hash }), {
        onSuccess: () => {
            router.visit(route("petitions.create.stepThree", { petition: props.petition.hash }));
        },
    });
}
</script>
