<template>
    <div class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
            <div class="col-span-1 lg:col-span-3 h-80 lg:h-full bg-indigo-600 px-4 py-6 lg:px-5 lg:py-7 xl:px-8 xl:py-9 rounded-lg">
                <BallotCard :ballot="ballots[0]" />
            </div>

            <div class="col-span-1 lg:col-span-4 flex flex-col gap-6" v-if="ballots.length > 1">
                <div class="w-full h-64 md:h-72 bg-indigo-600 rounded-lg px-4 py-6 lg:px-5 lg:py-7 xl:px-8 xl:py-9 ">
                    <BallotCard :ballot="ballots[1]" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="w-full h-44 bg-indigo-600 rounded-lg px-4 py-6 lg:px-5 lg:py-7 xl:px-8 xl:py-9 " v-if="ballots.length > 2">
                        <BallotCard :ballot="ballots[2]" size="mini" />
                    </div>
                    <div class="w-full h-44 bg-indigo-600 rounded-lg px-4 py-6 lg:px-5 lg:py-7 xl:px-8 xl:py-9 " v-if="ballots.length > 3">

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import {Head, Link} from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import BallotCard from "@/Pages/Ballot/Partisals/BallotCard.vue";

defineProps<{
    ballots: BallotData[];
}>();
</script>
