<template>
<Link :preserve-scroll="true"  :href="route('ballot.register.view', {ballot: ballot.hash})"
    class="absolute top-0 left-0 flex items-center justify-center w-full h-full p-8 text-indigo-700 rounded-lg bg-indigo-800/50">
    <span class="px-4 py-2 text-lg font-bold bg-indigo-100 rounded-full xl:text-text">
        Register to Vote
    </span>
</Link>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import { Link } from '@inertiajs/vue3';


defineProps<{
    pageData?:any,
    ballot: BallotData
}>()

</script>
