<template>
    <div class="flex flex-col gap-4 mt-6">
        <PollListItem v-for="poll in polls" :key="poll.hash" :poll="poll"/>
    </div>
</template>

<script setup lang="ts">
import PollData = App.DataTransferObjects.PollData;
import PollListItem from "@/Pages/Auth/Poll/Partials/PollListItem.vue";

const props = defineProps<{
    polls: PollData[];
}>();
</script>
