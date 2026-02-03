  <template>
    <div>
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
