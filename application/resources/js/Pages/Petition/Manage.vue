<template>
    <VoterLayout
        page="Manage Petition"
        :crumbs="crumbs"
        :actions="actions" >
        <div class="w-full">
            <div class="sticky top-0 w-full p-6 dark:text-white">
                <div class="container flex flex-col items-center gap-6 w-full pb-3">
                    <p class="text-xl font-bold leading-tight xl:text-2xl">{{ petition.title }}</p>

                  <div v-if="petition.status === 'draft'" class="flex items-center gap-2 text-base">
                    <div class="opacity-75">Status:</div>
                    <p class="font-bold leading-tight text-amber-500">
                      Waiting for admin approval.
                    </p>
                  </div>
                  <div v-if="petition.status === 'approved'" class="flex flex-col items-center gap-4 text-base">
                    <p class="font-bold leading-tight text-green-500">
                      Your Petition has been approved.
                    </p>
                    <button
                            @click.prevent="publishPetition()"
                            class="font-semibold bg-sky-500 txt-white hover:text-slate-700 dark:hover:text-white py-2 px-4 rounded-md">
                            <span>Publish</span>
                        </button>
                  </div>
                </div>
            </div>
            <div
                class="flex justify-between px-5 py-16 mx-auto bg-sky-50 dark:bg-sky-950 lg:px-8 2xl:px-10 dark:text-white">
                <div class="container flex flex-col gap-6">
                    <div>
                        <p class="mb-4 text-lg font-bold leading-tight xl:text-xl">Profile Overview</p>
                        <div class="grid gap-6 lg:grid-cols-2">
                            <PetitionSupporters :petition="petition" />

                            <div class="p-6 bg-white rounded-lg dark:bg-gray-900">
                                <div>
                                    <span class="font-bold">Petition Goals</span>
                                </div>
                                <PetitionGoals :petition="petition" />
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <p class="mb-4 text-lg font-bold leading-tight xl:text-xl">Grow Petition</p>
                            <div class="flex flex-col gap-4 p-6 bg-white rounded-lg dark:bg-gray-900">
                                <span class="font-bold">Share Petition</span>
                                <span> Share your petition tp spread awareness of the cause.</span>
                                <div class="flex gap-2">
                                    <input v-model="link" type="text" readonly
                                        class="w-1/2 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600">
                                    <PrimaryButton @click="copy(link)"
                                        class="inline-flex items-center p-1 font-semibold bg-white border rounded-md shadow-sm dark:bg-gray-900 gap-x-2 hover:bg-sky-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 ">
                                        <LinkIcon class="w-4 h-4" />
                                        <span>
                                            Copy
                                        </span>
                                    </PrimaryButton>
                                </div>
                                <Link :href="`share`">
                                <PrimaryButton :theme="'primary2'">
                                    <span class="">Share Petition</span>
                                </PrimaryButton>
                                </Link>
                            </div>
                        </div>
                        <div>
                            <div class="flex flex-row items-center gap-4 mb-4">
                                <p class="text-lg font-bold leading-tight xl:text-xl">Petition Criteria </p>
                                <LockClosedIcon class="w-4 h-4" />
                            </div>

                            <div class="p-3 bg-white rounded-lg dark:bg-gray-900">
                                <Criteria :model="petition" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <Modal :show="showModal">
                <ClosePetition :petition="petition" @close="showModal = false" />
            </Modal>
            <Modal :show="showPublishModal" :modalType="'publish'">
                <PublishPetition :petition="petition" @close="showPublishModal = false" />
            </Modal>
        </div>
    </VoterLayout>
</template>

<script lang="ts" setup>
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import {LockClosedIcon} from "@heroicons/vue/20/solid";
import {Link, useForm} from "@inertiajs/vue3";
import {LinkIcon} from "@heroicons/vue/20/solid";
import AlertService from "@/shared/Services/alert-service";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Criteria from "@/shared/components/Criteria.vue";
import Modal from "@/Components/Modal.vue";
import ClosePetition from "./Partials/ClosePetition.vue";
import PublishPetition from "./Partials/PublishPetition.vue";
import PetitionGoals from "./Partials/PetitionGoals.vue";
import PetitionSupporters from "@/Pages/Petition/Partials/PetitionSupporters.vue"
import { useConfigStore } from "@/stores/config-store";
import { storeToRefs } from "pinia";

const props = defineProps<{
    petition: PetitionData;
    crumbs: [];
    actions: []
}>();

let configStore = useConfigStore()
let {showModal, showPublishModal } = storeToRefs(configStore);

const form = useForm({
    status: props?.petition?.status,
});

const emit = defineEmits<{(e: 'close'):void}>()

const publishPetition = async () => {
        try {
            await form.put(route("petitions.publish", { petition: props.petition?.hash }));
            props.petition.status = "published";
            AlertService.show(["Petition has been published"], "success");
            emit('close');
        } catch (error) {
            AlertService.show(["There was an error publishing the petition"], "error");
        }
};

let copy = (link) => {
    try {
        navigator.clipboard.writeText(link);
        AlertService.show(['Copied'], 'success');
    } catch (error) {
        AlertService.show(['Not Copied'], 'error');
    }
}

let link = route('petitions.view', { petition: props.petition.hash });

</script>
