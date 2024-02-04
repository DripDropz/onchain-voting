<template >
    <div class="">
        <PollList :polls="publicPoll[0].draft?.polls" />

        <LoadMorePolls :context="'draft'" :params="params" />
    </div>
</template>

<script lang="ts" setup>
import { onMounted } from 'vue';
import LoadMorePolls from './LoadMorePolls.vue';
import PollList from './PollList.vue';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';

let params = { statusfilter: ['draft'] }
let pollStore = usePollStore();
let { publicPoll } = storeToRefs(pollStore);

if (!publicPoll.value[0].draft?.polls.length) {
    pollStore.loadPublicPolls('draft', params).then()
}

onMounted(()=>{
    pollStore.setContext('draft');
})

</script>
