<script lang="ts" setup>
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from "@headlessui/vue"
import { useModal } from "momentum-modal"
import { useConfigStore } from "@/stores/config-store";
import { storeToRefs } from 'pinia';

const { show, close, redirect } = useModal();
let configStore = useConfigStore();
let {isDarkMode} = storeToRefs(configStore);

withDefaults(
    defineProps<{
        maxWidthClass?: string;
    }>(),
    {
        maxWidthClass: 'max-w-lg',
    }
);
</script>

<template>
    <TransitionRoot appear as="template" :show="show">
        <Dialog as="div" class="relative z-10" :class="[isDarkMode ? 'dark' : '']" @close="close">
            <TransitionChild
                @after-leave="redirect"
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 opacity-75 dark:bg-gray-900" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex items-center justify-center min-h-full p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">
                        <DialogPanel class="w-full overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl dark:bg-gray-800 dark:text-gray-200"
                            :class="[maxWidthClass]">
                            <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                                <slot name="title" />
                            </DialogTitle>
                            <slot />
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
