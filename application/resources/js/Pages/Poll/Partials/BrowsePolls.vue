<template >
    <div class="">
        <PollList :polls="publicPoll[0]?.[context].polls" />

        <LoadMorePolls :context="context" :params="params"/>
    </div>
</template>

<script lang="ts" setup>
import { onMounted } from 'vue';
import LoadMorePolls from './LoadMorePolls.vue';
import PollList from './PollList.vue';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';

const props = withDefaults(defineProps<{
    context?: string
    params: {}
}>(), {
    context: 'browse',
});

let pollStore = usePollStore();
let { publicPoll, loadingMore } = storeToRefs(pollStore);

if (!publicPoll.value[0]?.[props.context]?.polls.length) {
    loadingMore.value = true;
    pollStore.loadPublicPolls(props.context, props.params).then()
}

onMounted(() => {
    pollStore.setContext(props.context);
})

</script>
