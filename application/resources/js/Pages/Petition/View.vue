<template>
    <VoterLayout page="Petition" :crumbs="crumbs" :actions="actions">
        <section class="w-full py-12">
            <div class="inner-container">
                <div v-if="petition.status === 'published'">
                    <PetitionSingle :key="petition.hash" :petition="petition" :signature="signature" />
                </div>
                <div v-else class="flex flex-row justify-center my-8 border rounded-lg border-slate-900 dark:border-slate-700 dark:text-slate-100">
                    <p class="py-16 text-2xl font-semibold text-center dark:text-white">This petition is not yet published.</p>
                </div>
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
