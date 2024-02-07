<template >
    <div class="flex justify-center mt-8" v-if="!loadingMore && showMore">
        <PrimaryButton :theme="'primary'" @click="fetch">
            Load more
        </PrimaryButton>
    </div>
    <PollLoadingShell v-if="loadingMore" />
</template>

<script lang="ts" setup>
import PollLoadingShell from './PollLoadingShell.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';

const props = withDefaults(defineProps<{
    context?: string
    params?:{}
}>(), {
    context: 'browse'
});

let pollStore = usePollStore();
let { loadingMore } = storeToRefs(pollStore);
let { showMore } = storeToRefs(pollStore);

let fetch = () => {
    loadingMore.value = true;
    pollStore.loadPublicPolls(props.context,props.params);
}

</script>
