<script setup lang="ts">
import BallotData = App.DataTransferObjects.BallotData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import voteSplashImg from '../../images/vote-splash.jpeg';
import {Link} from "@inertiajs/vue3";

const props = defineProps<{
    ballots: BallotData[];
}>();
</script>

<template>
    <VoterLayout page="Home">
        <div class="self-stretch place-items-stretch w-full">
            <div class="flex flex-col h-full w-full justify-center md:flex-row md:items-center">
                <div class="flex flex-col justify-center h-full gap-4 p-8 lg:p-16 items-center lg:w-1/2 ">
                    <div class="flex flex-col gap-y-8">
                        <div
                            class="text-4xl font-medium tracking-tight font-display text-slate-900 dark:text-slate-200 sm:text-6xl xl:text-7xl">
                            <span class="block text-4xl sm:text-7xl xl:text-8xl">
                                On-Chain Voting
                            </span>

                            <span class="relative text-sky-400 whitespace-nowrap">
                                <svg aria-hidden="true" viewBox="0 0 418 42"
                                    class="absolute left-0 top-2/3 h-[0.58em] w-full fill-blue-300/40"
                                    preserveAspectRatio="none">
                                    <path
                                        d="M203.371.916c-26.013-2.078-76.686 1.963-124.73 9.946L67.3 12.749C35.421 18.062 18.2 21.766 6.004 25.934 1.244 27.561.828 27.778.874 28.61c.07 1.214.828 1.121 9.595-1.176 9.072-2.377 17.15-3.92 39.246-7.496C123.565 7.986 157.869 4.492 195.942 5.046c7.461.108 19.25 1.696 19.17 2.582-.107 1.183-7.874 4.31-25.75 10.366-21.992 7.45-35.43 12.534-36.701 13.884-2.173 2.308-.202 4.407 4.442 4.734 2.654.187 3.263.157 15.593-.78 35.401-2.686 57.944-3.488 88.365-3.143 46.327.526 75.721 2.23 130.788 7.584 19.787 1.924 20.814 1.98 24.557 1.332l.066-.011c1.201-.203 1.53-1.825.399-2.335-2.911-1.31-4.893-1.604-22.048-3.261-57.509-5.556-87.871-7.36-132.059-7.842-23.239-.254-33.617-.116-50.627.674-11.629.54-42.371 2.494-46.696 2.967-2.359.259 8.133-3.625 26.504-9.81 23.239-7.825 27.934-10.149 28.304-14.005.417-4.348-3.529-6-16.878-7.066Z" />
                                </svg>
                                <span class="relative">made simple</span>
                            </span>
                            for everyone.
                        </div>
                        <div
                            class="text-lg tracking-tight lg:max-w-2xl xl:text-xl text-slate-700 dark:text-slate-300">
                            <p>
                                Easy, secure, and universally accessible on-chain
                                voting with a simple transaction.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start w-full gap-6">
                        <Link :href="route('ballots.index')"
                            class="inline-flex items-center gap-x-2 rounded-md bg-white px-2 py-2.5  font-semibold text-sky-400 shadow-sm hover:bg-sky-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 border border-sky-400">
                            Open Ballots
                        </Link>
                        <a href="#"
                            class="inline-flex items-center gap-x-2 rounded-md bg-sky-400 px-2 py-2.5  font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 ">
                            Start a petition
                        </a>
                    </div>
                </div>
                <div class="lg:w-1/2 h-full bg-center"
                     :style="{ backgroundImage: 'url(' + props.voteSplashImg + ')' }">
                </div>
            </div>

<!--            <div class="py-24 bg-sky-100 dark:bg-slate-800/20" v-if="ballots?.length > 0" id="open-ballots">-->
<!--                <div class="container" v-if="ballots?.length > 1">-->
<!--                    <h2 class="mb-6 title2 font-display lg:mb-10">-->
<!--                        <span class="flex">Open Ballots</span>-->
<!--                        <Line></Line>-->
<!--                    </h2>-->
<!--                </div>-->

<!--                <div class="container">-->
<!--                    <OpenBallots v-if="ballots.length > 3" :ballots="ballots"></OpenBallots>-->

<!--                    <div class="flex flex-col gap-16" v-else>-->
<!--                        <BallotSingle v-for="ballot in ballots" :key="ballot.hash" :ballot="ballot" context="list">-->
<!--                        </BallotSingle>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </VoterLayout>
</template>


