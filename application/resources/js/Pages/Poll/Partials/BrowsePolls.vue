<template >
    <div class="inner-container">
        <PollList :polls="pollsData" />

        <LoadMorePolls />
    </div>
</template>

<script lang="ts" setup>
import PollData = App.DataTransferObjects.PollData;
import LoadMorePolls from './LoadMorePolls.vue';
import PollList from './PollList.vue';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';

let pollStore = usePollStore();
let { pollsData } = storeToRefs(pollStore);


</script>
