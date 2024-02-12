<template>
    <div class="p-6 bg-white rounded-lg dark:bg-gray-900">
        <div>
            <div v-if="visible || featurePetition || ballotEligible">
                <div>
                    <span class="font-bold">Petition Goals</span>
                </div>
                <div>
                    <span>
                        Enlist your community!
                    </span>
                    <ul class="p-3 text-sm list-disc">
                        <li class="py-1" v-if="visible">
                            <span class="font-bold"> {{ visible }}
                                signatures</span> vissible on site
                        </li>
                        <li class="py-1" v-if="featurePetition">
                            <span class="font-bold"> {{ featurePetition }}
                                signatures</span> featured petition
                        </li>
                        <li class="py-1" v-if="ballotEligible">
                            <span class="font-bold"> {{ ballotEligible }}
                                signatures</span> Become a ballot
                        </li>
                    </ul>
                </div>
            </div>
            <div v-else class="flex flex-col items-start w-full mt-4">
                <span>
                    Waiting for admin to set petition goal
                </span>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia';
import PetitionData = App.DataTransferObjects.PetitionData;
import { usePetitionSignatureStore } from '@/Pages/Petition/stores/petition-signature-store';

const props = defineProps<{
    petition: PetitionData;
}>();

let petitionSignatureStore = usePetitionSignatureStore();
petitionSignatureStore.setPetition(props.petition);
let { visible$, featurePetition$, ballotEligible$ } = storeToRefs(petitionSignatureStore);

const visible = visible$.value?.value2;
const featurePetition = featurePetition$.value?.value2
const ballotEligible = ballotEligible$.value?.value2
</script>