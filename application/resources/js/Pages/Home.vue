<script setup lang="ts">
import {ArrowLongDownIcon} from '@heroicons/vue/20/solid'
import OpenBallots from "@/Pages/Partials/OpenBallots.vue";
import BallotData = App.DataTransferObjects.BallotData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import Line from "@/Pages/Partials/Line.vue";
import BallotSingle from "@/Pages/Ballot/Partials/BallotSingle.vue";

defineProps<{
    canLogin?: boolean;
    ballots: BallotData[];
}>();
</script>

<template>
    <VoterLayout page="Home">
        <div class="flex flex-col gap-24">
            <div class="container h-screen flex flex-col justify-end -mt-32">
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <h1 class="font-display text-4xl font-medium tracking-tight text-slate-900 dark:text-slate-200 sm:text-6xl xl:text-7xl mt-auto -mt-16">
                                    <span class="block text-4xl sm:text-7xl xl:text-8xl">
                                        On-Chain Voting
                                    </span>

                                    <span class="relative whitespace-nowrap text-indigo-600">
                                        <svg
                                            aria-hidden="true" viewBox="0 0 418 42"
                                            class="absolute left-0 top-2/3 h-[0.58em] w-full fill-indigo-300/70"
                                            preserveAspectRatio="none">
                                            <path
                                                d="M203.371.916c-26.013-2.078-76.686 1.963-124.73 9.946L67.3 12.749C35.421 18.062 18.2 21.766 6.004 25.934 1.244 27.561.828 27.778.874 28.61c.07 1.214.828 1.121 9.595-1.176 9.072-2.377 17.15-3.92 39.246-7.496C123.565 7.986 157.869 4.492 195.942 5.046c7.461.108 19.25 1.696 19.17 2.582-.107 1.183-7.874 4.31-25.75 10.366-21.992 7.45-35.43 12.534-36.701 13.884-2.173 2.308-.202 4.407 4.442 4.734 2.654.187 3.263.157 15.593-.78 35.401-2.686 57.944-3.488 88.365-3.143 46.327.526 75.721 2.23 130.788 7.584 19.787 1.924 20.814 1.98 24.557 1.332l.066-.011c1.201-.203 1.53-1.825.399-2.335-2.911-1.31-4.893-1.604-22.048-3.261-57.509-5.556-87.871-7.36-132.059-7.842-23.239-.254-33.617-.116-50.627.674-11.629.54-42.371 2.494-46.696 2.967-2.359.259 8.133-3.625 26.504-9.81 23.239-7.825 27.934-10.149 28.304-14.005.417-4.348-3.529-6-16.878-7.066Z"/>
                                        </svg>
                                        <span class="relative">made simple</span>
                                    </span>
                            for everyone.
                        </h1>
                        <div
                            class="mt-10 lg:max-w-2xl text-lg xl:text-xl tracking-tight text-slate-700 dark:text-slate-300">
                            <p>
                                Voting should be easy, secure, and accessible to everyone. With chainvote, you
                                can
                                vote on-chain with a simple transaction.
                            </p>
                        </div>
                    </div>
                    <div>
                        <a href="#open-ballots"  v-if="ballots?.length > 0"
                                class="inline-flex items-center gap-x-2 rounded-md bg-indigo-600 px-3.5 py-2.5 xl:px-5 xl:py-4 text-sm lg:text-lg xl:text-xl 2xl:text-2xl font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Open Ballots
                            <ArrowLongDownIcon class="-mr-0.5 h-5 w-5" aria-hidden="true"/>
                        </a>
                    </div>
                </div>
            </div>

            <div class="py-24 bg-slate-200 dark:bg-slate-800/20"  v-if="ballots?.length > 0" id="open-ballots">
                <div class="container" v-if="ballots?.length > 1">
                    <h2 class="title2 font-display mb-6 lg:mb-8">
                        <span class="flex">Open Ballots</span>
                        <Line></Line>
                    </h2>
                </div>

                <div class="container">
                    <OpenBallots v-if="ballots.length > 1" :ballots="ballots"></OpenBallots>
                    <BallotSingle v-else :ballot="ballots[0]"></BallotSingle>
                </div>
            </div>
        </div>
    </VoterLayout>
</template>


