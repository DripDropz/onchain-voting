<template>
    <VoterLayout :page="`${ballot.title} Ballot`">
        <div class="container flex flex-col justify-between h-full mt-16">

           <BallotSingle :ballot="ballot"></BallotSingle>
        </div>
    </VoterLayout>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import Line from "@/Pages/Partials/Line.vue";
import BallotStatusBadge from "@/Pages/Auth/Ballot/Partials/BallotStatusBadge.vue";
import BallotSingle from "@/Pages/Ballot/Partials/BallotSingle.vue";

const props = defineProps<{
    ballot: BallotData;
}>();

</script>
