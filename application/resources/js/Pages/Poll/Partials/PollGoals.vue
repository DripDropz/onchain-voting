<template>
    <div class="space-y-3">
        <div v-if="poll.status === 'draft'" class="text-sm text-gray-400 italic">
            Complete your poll details and submit for review to set participation criteria.
        </div>
        <div v-else-if="poll.status === 'pending'" class="text-sm text-gray-400 italic">
            Admin will review your poll and approve it for publication.
        </div>
        <div v-else-if="poll.status === 'approved'" class="text-sm text-gray-400 italic">
            Your poll is approved. Publish it to start collecting votes.
        </div>
        <div v-else-if="poll.status === 'rejected'" class="text-sm text-red-400 italic">
            Your poll was rejected. Edit and resubmit for review.
        </div>
        <div v-else-if="poll.status === 'published'" class="text-sm text-gray-400 italic">
            Your poll is live and receiving votes. Configure voting criteria to restrict participation.
        </div>
        <div v-else class="text-sm text-gray-400 italic">
            Configure voting criteria and manage your poll settings.
        </div>
    </div>
</template>

<script lang="ts" setup>
import PollData = App.DataTransferObjects.PollData;

const props = defineProps<{
    poll: PollData;
}>();
</script>
