<template>
    <div class="flex flex-col bg-white rounded-lg dark:bg-gray-900 ">
        <div class="flex flex-col items-center w-full h-full">
<!--            <div :class="{-->
<!--                    'relative float-left m-4 text-center': hasNextGoal$,-->
<!--                    'flex flex-col items-center justify-center gap-4 float-left m-4 text-center h-full': !hasNextGoal$,-->
<!--                }">-->
<!--                <div class="w-300">-->
<!--                    <PetitionSupportesDoughnut v-if="hasNextGoal$" :chart-data="chartData" :chart-options="chartOptions" />-->
<!--                </div>-->
<!--                <div :class="{-->
<!--                        'absolute  flex flex-col bottom-24 text-center w-full': hasNextGoal$,-->
<!--                        'flex flex-col text-center w-full': !hasNextGoal$-->
<!--                    }">-->
<!--                    <span class="text-xl font-bold leading-tight xl:text-2xl">{{ petition$.signatures_count }}</span>-->
<!--                    <span> supporters</span>-->
<!--                </div>-->
<!--                <div v-if="lackingNextGoal$" class="flex flex-col items-center w-full">-->
<!--                    <span>waiting for admin to set next petition goals</span>-->
<!--                </div>-->
<!--                <div v-if="allGoalsAchieved$" class="flex flex-col items-center w-full">-->
<!--                    <span>All petition goals achieved</span>-->
<!--                </div>-->
<!--                <div v-if="hasNextGoal$" class="flex flex-col items-center w-full">-->
<!--                    <span>Only <span class="font-bold">{{ neededSupportesNextGoal$ }} more</span> supporters to your next goal.</span>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import PetitionSupportesDoughnut from './PetitionSupportesDoughnut.vue';
import { useConfigStore } from '@/stores/config-store';
import { storeToRefs } from 'pinia';
// import {usePetitionSignatureStore} from '@/Pages/Petition/stores/petition-signature-store';

const props = defineProps<{
    petition: PetitionData;
}>();

let configStore = useConfigStore();
let {isDarkMode} = storeToRefs(configStore);

// let petitionSignatureStore = usePetitionSignatureStore();
// petitionSignatureStore.setPetition(props.petition);
// let { petition$, neededSupportesNextGoal$, lackingNextGoal$, allGoalsAchieved$, hasNextGoal$} = storeToRefs(petitionSignatureStore);

let nextGoalPieSectionColor = computed(() => {
    return isDarkMode.value ? '#374151' : "#e5e7eb";
})

// let chartData = computed(() => {
//     const signatureCount = Number(petition$.value?.signatures_count);
//     const currentGoalNo = neededSupportesNextGoal$.value;
//
//     return {
//         labels: ['signatures', 'pending to next goal'],
//         datasets: [{
//             data: [signatureCount, currentGoalNo],
//             backgroundColor: ["#50abcb", nextGoalPieSectionColor.value]
//         }],
//     }
// })

let chartOptions = {
    rotation: -90,
    circumference: 180,
    cutout: '90%',
    borderWidth: 0,
}


</script>
