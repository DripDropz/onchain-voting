<script setup lang="ts">
import CreateUpdatePolicy from "./Partials/CreateUpdatePolicy.vue";
import {computed} from 'vue';
import BallotData = App.DataTransferObjects.BallotData;
import PolicyData = App.DataTransferObjects.PolicyData;
import ModalRoute from "@/Components/ModalRoute.vue";
import { ComputedRef } from "vue";
import { VARIABLES } from "@/models/variables";

const props = defineProps<{
    ballot: BallotData;
}>();

let context = computed(() => {
    if (props?.ballot?.policies && props?.ballot?.policies.length > 0) return VARIABLES.VOTING;
    return VARIABLES.REGISTRATION;
});

let policy: ComputedRef<PolicyData|undefined> = computed(() => {
    switch(context.value) {
        case VARIABLES.VOTING:
            if ( props.ballot.policies?.length == 2)
                return props.ballot?.policies[1];
            break;
        case VARIABLES.REGISTRATION:
            if ( props.ballot.policies?.length == 1)
                return props.ballot.policies[0];
    }
});
</script>

<template>
    <ModalRoute>
        <div class="flex flex-col">
            <div class="p-6 bg-gray-50 dark:bg-gray-900">
                <h2 class="text-lg font-semibold leading-tight text-gray-800 2xl:text-xl dark:text-gray-200">
                    Create <span class="italic font-bold">{{ballot.title}}</span> <span class="uppercase">{{ context }}</span> Ballot Policy
                </h2>
            </div>

            <div class="p-6 smax-w-7xl">
                <CreateUpdatePolicy
                    :ballot="ballot"
                    :context="context"
                    :policy="policy"
                    class="max-w-xl" />
            </div>
        </div>
    </ModalRoute>
</template>
