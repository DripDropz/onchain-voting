<template >
    <div class="grid grid-cols-2 gap-8 py-8">
        <div v-for="poll in polls" :key="poll.hash">
            <PollListItem :poll="poll"/>
        </div>
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
