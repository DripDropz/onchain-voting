<template>
    <div>
        <PetitionList :petitions="publicPetition[0]?.[context].petitions" />

        <div
            v-if="!loadingMore && !publicPetition[0]?.[context]?.petitions?.length"
            class="py-10 text-center text-sm text-gray-500 dark:text-gray-400"
        >
            No petitions found yet.
        </div>

        <LoadMorePetitions :context="context" :params="params"/>
    </div>
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue';
import LoadMorePetitions from './LoadMorePetitions.vue';
import PetitionList from './PetitionList.vue';
import { usePetitionStore } from '@/stores/petition-store';
import { storeToRefs } from 'pinia';

const props = withDefaults(defineProps<{
    context?: string
    params: {}
}>(), {
    context: 'browse',
});

let petitionStore = usePetitionStore();
let { publicPetition, loadingMore } = storeToRefs(petitionStore);

if (!publicPetition.value[0]?.[props.context]?.petitions.length) {
    loadingMore.value = true;
    petitionStore.loadPublicPetitions(props.context, props.params).then()
}

onMounted(() => {
    petitionStore.setContext(props.context);
})

watch(() => props.context, (context) => {
    petitionStore.setContext(context);
});

</script>