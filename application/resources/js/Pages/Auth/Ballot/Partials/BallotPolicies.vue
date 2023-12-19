<template>
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Ballot Policies
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add registration and voting cardano policie scripts.
            </p>
        </div>

        <div class="flex flex-row flex-wrap w-full gap-4">
            <div class="grid w-full grid-cols-2">
                <div class="p-16 border border-gray-300 rounded-l-lg dark:border-gray-700">
                    <div class="flex flex-row flex-wrap w-full gap-4">
                        <div v-if="registrationPolicy">
                            <BallotPolicyCard :policy="registrationPolicy"
                                              :address="addresses.registrationPolicyAddress"
                                              :ballot="ballot">
                                Registration Policy
                            </BallotPolicyCard>
                        </div>
                        <span v-else>
                            <NewPolicyButton class="w-96" :ballot="ballot"/>
                        </span>
                    </div>
                </div>
                <div class="p-16 -ml-px border border-gray-300 rounded-r-lg dark:border-gray-700">
                    <div class="flex flex-row flex-wrap w-full gap-4">
                        <div v-if="votingPolicy">
                            <BallotPolicyCard :policy="votingPolicy"
                                              :address="addresses.votingPolicyAddress"
                                              :ballot="ballot">
                                Voting Policy
                            </BallotPolicyCard>
                        </div>
                        <span v-else>
                            <NewPolicyButton class="w-96" :ballot="ballot"/>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script setup lang="ts">
import {ComputedRef, computed} from "vue";
import BallotData = App.DataTransferObjects.BallotData;
import PolicyData = App.DataTransferObjects.PolicyData;
import NewPolicyButton from "./NewPolicyButton.vue";
import BallotPolicyCard from "./BallotPolicyCard.vue";

const props = defineProps<{
    ballot: BallotData;
    addresses: {
        registrationPolicyAddress: string;
        votingPolicyAddress: string;
    }
}>();

let registrationPolicy: ComputedRef<PolicyData | null> = computed(() => {
    if (props.ballot?.policies?.length === 1 || props.ballot?.policies?.length === 2)
        return props.ballot?.policies[0];
    return null;
});

let votingPolicy: ComputedRef<PolicyData | null> = computed(() => {
    if (props.ballot.policies?.length == 2)
        return props.ballot?.policies[1];
    return null;
});

</script>
