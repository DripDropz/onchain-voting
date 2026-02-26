<template >
    <div class="grid gap-4 mt-6 sm:grid-cols-1 lg:grid-cols-2">
        <template v-for="poll in polls" :key="poll.hash">
            <PollListItem :poll="poll"/>
        </template>
    </div>
</template>

<script lang="ts" setup>
import PollData = App.DataTransferObjects.PollData;
import PollListItem from './PollListItem.vue';
withDefaults(defineProps<{
    polls: PollData[]
}>(), {
});

</script>
