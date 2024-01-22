<template>
  <h2 class="mb-8 text-lg font-semibold text-sky-500">Petition details</h2>
  <div class="p-6">
    <h2 class="mb-10 text-2xl font-bold leading-tight text-center text-gray-800 xl:text-4xl dark:text-gray-200">
      {{ petition.title }}
    </h2>
    <div class="flex flex-col gap-24 my-8 lg:flex-row dark:text-slate-100">
      <div class="lg:w-[60%]">
        <div class="h-[250px] border border-slate-700 border-dashed rounded-lg flex flex-col items-center">
          <div class="my-auto">
            <PhotoIcon class="w-16 h-16 m-auto text-slate-300" />
          </div>
        </div>

        <div class="my-8">
          <h2 class="text-2xl font-bold">{{ petition.hash }}</h2>
          <p class="my-2 font-semibold text-slate-500">Started this petition on {{ formatDate(petition.created_at) }}</p>
        </div>

        <div>
          <p>{{ petition.description }}</p>
        </div>
      </div>


      <div class=" lg:w-[30%]">
        <SignatureProgress />
        <SignPetitionForm :petition="petition" :user="user" />
      </div>


    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, defineProps } from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import { LockClosedIcon, PhotoIcon } from '@heroicons/vue/20/solid';
import voteAppLogo from '../../../../images/openchainvote.png';
import SignatureProgress from "./SignatureProgress.vue";
import SignPetitionForm from "./SignPetitionForm.vue";
import { usePage } from '@inertiajs/vue3';

const page = usePage();


const props = defineProps<{
  petition: PetitionData;
}>();

const formatDate = (dateString: string): string => {
  const options = { month: 'long', day: 'numeric', year: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const user = computed(() => page.props.auth.user);

</script>
