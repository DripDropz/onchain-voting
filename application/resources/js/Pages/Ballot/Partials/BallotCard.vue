<template>
    <div class="flex flex-col justify-between h-full">
        <div>
            <div class="flex flex-row justify-between items-start">
                <div class="flex flex-col gap-4 divide-y-4 divide-white border-y-4 border-white w-64 pb-4" v-if="size === 'full'">
                    <div class="text-sm font-semibold text-white pt-4 flex flex-row justify-between items-center">
                        <div class="text-gray-300">Starts</div>
                        <div class="text-md">{{ ballot.started_at }}</div>
                    </div>
                    <div class="text-sm font-semibold text-white pt-4 flex flex-row justify-between items-center">
                        <div class="text-gray-300">Ends</div>
                        <div class="text-md">{{ ballot.ended_at }}</div>
                    </div>

<!--                    <div class="text-sm font-semibold text-gray-300 pt-4">Start</div>-->
<!--                    <div class="text-sm font-semibold text-gray-300 pt-4">End</div>-->
                </div>
                <Link :href="route('ballots.view', {ballot: ballot.hash})"
                      class="rounded-full bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm flex flex-row gap-2 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 ml-auto">
                    <BallotStatusBadge :ballot="ballot"></BallotStatusBadge>
                    <span>
                        {{ballot.live ? 'Go Vote' : 'View'}}
                    </span>
                </Link>
            </div>
        </div>
        <div :class="{
                'pt-16': size === 'full'
             }">
            <p class="text-gray-300">Ballot</p>
            <h3 class="title3 font-display flex flex-row gap-2 items-center">
                <span>{{ballot.title}}</span>
                <Line></Line>
            </h3>
        </div>
    </div>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import Line from "@/Pages/Partials/Line.vue";
import BallotStatusBadge from "@/Pages/Auth/Ballot/Partials/BallotStatusBadge.vue";
import {Link} from "@inertiajs/vue3";

const props = withDefaults(
    defineProps<{
        ballot?: BallotData,
        size?: 'full' | 'drip' | 'mini'
    }>(),
    {
        size: 'full',
    }
);
</script>
