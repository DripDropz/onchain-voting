<template>
    <ModalRoute max-width-class="max-w-xl">
        <div class="px-8 py-4">
            <div class="sm:flex sm:items-center">
                <div
                    class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                >
                    <ExclamationTriangleIcon
                        class="w-6 h-6 text-red-600"
                        aria-hidden="true"
                    />
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <DialogTitle
                        as="h3"
                        class="text-xl font-semibold leading-6 text-gray-900 dark:text-white"
                        >Missing from Snapshot!</DialogTitle
                    >
                    <div class="mt-2">
                        <p class="text-gray-500 text-md">
                            We apologize for the inconvenience, but we can't seem to find your voting power.
                            Please contact 
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button
                    type="button"
                    class="inline-flex justify-center w-full px-3 py-2 text-sm font-semibold text-white bg-red-600 rounded-md shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                    @click.prevent="closeModal"
                >
                    Close
                </button>
            </div>
        </div>
    </ModalRoute>
</template>
<script lang="ts" setup>
import ModalRoute from "@/Components/ModalRoute.vue";
import { XMarkIcon, ExclamationTriangleIcon } from "@heroicons/vue/24/outline";
import { useModal } from "momentum-modal";

const { close } = useModal();

function closeModal() {
    close();
}
</script>
