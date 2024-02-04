<template >
    <div class="">
        <PollList :polls="publicPoll[0].pending?.polls" />

        <LoadMorePolls :context="'pending'" :params="params" />
    </div>
</template>

<script lang="ts" setup>
import { onMounted } from 'vue';
import LoadMorePolls from './LoadMorePolls.vue';
import PollList from './PollList.vue';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';

let params = { hasPending: true }
let pollStore = usePollStore();
let { publicPoll } = storeToRefs(pollStore);

if (!publicPoll.value[0].pending?.polls.length) {
    pollStore.loadPublicPolls('pending', params).then()
}

onMounted(()=>{
    pollStore.setContext('pending');
})

</script>
