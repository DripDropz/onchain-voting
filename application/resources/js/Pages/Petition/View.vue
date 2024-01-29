<template>
    <VoterLayout page="Petition">
        <template #header>
            <Nav :crumbs="crumbs"/>
        </template>
      <section class="py-12 m-auto">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8"> 
                <PetitionSingle
                   :key="petition.hash"
                   :petition="petition"
                   :signature="signature"
                 />
            </div>
        </section>
    </VoterLayout>
</template>

<script lang="ts" setup>
import VoterLayout from '@/Layouts/VoterLayout.vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import SignatureData = App.DataTransferObjects.SignatureData;
import PetitionSingle from './Partials/PetitionSingle.vue';
import Nav from '../NavCrumbs.vue';

defineProps<{
    petition: PetitionData;
    crumbs: []
    signature:SignatureData;
}>();

</script>
