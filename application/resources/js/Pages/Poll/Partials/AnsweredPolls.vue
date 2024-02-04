<template >
    <div class="inner-container">
        <PollList :polls="publicPoll[0].answered?.polls" />

        <LoadMorePolls :context="'answered'" :params="params" />
    </div>
</template>

<script lang="ts" setup>
import { onMounted } from 'vue';
import LoadMorePolls from './LoadMorePolls.vue';
import PollList from './PollList.vue';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';

let params = { hasAnswered: true }
let pollStore = usePollStore();
let { publicPoll } = storeToRefs(pollStore);

if (!publicPoll.value[0].answered?.polls.length) {
    pollStore.loadPublicPolls('answered', params).then()
}

onMounted(()=>{
    pollStore.setContext('answered');
})

</script>