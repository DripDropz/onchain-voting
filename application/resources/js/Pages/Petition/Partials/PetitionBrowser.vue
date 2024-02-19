<template>
    <div>
        <PetitionList v-if="browsePetitions" :petitions="publicPetition[0].browse?.petitions" :currentTab="currentTab" />

        <LoadMorePetitions />
    </div>
</template>

<script setup lang="ts">
import { defineProps, onMounted } from 'vue';
import PetitionList from './PetitionList.vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import LoadMorePetitions from './LoadMorePetitions.vue';
import { usePetitionStore } from '@/stores/petition-store';
import { storeToRefs } from 'pinia';

const props = defineProps<{
    browsePetitions: Array<PetitionData>,
    currentTab: string,
}>();

let petitionStore = usePetitionStore();
let { publicPetition } = storeToRefs(petitionStore);

if (!publicPetition.value[0].browse?.petitions.length) {
    petitionStore.loadPublicPetitions('browse').then()
}

onMounted(() => {
    petitionStore.setContext('browse');
})

</script>