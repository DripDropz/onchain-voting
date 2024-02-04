<template>
    <div class="flex flex-col bg-white rounded-lg dark:bg-gray-900 ">
        <div class="flex flex-col items-center w-full">
            <div class="relative float-left m-4 text-center">
                <div class="w-300">
                    <PetitionSupportesDoughnut :chart-data="chartData" :chart-options="chartOptions" />
                </div>
                <div class="absolute  flex flex-col bottom-24 text-center w-full">
                    <span class="text-xl font-bold leading-tight xl:text-2xl">{{ petition.signatures_count }}</span>
                    <span> supporters</span>
                </div>
                <div v-if="neededSupportesNextGoal" class="flex flex-col items-center w-full">
                    <span>Only <span class="font-bold">{{ neededSupportesNextGoal }} more</span> supporters to your next goal.</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import RuleData = App.DataTransferObjects.RuleData;
import PetitionSupportesDoughnut from './PetitionSupportesDoughnut.vue';

const props = defineProps<{
    petition: PetitionData;
}>();

const visible = props.petition?.petition_goals?.visible as RuleData;
const featurePetition = props.petition?.petition_goals?.['feature-petition'] as RuleData;
const ballotEligible = props.petition?.petition_goals?.['ballot-eligible'] as RuleData;

let chartData = computed(() => {
    const visibleNo = Number(visible?.value2);
    const featurePetitionNo = Number(featurePetition?.value2);
    const ballotEligibleNo = Number(ballotEligible?.value2)

    return {
        labels: ['visible', 'feature-petition', 'ballot-eligible'],
        datasets: [{
            data: [visibleNo, featurePetitionNo, ballotEligibleNo],
            backgroundColor: ["#d3eaf2", "#66b5d1", "#165a72"],
        }],
    }
})

let chartOptions = {
    rotation: -90,
    circumference: 180,
    cutout: '90%'
}

let neededSupportesNextGoal = computed(() => {
    if ((Number(visible?.value2) - props.petition?.signatures_count) > 0) {
        return Number(visible?.value2) - props.petition?.signatures_count;
    } else if((Number(featurePetition?.value2) - props.petition?.signatures_count) > 0) {
        return Number(featurePetition?.value2) - props.petition?.signatures_count;
    } else if((Number(ballotEligible?.value2) - props.petition?.signatures_count) > 0) {
        return Number(ballotEligible?.value2) - props.petition?.signatures_count;
    } else {
        return 0;
    }
})
</script>