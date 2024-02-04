<template >
    <div class="">
        <PollList :polls="publicPoll[0].browse?.polls" />

        <LoadMorePolls />
    </div>
</template>

<script lang="ts" setup>
import { onMounted } from 'vue';
import LoadMorePolls from './LoadMorePolls.vue';
import PollList from './PollList.vue';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';

let pollStore = usePollStore();
let { publicPoll } = storeToRefs(pollStore);

if (!publicPoll.value[0].browse?.polls.length) {
    pollStore.loadPublicPolls('browse').then()
}

onMounted(() => {
    pollStore.setContext('browse');
})

</script>
