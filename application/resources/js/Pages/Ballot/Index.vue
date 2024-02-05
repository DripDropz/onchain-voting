<template>
    <VoterLayout page="Ballots"
                 :crumbs="crumbs">
        <div class="container flex flex-col justify-between py-16">
            <div class="" v-if="props.ballots?.length > 0" id="open-ballots">
                <div class="container" v-if="props.ballots?.length > 1">
                    <h2 class="mb-6 title2 text-sky-500 dark:text-white font-display lg:mb-10">
                        <span class="flex">Open Ballots</span>
                        <Line></Line>
                    </h2>
                </div>

                <div class="container">
                    <OpenBallots :ballots="props.ballots"></OpenBallots>

                    <!--                    <div class="flex flex-col gap-16" v-else>-->
                    <!--                        <BallotSingle v-for="ballot in ballots" :key="ballot.hash" :ballot="ballot" context="list">-->
                    <!--                        </BallotSingle>-->
                    <!--                    </div>-->
                </div>
            </div>
        </div>
    </VoterLayout>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import BallotSingle from "@/Pages/Ballot/Partials/BallotSingle.vue";
import OpenBallots from "@/Pages/Partials/OpenBallots.vue";
import Line from "@/Pages/Partials/Line.vue";

const props = defineProps<{
    ballots: BallotData[];
    crumbs: [];
    actions?: []
}>();
</script>
