<template >
    <ModalRoute>
        <div v-if="!(creatingBallot || selecteExistingBallot)"
            class="flex flex-col justify-center gap-8 p-16 overflow-hidden rounded-xl dark:border-gray-700">
            <PrimaryButton @click="creatingBallot = true" :theme="'primary'">
                <PlusIcon class="w-6 h-6" />
                <span>Create new Ballot</span>
            </PrimaryButton>
            <PrimaryButton @click="selecteExistingBallot = true" :theme="'primary'">
                <ArrowDownTrayIcon class="w-5 h-5" />
                <span>Select existing Ballot</span>
            </PrimaryButton>
        </div>
        <div v-if="selecteExistingBallot" class="flex flex-col min-h-96">
            <SelectBallot @create-ballot="changeContext()" @submit="(payload) => moveToBallot({}, payload)" />
        </div>
        <div v-if="creatingBallot" class="min-h-96">
            <PetitionBallotForm @select-existing="changeContext()" @submit="(payload) => moveToBallot(payload)" />
        </div>
    </ModalRoute>
</template>

<script lang="ts" setup>
import { Ref, computed, onUnmounted, ref } from 'vue';
import { PlusIcon, ArrowDownTrayIcon } from '@heroicons/vue/20/solid';
import ModalRoute from '@/Components/ModalRoute.vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PetitionBallotForm from './Partials/PetitionBallotForm.vue';
import SelectBallot from './Partials/SelectBallot.vue';
import BallotData = App.DataTransferObjects.BallotData;
import { useBallotStore } from '@/stores/ballot-store';
import { storeToRefs } from 'pinia';
import { router, useForm } from '@inertiajs/vue3';
import AlertService from '@/shared/Services/alert-service';
import compareValues from '@/utils/compare-values';
import { useModal } from 'momentum-modal';
import axios from 'axios';

let { close, redirect, } = useModal();

const ballotstore = useBallotStore()
let { ballots } = storeToRefs(ballotstore);
let creatingBallot = ref(false);
let selecteExistingBallot = ref(false);
let ballot: Ref<BallotData> = ref(null);



const props = defineProps<{
    petition: PetitionData;
}>();


let changeContext = () => {
    creatingBallot.value = !creatingBallot.value;
    selecteExistingBallot.value = !selecteExistingBallot.value;
}

function moveToBallot(formData, data = null) {
    const params = {
        ...formData,
        status: 'ballot',
    };

    if (data?.questionTitle) {
        params['questionTitle'] = data?.questionTitle
    }
    const minSignatureReq = props.petition.petition_goals['ballot-eligible']['value2'];
    const operator = props.petition.petition_goals['ballot-eligible']['operator'];
    const canMovePetition = compareValues(props.petition.signatures_count, minSignatureReq, operator)

    if (canMovePetition) {
        axios.patch(route(
            'admin.petitions.update', { petition: props.petition?.hash, ballot: data?.ballotHash, }),
            params).then((res) => {
                redirect()
                if (res.data.hash) {
                    ballot.value = res.data;
                } else {
                    AlertService.show(['Error '], 'error');
                    close();
                }
            })
    } else {
        AlertService.show(['Minimum signature requirements not met '], 'error');
        close();
    }

}

onUnmounted(() => {
    router.visit(route('admin.ballots.edit', { ballot: ballot.value.hash }));
})
</script>