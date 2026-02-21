<template>
  <VoterLayout page="Create a petition">
    <section class="w-[50%] mx-auto pt-12 space-y-6">
      <div class="space-y-4 dark:text-white">
        <h1 class="text-5xl font-extrabold">Petition saved as a draft</h1>
        <p class="text-xl font-extrabold">What's next:</p>
      </div>
      <div class="text-xl space-y-4">
        <div class="flex space-x-4 items-center">
          <span class="flex items-center justify-center border-2 border-sky-500 text-sky-500 rounded-full h-9 w-9 shrink-0">1</span>
          <p class="dark:text-white">
            <span class="font-extrabold">Submit for admin review.</span>
            An admin will approve or reject your petition before it goes public.
          </p>
        </div>
        <div class="flex space-x-4 items-center">
          <span class="flex items-center justify-center border-2 border-sky-500 text-sky-500 rounded-full h-9 w-9 shrink-0">2</span>
          <p class="dark:text-white">
            <span class="font-extrabold">Once approved, publish it</span>
            and share it with your community to collect signatures.
          </p>
        </div>
        <p class="bg-gray-100 dark:bg-gray-800 dark:text-gray-300 w-full px-4 py-5 rounded-lg text-base">
          Your petition will appear publicly on OpenChainVote once it reaches the visibility signature threshold set by an admin.
        </p>
      </div>
      <div class="flex flex-row justify-end gap-3 pb-8">
        <button
            @click.prevent="saveDraft"
            class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
        >
          Save for later
        </button>
        <button
            @click.prevent="submitForReview"
            :disabled="submitting"
            class="inline-flex justify-center rounded-md bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ submitting ? 'Submitting…' : 'Submit for Review' }}
        </button>
      </div>
    </section>
  </VoterLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import AlertService from "@/shared/Services/alert-service";
import { router, useForm } from "@inertiajs/vue3";

const props = defineProps<{
  petition: PetitionData;
}>();

const submitting = ref(false);
const form = useForm({});

function saveDraft() {
  AlertService.show(["Petition saved as a draft."], "success");
  setTimeout(() => {
    router.visit(route('petitions.manage', { petition: props.petition.hash }));
  }, 800);
}

function submitForReview() {
  submitting.value = true;
  form.patch(route('petitions.submit', { petition: props.petition.hash }), {
    onSuccess: () => {
      AlertService.show(["Petition submitted for admin review."], "success");
      setTimeout(() => {
        router.visit(route('petitions.manage', { petition: props.petition.hash }));
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
