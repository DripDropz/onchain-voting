<template>
    <div class="flex flex-col justify-between h-full">
        <div>
            <div class="flex flex-row items-start justify-between">
                <div class="flex flex-col w-64 gap-4 pb-4 border-white divide-y-4 divide-white border-y-4" v-if="size === 'full'">
                    <div class="flex flex-row items-center justify-between pt-4 text-sm font-semibold text-white">
                        <div class="text-gray-300">Starts</div>
                        <div class="text-md">{{ ballot.started_at }}</div>
                    </div>
                    <div class="flex flex-row items-center justify-between pt-4 text-sm font-semibold text-white">
                        <div class="text-gray-300">Ends</div>
                        <div class="text-md">{{ ballot.ended_at }}</div>
                    </div>

<!--                    <div class="pt-4 text-sm font-semibold text-gray-300">Start</div>-->
<!--                    <div class="pt-4 text-sm font-semibold text-gray-300">End</div>-->
                </div>
                <Link :href="route('ballot.view', {ballot: ballot.hash})"
                      class="rounded-full bg-indigo-100 px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm flex flex-row gap-2 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 ml-auto">
                    <BallotStatusBadge :ballot="ballot"></BallotStatusBadge>
                    <span>
                        {{ballot?.live ? 'Go Vote' : 'View'}}
                    </span>
                </Link>
            </div>
        </div>
        <div :class="{
                'pt-16': size === 'full'
             }">
            <p class="text-gray-300">Ballot</p>
            <h3 class="flex flex-row items-center gap-2 title3 font-display">
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
import {withDefaults} from 'vue';

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
