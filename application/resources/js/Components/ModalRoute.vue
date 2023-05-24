<script lang="ts" setup>
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from "@headlessui/vue"
import { useModal } from "momentum-modal"
import { useDarkModeStore } from "@/store/dark-mode-store";
import { storeToRefs } from 'pinia';

const { show, close, redirect } = useModal()
let darkModeStore = useDarkModeStore();
let {isDarkMode} = storeToRefs(darkModeStore);

</script>

<template>
    <TransitionRoot appear as="template" :show="show" :class="{'dark': isDarkMode }">
        <Dialog as="div" class="relative z-10 dark" @close="close">
            <TransitionChild
                @after-leave="redirect"
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">
                        <DialogPanel class="w-full max-w-lg transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 dark:text-gray-200 text-left align-middle shadow-xl transition-all">
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