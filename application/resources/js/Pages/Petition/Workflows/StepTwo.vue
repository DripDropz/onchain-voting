<template>
  <VoterLayout page="Create a petition">
    <section class="w-[50%] mx-auto pt-12 space-y-4">
      <div class="space-y-4 dark:text-white">
        <h1 class="text-5xl font-extrabold">
          Tell us about your petition
        </h1>
        <p class="text-xl">
          You can always edit your petition, even after publishing.
        </p>
      </div>
      <div class="space-y-4">
        <div>
          <v-md-editor v-model="form.description" height="400px" lang="en-US"></v-md-editor>
        </div>
        <div class="flex flex-row justify-end pb-8">
          <div class="space-x-4">
            <Link
                :href="
                                route('petitions.create', {
                                    petition: petition.hash,
                                })
                            "
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
import PetitionData = App.DataTransferObjects.PetitionData;
import {Link, router, useForm} from "@inertiajs/vue3";
import AlertService from "@/shared/Services/alert-service";
import enUS from '@kangc/v-md-editor/lib/lang/en-US';
import VueMarkdownEditor from '@kangc/v-md-editor';

const props = defineProps<{
  petition: PetitionData;
}>();

VueMarkdownEditor.lang.use('en-US', enUS);

const form = useForm({
  description: props.petition.description,
});

function submit() {
  if (form.description === "") {
    AlertService.show(["The petition's description is required."], "info");
    return;
  }

  form.patch(route("petitions.update", {petition: props.petition.hash}), {
    onSuccess: () => {
      router.visit(route("petitions.create.stepThree", {petition: props.petition.hash}));
    },
  });
}
</script>
