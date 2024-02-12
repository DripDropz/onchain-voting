import {defineStore} from 'pinia';
import {computed, ref} from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import RuleData = App.DataTransferObjects.RuleData;

export const usePetitionSignatureStore = defineStore('petition-signature', () => {
    let petition = ref<PetitionData>(null);

    function setPetition(petitionData: PetitionData) {
        petition.value = petitionData;
    }
    
    const visible = computed(() => petition.value?.petition_goals?.visible as RuleData);
    const featurePetition = computed(() => petition.value?.petition_goals?.['feature-petition'] as RuleData);
    const ballotEligible = computed(() => petition.value?.petition_goals?.['ballot-eligible'] as RuleData);

    let neededSupportesNextGoal = computed(() => {
        if ((Number(visible?.value?.value2) - petition.value?.signatures_count) > 0) {
            return Number(visible?.value?.value2) - petition.value?.signatures_count;
        } else if((Number(featurePetition?.value?.value2) - petition.value?.signatures_count) > 0) {
            return Number(featurePetition?.value?.value2) - petition.value?.signatures_count;
        } else if((Number(ballotEligible?.value?.value2) - petition.value?.signatures_count) > 0) {
            return Number(ballotEligible?.value?.value2) - petition.value?.signatures_count;
        }else if((petition.value?.signatures_count - Number(ballotEligible?.value?.value2)) >= 0) {
            return 0;
        } else {
            return null;
        }
    })

    let lackingNextGoal = computed(() => {
        return (neededSupportesNextGoal.value) == null ? true : false;
    })

    let allGoalsAchieved = computed(() => {
        return (neededSupportesNextGoal.value == 0) ? true : false;
    })

    let hasNextGoal = computed(() => {
        return (neededSupportesNextGoal.value > 0) ? true : false;
    })

    let nextGoal = computed(() => {
        if (hasNextGoal.value) {
            return petition.value.signatures_count + neededSupportesNextGoal.value;
        } else {
            return 0;
        }
    })

    let currentGoalPercetage = computed(() => {
        if (hasNextGoal.value) {
            let goalPecentage = (petition.value.signatures_count / nextGoal.value) * 100;

            return Math.floor(goalPecentage); 
        }
    })

    return {
        petition$: petition,
        visible$: visible,
        featurePetition$: featurePetition,
        ballotEligible$: ballotEligible,
        neededSupportesNextGoal$: neededSupportesNextGoal,
        lackingNextGoal$: lackingNextGoal,
        allGoalsAchieved$: allGoalsAchieved,
        hasNextGoal$: hasNextGoal,
        currentGoalPercetage$: currentGoalPercetage,
        nextGoal$: nextGoal,
        setPetition,
    }
});


