import {defineStore} from 'pinia';
import {computed, ref} from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import RuleData = App.DataTransferObjects.RuleData;
import SignatureData = App.DataTransferObjects.SignatureData;
import axios from 'axios';

export const usePetitionSignatureStore = defineStore('petition-signature', () => {
    let petition = ref<PetitionData>(null);
    let signature = ref<SignatureData | null>(null);

    function setPetition(petitionData: PetitionData) {
        petition.value = petitionData;
    }

    function setSignature(signatureData: SignatureData | null) {
        signature.value = signatureData;
    }

    async function reloadPetitionData(petitionHash:string, stakeAddress?: string) {
        const query: Record<string, string> = {};
        if (stakeAddress) {
            query.stakeAddress = stakeAddress;
        }

        const res = await axios.get(route('petitions.petitionData', { petition: petitionHash }), {
            params: query,
        });

        if (res.status == 200) {
            if (res.data?.petition) {
                petition.value = res.data.petition;
                signature.value = res.data.signature ?? null;
                return;
            }

            petition.value = res.data;
        }
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
        signature$: signature,
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
        setSignature,
        reloadPetitionData,
    }
});


