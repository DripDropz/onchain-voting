<template>
    <VoterLayout page="Ballots"
                 :crumbs="crumbs">
        <div class="container flex flex-col justify-between py-16">
            <div class="" v-if="props.ballots?.length > 0" id="open-ballots">
                <h2 class="mb-6 title2 text-sky-500 dark:text-white font-display lg:mb-10">
                    <span class="flex">Open Ballots</span>
                    <Line></Line>
                </h2>

                <div class="container">
                    <OpenBallots :ballots="props.ballots"></OpenBallots>

                    <!--                    <div class="flex flex-col gap-16" v-else>-->
                    <!--                        <BallotSingle v-for="ballot in ballots" :key="ballot.hash" :ballot="ballot" context="list">-->
                    <!--                        </BallotSingle>-->
                    <!--                    </div>-->
                </div>
            </div>
            <div v-else>
                <div class="flex flex-col gap-4 justify-center items-center text-center">
                    <div
                        class="text-4xl font-medium tracking-tight font-display text-slate-900 dark:text-slate-200 sm:text-6xl xl:text-7xl">
                        <span class="block text-4xl sm:text-7xl xl:text-8xl">
                            Ballots Cooking
                        </span>
                    </div>
                    <div
                        class="text-lg tracking-tight lg:max-w-2xl xl:text-xl 2xl:text-2xl text-slate-700 dark:text-slate-300">
                        <p>
                            Admins are still cooking up ballots. Check back soon.
                        </p>
                    </div>
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
