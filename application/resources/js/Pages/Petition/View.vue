<template>
   <VoterLayout v-if="petition$" page="Petition" :crumbs="crumbs" :actions="actions">
       <section class="w-full py-12">
           <div class="inner-container">
               <!-- Preview banner for owners viewing non-published petitions -->
               <div
                   v-if="isOwner && petition$.status !== 'published'"
                   class="mb-6 flex items-center gap-3 rounded-lg border px-4 py-3 text-sm font-medium"
                   :class="previewBannerClass"
               >
                   <span class="shrink-0 capitalize font-semibold">[{{ petition$.status }}]</span>
                   <span>{{ previewBannerMessage }}</span>
               </div>

               <div v-if="petition$.status === 'published' || isOwner">
                   <PetitionSingle :signature="signature" />
               </div>
               <div v-else class="flex flex-row justify-center my-8 border rounded-lg border-slate-900 dark:border-slate-700 dark:text-slate-100">
                   <p class="py-16 text-2xl font-semibold text-center dark:text-white">This petition is not yet published.</p>
               </div>
           </div>
           <Modal :show="showModal">
               <ClosePetition @close="showModal = false"></ClosePetition>
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
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import {usePetitionSignatureStore} from '@/Pages/Petition/stores/petition-signature-store';

const props = defineProps<{
   petition: PetitionData;
   crumbs: [];
   actions: []
   signature: SignatureData;
}>();

let configStore = useConfigStore();
let { showModal } = storeToRefs(configStore);

const page = usePage();
const user = computed(() => page.props.auth?.user);

let petitionSignatureStore = usePetitionSignatureStore();
petitionSignatureStore.setPetition(props.petition);
let { petition$ } = storeToRefs(petitionSignatureStore);

const isOwner = computed(() => !!user.value && petition$.value?.user_id === user.value.id);

const previewBannerClass = computed(() => {
    const status = petition$.value?.status;
    if (status === 'draft')     return 'bg-amber-50 border-amber-300 text-amber-800 dark:bg-amber-900/30 dark:border-amber-600 dark:text-amber-300';
    if (status === 'pending')   return 'bg-blue-50 border-blue-300 text-blue-800 dark:bg-blue-900/30 dark:border-blue-600 dark:text-blue-300';
    if (status === 'approved')  return 'bg-green-50 border-green-300 text-green-800 dark:bg-green-900/30 dark:border-green-600 dark:text-green-300';
    if (status === 'rejected')  return 'bg-red-50 border-red-300 text-red-800 dark:bg-red-900/30 dark:border-red-600 dark:text-red-300';
    return 'bg-gray-50 border-gray-300 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300';
});

const previewBannerMessage = computed(() => {
    const status = petition$.value?.status;
    if (status === 'draft')    return 'This is a draft — only you can see this preview. Submit it for review when ready.';
    if (status === 'pending')  return 'This petition is under review — only you can see this preview.';
    if (status === 'approved') return 'This petition has been approved — only you can see this preview. Publish it to make it public.';
    if (status === 'rejected') return 'This petition was rejected — only you can see this preview.';
    return 'Only you can see this preview.';
});
</script>
