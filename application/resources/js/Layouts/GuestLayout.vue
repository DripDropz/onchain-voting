<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import GlobalAlertComponent from '../shared/components/GlobalAlertComponent.vue';
import { Link } from '@inertiajs/vue3';
import { useConfigStore } from "@/stores/config-store";
import { storeToRefs } from 'pinia';

let configStore = useConfigStore();
let {isDarkMode} = storeToRefs(configStore);
</script>

<template>
    <div :class="{'dark': isDarkMode }">
        <div class="flex flex-col items-center min-h-screen pt-6 bg-indigo-100 sm:justify-center sm:pt-0 dark:bg-gray-900">
            <div>
                <Link href="/">
                    <ApplicationLogo class="w-20 h-20 text-gray-500 fill-current" />
                </Link>
            </div>

            <div
                class="w-full px-6 py-4 mt-6 overflow-hidden bg-indigo-200 shadow-md sm:max-w-md dark:bg-gray-800 sm:rounded-lg"
            >
            <GlobalAlertComponent/>
                <slot />
            </div>
        </div>
    </div>
</template>
