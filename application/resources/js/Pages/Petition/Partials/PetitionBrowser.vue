<template>
    <div>
        <div v-if="publicPetition[0]?.[context].petitions?.length > 0">
            <PetitionList :petitions="publicPetition[0]?.[context].petitions"/>

            <LoadMorePetitions :context="context" :params="params"/>
        </div>
        <div class="py-16" v-else>
            <EmptyPetition />
        </div>
    </div>
</template>

<script setup lang="ts">
import {onMounted} from 'vue';
import LoadMorePetitions from './LoadMorePetitions.vue';
import PetitionList from './PetitionList.vue';
import {usePetitionStore} from '@/stores/petition-store';
import {storeToRefs} from 'pinia';
import EmptyPetition from "@/Pages/Petition/Partials/EmptyPetition.vue";

const props = withDefaults(defineProps<{
    context?: string
    params: {}
}>(), {
    context: 'browse',
});

let petitionStore = usePetitionStore();
let {publicPetition, loadingMore} = storeToRefs(petitionStore);

if (!publicPetition.value[0]?.[props.context]?.petitions.length) {
    loadingMore.value = true;
    petitionStore.loadPublicPetitions(props.context, props.params).then()
}

onMounted(() => {
    petitionStore.setContext(props.context);
})

</script>
