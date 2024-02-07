<template>
    <div class="p-6 bg-white rounded-lg dark:bg-gray-900">
        <div>
            <span class="font-bold">Petition Goals</span>
        </div>
        <div>
            <span>
                Enlist your community!
            </span>
            <ul class="p-3 text-sm list-disc">
                <li class="py-1 ">
                    <span class="font-bold"> {{ visible }}
                        signatures</span> vissible on site
                </li>
                <li class="py-1 ">
                    <span class="font-bold"> {{ featurePetition }}
                        signatures</span> featured petition
                </li>
                <li class="py-1 ">
                    <span class="font-bold"> {{ ballotEligible }}
                        signatures</span> Become a ballot
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts" setup>
import PetitionData = App.DataTransferObjects.PetitionData;

const props = defineProps<{
    petition: PetitionData;
}>();

const visible = props.petition?.petition_goals?.visible?.value2;
const featurePetition = props.petition?.petition_goals?.['feature-petition']?.value2
const ballotEligible = props.petition?.petition_goals?.['ballot-eligible']?.value2
</script>