<template>
    <VoterLayout
        page="Manage Petition"
        :crumbs="crumbs"
        :actions="actions" >
        <div class="w-full">
            <div class="sticky top-0 w-full p-6 bg-white dark:bg-gray-900 dark:text-white">
                <div class="container flex justify-center w-full pb-3">
                    <p class="text-xl font-bold leading-tight xl:text-2xl">{{ petition.title }}</p>
                </div>
            </div>
            <div
                class="flex justify-between px-5 py-16 mx-auto bg-sky-50 dark:bg-sky-950 lg:px-8 2xl:px-10 dark:text-white">
                <div class="container flex flex-col gap-6">
                    <div>
                        <p class="mb-4 text-lg font-bold leading-tight xl:text-xl">Profile Overview</p>
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="flex flex-col p-6 bg-white rounded-lg dark:bg-gray-900 ">
                                <div class="flex flex-col items-center w-full">
                                    <div class="relative float-left m-4 text-center">
                                        <div class="relative w-[180px] h-[90px] -mb-[14px] overflow-hidden">
                                            <div
                                                class="absolute top-0 left-0 w-[180px] h-[180px] rounded-[50%] box-border border-[10px] border-slate-100 border-b-sky-500 border-l-sky-500">
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xl font-bold leading-tight xl:text-2xl">10</span>
                                            <span> supporters</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center w-full">
                                    <span>Only <span class="font-bold">5 more</span> supporters to your goal.</span>
                                </div>
                            </div>
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
                <ClosePetition :petition="petition" @close="showModal = false"></ClosePetition>
            </Modal>
        </div>
    </VoterLayout>
</template>

<script lang="ts" setup>
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import {PencilIcon} from "@heroicons/vue/20/solid";
import {ArrowTopRightOnSquareIcon} from "@heroicons/vue/20/solid";
import {LockClosedIcon} from "@heroicons/vue/20/solid";
import {Link} from "@inertiajs/vue3";
import {LinkIcon} from "@heroicons/vue/20/solid";
import AlertService from "@/shared/Services/alert-service";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Criteria from "@/shared/components/Criteria.vue";
import Modal from "@/Components/Modal.vue";
import ClosePetition from "./Partials/ClosePetition.vue";
import PetitionGoals from "./Partials/PetitionGoals.vue";
import { useConfigStore } from "@/stores/config-store";
import { storeToRefs } from "pinia";
import axios from "axios";
import { useForm } from '@inertiajs/vue3';



const props = defineProps<{
    petition: PetitionData;
    crumbs: [];
    actions: []
}>();

let configStore = useConfigStore();
let { showModal } = storeToRefs(configStore);

const form = useForm({
    petition: props.petition.hash,
});

const publishPetition = async () => {
    try {
        await axios.post(route('petitions.publish', { petition: props.petition.hash }));
        AlertService.show(["Petition has been published"], "success");
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

