<template >
    <div class="flex justify-center mt-8" v-if="!loadingMore && showMore">
        <PrimaryButton :theme="'primary'" @click="fetch">
            Load more
        </PrimaryButton>
    </div>
    <PollLoadingShell v-if="loadingMore" />
</template>

<script lang="ts" setup>
import PollLoadingShell from './PetitionLoadingShell.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePetitionStore } from '@/stores/petition-store';
import { storeToRefs } from 'pinia';

const props = withDefaults(defineProps<{
    context?: string
    params?:{}
}>(), {
    context: 'browse'
});

let petitionStore = usePetitionStore();
let { loadingMore } = storeToRefs(petitionStore);
let { showMore } = storeToRefs(petitionStore);

let fetch = () => {
    loadingMore.value = true;
    setTimeout(() => {
        petitionStore.loadPublicPetitions(props.context,props.params);
    }, 500);
}

</script>
