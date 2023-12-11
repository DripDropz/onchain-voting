<template>
    <a :preserve-scroll="true" @click.prevent="registerUser()"
        class="absolute top-0 left-0 flex items-center justify-center w-full h-full p-8 text-sky-700 rounded-lg bg-sky-800/50">
        <span class="px-4 py-2 text-lg font-bold bg-sky-100 rounded-full xl:text-text">
            Register to Vote
        </span>
    </a>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import { router } from '@inertiajs/vue3';
import AlertService from '@/shared/Services/alert-service';


const props = defineProps<{
    pageData?:any,
    ballot: BallotData
    hasVotingPower?: boolean
}>()

function registerUser()
{
    console.log(props.hasVotingPower);
    if (props.hasVotingPower) {
        router.get(route('ballot.register.view', {ballot: props.ballot.hash}));
    } else {
        AlertService.show(["Voting power needed to register"], 'info');
    }
}
</script>
