<template>
    <h2 class="text-sky-500 font-semibold text-lg mb-8">Petition details</h2>
    <div>
        <h2 class="font-bold text-center text-2xl xl:text-4xl text-gray-800 dark:text-gray-200 leading-tight mb-10">
        {{ petition.title }}
    </h2>
    <div class="dark:text-slate-100 my-8 flex flex-row gap-24">
      <div class="w-[60%]">
        <div class="h-[250px] border border-slate-700 border-dashed rounded-lg flex flex-col items-center">
<!--            <div class="flex flex-row items-center justify-center gap-2 p-2 bg-slate-100 w-[200px] rounded-lg m-4">-->
<!--                <LockClosedIcon class="h-4 w-8" />-->
<!--                <p class="text-slate-500">Locked by admin</p>-->
<!--            </div>-->
            <div class="my-auto">
                <PhotoIcon class="h-16 w-16 text-slate-300 m-auto"/>
            </div>

         </div>

         <div class="my-8">
          <h2 class="text-2xl font-bold">{{ petition.hash}}</h2>
              <p class="text-slate-500 font-semibold my-2">Started this petition on {{ formatDate(petition.created_at) }}</p>
        </div>

        <div>
          <p>{{ petition.description }}</p>
        </div>
      </div>


      <div class=" w-[30%]">
            <SignatureProgress />
            <SignPetitionForm />
      </div>


    </div>
    </div>
  </template>

  <script setup lang="ts">
  import { defineProps } from 'vue';
  import PetitionData = App.DataTransferObjects.PetitionData;
  import { LockClosedIcon, PhotoIcon } from '@heroicons/vue/20/solid';
  import voteAppLogo from '../../../../images/openchainvote.png';
  import SignatureProgress from "./SignatureProgress.vue";
  import SignPetitionForm from "./SignPetitionForm.vue";

  const props = defineProps<{
    petition: PetitionData;
  }>();

  const formatDate = (dateString: string): string => {
  const options = { month: 'long', day: 'numeric', year: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
  </script>
