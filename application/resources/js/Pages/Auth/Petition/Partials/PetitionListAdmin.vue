<template>
  <div class="flex flex-col gap-4 mt-6">
    <PetitionListItem
      v-for="petition in petitions"
      :key="petition.hash"
      :petition="petition"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import PetitionListItem from './PetitionListItem.vue';



const props = defineProps<{
  petitions: PetitionData[];
}>();

</script>