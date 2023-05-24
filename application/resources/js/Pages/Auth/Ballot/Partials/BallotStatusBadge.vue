<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import {computed} from "vue";

const props = defineProps<{
    ballot: BallotData;
}>();

let status = computed(() => {
    switch (props.ballot.status) {
        case 'published':
            return props.ballot?.live ? 'Live' : 'Published';
        default:
            return props.ballot.status;
    }
});

let theme = computed(() => {
    switch (props.ballot.status) {
        case 'published':
            return props.ballot.live ? 'emerald' : 'indigo';
        default:
            return 'gray';
    }
});
</script>
<template>
    <span class="flex items-center gap-x-1.5">
        <span class="flex-none rounded-full p-1" :class="[`bg-${theme}-500/20`]">
            <div class="h-1.5 w-1.5 rounded-full" :class="[`bg-${theme}-500`]" />
        </span>
        <span class="text-xs leading-5 text-gray-300 capitalize">
            {{ status }}
        </span>
    </span>
</template>
