<template>
    <VoterLayout :page="`${ballot.title} Ballot`">
        <div class="container flex flex-col justify-between h-full mt-16">

            <div class="bg-indigo-600 text-white flex flex-col gap-10 rounded-lg py-10 px-8">
                <div>
                    <div class="flex flex-row gap-5 items-center">
                        <BallotStatusBadge :ballot="ballot"></BallotStatusBadge>
                        <div class="text-sm font-semibold text-white flex flex-row gap-2 justify-between items-center">
                            <div class="text-gray-300">Starts</div>
                            <div class="text-md">{{ ballot.started_at }}</div>
                        </div>
                        <div class="text-sm font-semibold text-white gap-2 flex flex-row justify-between items-center">
                            <div class="text-gray-300">Ends</div>
                            <div class="text-md">{{ ballot.ended_at }}</div>
                        </div>
                    </div>
                    <h1 class="title2 font-display flex flex-row gap-2 items-center">
                        <span>{{ ballot.title }}</span>
                        <Line></Line>
                    </h1>
                    <div class="mt-3">
                        {{ballot.description}}
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="border-4 border-white rounded-lg px-4 py-5 xl:px-6 xl:py-8">
                        <div v-for="question in ballot.questions" :key="question.hash">
                            <h3 class="title3 mb-4 xl:mb-6">{{ question.title }}</h3>
                            <ul class="flex flex-col gap-3">
                                <li class="border-2 border-white p-2 xl:p-3 rounded-full" v-for="choice in question.choices" :key="choice.hash">
                                    {{ choice.title }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="title2 items-center"> Live Metrics here</div>
                </div>
            </div>
        </div>
    </VoterLayout>
</template>
<script lang="ts" setup>
import {defineProps} from "vue";
import BallotData = App.DataTransferObjects.BallotData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import Line from "@/Pages/Partials/Line.vue";
import BallotStatusBadge from "@/Pages/Auth/Ballot/Partials/BallotStatusBadge.vue";

const props = defineProps<{
    ballot: BallotData;
}>();

</script>
