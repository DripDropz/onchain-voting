<template>
    <div class="w-full">
    <div class="h-4 w-full bg-gray-200 dark:bg-gray-700">
        <div v-if="allGoalsAchieved$ || hasNextGoal$" class="h-4 bg-sky-400" :style="{ 'width': currentGoalPercetage$ + '%' }"></div>
    </div>
    </div>
    <div class="flex flex-row items-center justify-between my-4">
        <div class="items-start">
            <p class="text-2xl font-bold">{{ petition$.signatures_count }}</p>
            <p class="text-xl">Signatures</p>
        </div>
        <div v-if="allGoalsAchieved$" class="text-end">
            <p class="text-2xl font-bold">100%</p>
            <p class="text-xl">All Goals Achieved</p>
        </div>
        <div v-else class="text-end">
            <p v-if="hasNextGoal$" class="text-2xl font-bold">{{ nextGoal$ }}</p>
            <p v-if="lackingNextGoal$" class="text-2xl font-bold">-</p>
            <p class="text-xl">Next Goal</p>
        </div>
      </div>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import PetitionData = App.DataTransferObjects.PetitionData;
import {usePetitionSignatureStore} from '@/Pages/Petition/stores/petition-signature-store';

const props = defineProps<{
    petition: PetitionData;
}>();

let petitionSignatureStore = usePetitionSignatureStore();
petitionSignatureStore.setPetition(props.petition);
let {  petition$, currentGoalPercetage$, allGoalsAchieved$, nextGoal$, hasNextGoal$, lackingNextGoal$ } = storeToRefs(petitionSignatureStore);  
</script>