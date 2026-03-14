  <template>
    <div class="grid gap-4 mt-6 sm:grid-cols-1 lg:grid-cols-2">
      <PetitionItem
        v-for="petition in petitions"
        :key="petition.hash"
        :petition="petition"
      />
    </div>
  </template>

  <script setup lang="ts">
  import PetitionData = App.DataTransferObjects.PetitionData;
  import PetitionItem from './PetitionItem.vue';



  defineProps<{
    petitions: PetitionData[];
  }>();

  </script>
