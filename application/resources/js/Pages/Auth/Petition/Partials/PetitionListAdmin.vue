<template>
  <div>
    <PetitionListItem
      v-for="petition in petitions"
      :key="petition.hash"
      :petition="petition"
    />
    
  </div>
</template>

<script setup lang="ts">
import { defineProps, computed } from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import PetitionListItem from './PetitionListItem.vue';



const props = defineProps<{
  petitions: PetitionData[];
}>();

</script>