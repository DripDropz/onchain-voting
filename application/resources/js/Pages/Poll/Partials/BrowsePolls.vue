<template >
    <div class="">
        <PollList :polls="(publicPoll[0]?.[context]?.polls) || []" />

        <div
            v-if="!loadingMore && !publicPoll[0]?.[context]?.polls?.length"
            class="py-16"
        >
            <div class="flex flex-col items-center justify-center text-center rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 py-16 px-6">
                <DocumentTextIcon class="w-12 h-12 text-gray-300 dark:text-gray-600" />
                <h3 class="mt-4 text-base font-semibold text-gray-700 dark:text-gray-200">
                    No polls available yet
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md">
                    Polls allow wallet-connected users to gather community sentiment on any topic.
                </p>
                <div class="mt-6 space-y-4 max-w-lg">
                    <div class="flex items-start gap-3 text-left">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 text-sm font-semibold shrink-0">1</div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Create a poll</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Ask a question and provide voting options</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 text-left">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 text-sm font-semibold shrink-0">2</div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Submit for review</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Admins verify your poll meets guidelines</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 text-left">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 text-sm font-semibold shrink-0">3</div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Publish and collect votes</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Share with the community and view results</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <LoadMorePolls :context="context" :params="params"/>
    </div>
</template>

<script lang="ts" setup>
import { onMounted, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import LoadMorePolls from './LoadMorePolls.vue';
import PollList from './PollList.vue';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';
import { DocumentTextIcon } from '@heroicons/vue/24/outline';

const props = withDefaults(defineProps<{
    context?: string
    params: {}
}>(), {
    context: 'browse',
});

const page = usePage();
const userId = computed(() => page.props.auth?.user?.id);

let pollStore = usePollStore();
let { publicPoll, loadingMore } = storeToRefs(pollStore);

const loadData = (forceReload = false) => {
    const hasExistingData = publicPoll.value[0]?.[props.context]?.polls?.length > 0;
    
    // Load if no data exists or if force reload is requested
    if (!hasExistingData || forceReload) {
        loadingMore.value = true;
        pollStore.loadPublicPolls(props.context, props.params).then();
    }
};

// Initial load
loadData();

onMounted(() => {
    pollStore.setContext(props.context);
});

// Watch for context changes
watch(() => props.context, (newContext, oldContext) => {
    pollStore.setContext(newContext);
    // Always reload when switching contexts
    if (newContext !== oldContext) {
        loadingMore.value = true;
        pollStore.loadPublicPolls(newContext, props.params).then();
    }
});

// Watch for user changes - reload data when user switches
watch(userId, (newUserId, oldUserId) => {
    if (newUserId !== oldUserId) {
        // User changed, force reload of data for this context
        loadingMore.value = true;
        pollStore.reloadContext(props.context, props.params);
    }
});
</script>
