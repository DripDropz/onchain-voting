<template>
    <VoterLayout page="Petition">
      <section class="py-12 m-auto">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8"> 
                <PetitionSingle
                   :key="petition.hash"
                   :petition="petition"
                 />
            </div>
        </section>
    </VoterLayout>
</template>

<script lang="ts" setup>
import VoterLayout from '@/Layouts/VoterLayout.vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import PetitionSingle from './Partials/PetitionSingle.vue';

defineProps<{
    petition: PetitionData;
}>();

</script>
