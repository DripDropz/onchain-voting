<template>
    <div class="flex flex-col bg-white rounded-lg dark:bg-gray-900 ">
        <div class="flex flex-col items-center w-full h-full">
            <div :class="{
                    'relative float-left m-4 text-center': (neededSupportesNextGoal > 0),
                    'flex flex-col items-center justify-center gap-4 float-left m-4 text-center h-full': !(neededSupportesNextGoal > 0),
                }">
                <div class="w-300">
                    <PetitionSupportesDoughnut v-if="neededSupportesNextGoal > 0" :chart-data="chartData" :chart-options="chartOptions" />
                </div>
                <div :class="{
                        'absolute  flex flex-col bottom-24 text-center w-full': (neededSupportesNextGoal > 0),
                        'flex flex-col text-center w-full': !(neededSupportesNextGoal > 0)
                    }">
                    <span class="text-xl font-bold leading-tight xl:text-2xl">{{ petition.signatures_count }}</span>
                    <span> supporters</span>
                </div>
                <div v-if="neededSupportesNextGoal == null" class="flex flex-col items-center w-full">
                    <span>waiting for admin to set next petition goals</span>
                </div>
                <div v-if="neededSupportesNextGoal > 0" class="flex flex-col items-center w-full">
                    <span>Only <span class="font-bold">{{ neededSupportesNextGoal }} more</span> supporters to your next goal.</span>
                </div>
                <div v-if="neededSupportesNextGoal == 0" class="flex flex-col items-center w-full">
                    <span>All petition goals achieved</span>
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
import { useConfigStore } from '@/stores/config-store';
import { storeToRefs } from 'pinia';

const props = defineProps<{
    petition: PetitionData;
}>();

const visible = props.petition?.petition_goals?.visible as RuleData;
const featurePetition = props.petition?.petition_goals?.['feature-petition'] as RuleData;
const ballotEligible = props.petition?.petition_goals?.['ballot-eligible'] as RuleData;

let configStore = useConfigStore();
let {isDarkMode} = storeToRefs(configStore);

let nextGoalPieSectionColor = computed(() => {
    return isDarkMode.value ? '#374151' : "#e5e7eb";
})

let neededSupportesNextGoal = computed(() => {
    if ((Number(visible?.value2) - props.petition?.signatures_count) > 0) {
        return Number(visible?.value2) - props.petition?.signatures_count;
    } else if((Number(featurePetition?.value2) - props.petition?.signatures_count) > 0) {
        return Number(featurePetition?.value2) - props.petition?.signatures_count;
    } else if((Number(ballotEligible?.value2) - props.petition?.signatures_count) > 0) {
        return Number(ballotEligible?.value2) - props.petition?.signatures_count;
    }else if((props.petition?.signatures_count - Number(ballotEligible?.value2)) >= 0) {
        return 0;
    } else {
        return null;
    }
})

let chartData = computed(() => {
    const signatureCount = Number(props.petition.signatures_count);
    const currentGoalNo = neededSupportesNextGoal.value;

    return {
        labels: ['signatures', 'pending to next goal'],
        datasets: [{
            data: [signatureCount, currentGoalNo],
            backgroundColor: ["#50abcb", nextGoalPieSectionColor.value]
        }],
    }
})

let chartOptions = {
    rotation: -90,
    circumference: 180,
    cutout: '90%',
    borderWidth: 0,
}


</script>