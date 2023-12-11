<template>
    <VoterLayout :page="`${ballot.title} Ballot`" :page-data="ballot">
        <div class="container flex flex-col justify-between py-16">
           <BallotSingle :ballot="ballot"></BallotSingle>
        </div>
    </VoterLayout>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import BallotSingle from "@/Pages/Ballot/Partials/BallotSingle.vue";

defineProps<{
    ballot: BallotData;
}>();
</script>
