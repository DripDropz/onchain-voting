<template>
    <div class="flex flex-col items-center justify-center py-8">
        <div class="text-center">
            <span class="text-4xl font-bold text-white">{{ poll.responses_count ?? 0 }}</span>
            <span class="text-sm text-gray-400 block mt-1">votes cast</span>
        </div>
        <div v-if="totalChoices > 0" class="mt-4 text-center">
            <span class="text-sm text-gray-500">{{ totalChoices }} options</span>
        </div>
        <div v-if="poll.status === 'published' && daysLive > 0" class="mt-2 text-center">
            <span class="text-sm text-gray-500">{{ daysLive }} days live</span>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import PollData = App.DataTransferObjects.PollData;

const props = defineProps<{
    poll: PollData;
}>();

const totalChoices = computed(() => props.poll.question?.choices?.length ?? 0);

const daysLive = computed(() => {
    const from = props.poll.started_at ?? props.poll.created_at;
    if (!from) return 0;
    return Math.max(0, Math.floor((Date.now() - new Date(from).getTime()) / 86400000));
});
</script>
