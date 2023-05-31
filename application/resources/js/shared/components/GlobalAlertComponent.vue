<template>
    <div aria-live="assertive" class="inset-0 flex items-end px-4 py-6 pointer-events-none sm:items-start sm:p-6">
        <div class="flex flex-col items-center w-full space-y-4 sm:items-end">
            <transition enter-active-class="transition duration-300 ease-out transform"
                        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                        leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100"
                        leave-to-class="opacity-0">

                <div v-if="alert?.show" :class="{
                    'bg-yellow-800': alert?.type === 'info',
                    'bg-teal-600': alert?.type === 'success',
                    'bg-red-500': alert?.type === 'error',
                }"
                     class="absolute bottom-0 right-0 py-6 w-full max-w-sm m-8 bg-red-400 shadow-lg pointer-events-auto ronded-lg overflow-hiddene ring-1 ring-black ring-opacity-5">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <CheckCircleIcon v-if="alert?.type === 'success'" class="w-6 h-6 text-white"
                                                 aria-hidden="true"/>
                                <XCircleIcon v-if="alert?.type === 'error'" class="w-6 h-6 text-white"
                                             aria-hidden="true"/>
                                <InformationCircleIcon v-if="alert?.type === 'info'" class="w-6 h-6 text-white"
                                                       aria-hidden="true"/>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5 text-white">
                                <p class="text-sm font-medium ">
                                    {{ alert?.message }}
                                </p>
                            </div>
                            <div class="flex flex-shrink-0 ml-4">
                                <button type="button" @click="alertStore.closeAlert()"
                                        class="inline-flex text-gray-400 bg-white rounded-md hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="sr-only">Close</span>
                                    <XMarkIcon class="w-5 h-5" aria-hidden="true"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>
<<<<<<< Updated upstream
=======


<script lang="ts" setup>
import { ref } from "vue";
import { CheckCircleIcon } from "@heroicons/vue/24/outline";
import { XCircleIcon } from "@heroicons/vue/24/outline";
import { XMarkIcon } from "@heroicons/vue/20/solid";
import { InformationCircleIcon } from "@heroicons/vue/20/solid";
import { useGlobalAlert } from "@/stores/global-alert-store";
import { storeToRefs } from "pinia";
>>>>>>> Stashed changes

<script lang="ts" setup>
import {ref} from "vue";
import {CheckCircleIcon} from "@heroicons/vue/24/outline";
import {XCircleIcon} from "@heroicons/vue/24/outline";
import {XMarkIcon} from "@heroicons/vue/20/solid";
import {InformationCircleIcon} from "@heroicons/vue/20/solid";
import {useGlobalAlert} from "@/store/global-alert-store";
import {storeToRefs} from "pinia";
import Alert from "@/models/alert";

const show = ref(false);
const alertStore = useGlobalAlert();
let {alert} = storeToRefs(alertStore);
</script>
