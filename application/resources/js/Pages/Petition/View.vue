<template>
    <VoterLayout
        page="Petition"
        :crumbs="crumbs"
        :actions="actions">
        <section class="py-12 w-full">
            <div class="inner-container">
                <PetitionSingle
                    :key="petition.hash"
                    :petition="petition"
                    :signature="signature"
                />
            </div>
            <Modal :show="showModal">
                <ClosePetition :petition="petition" @close="showModal = false"></ClosePetition>
            </Modal>
        </section>
    </VoterLayout>
</template>

<script lang="ts" setup>
import VoterLayout from '@/Layouts/VoterLayout.vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import SignatureData = App.DataTransferObjects.SignatureData;
import PetitionSingle from './Partials/PetitionSingle.vue';
import Modal from "@/Components/Modal.vue";
import ClosePetition from "./Partials/ClosePetition.vue";
import { useConfigStore } from "@/stores/config-store";
import { storeToRefs } from "pinia";

defineProps<{
    petition: PetitionData;
    crumbs: [];
    actions: []
    signature: SignatureData;
}>();

let configStore = useConfigStore();
let { showModal } = storeToRefs(configStore);

</script>
