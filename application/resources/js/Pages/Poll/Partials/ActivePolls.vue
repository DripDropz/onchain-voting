<template >
    <div class="">
        <PollList :polls="publicPoll[0].active?.polls" />

        <LoadMorePolls :context="'active'" :params="params" />
    </div>
</template>

<script lang="ts" setup>
import { onMounted } from 'vue';
import LoadMorePolls from './LoadMorePolls.vue';
import PollList from './PollList.vue';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';

let params = { statusfilter: ['published'] }
let pollStore = usePollStore();
let { publicPoll } = storeToRefs(pollStore);

if (!publicPoll.value[0].active?.polls.length) {
    pollStore.loadPublicPolls('active', params).then()
}

onMounted(()=>{
    pollStore.setContext('active');
})

</script>
