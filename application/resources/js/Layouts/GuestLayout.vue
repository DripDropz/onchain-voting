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
        <div class="flex flex-col items-center min-h-screen pt-6 bg-sky-50 dark:bg-gray-950 sm:justify-center sm:pt-0">
            <div>
                <Link href="/">
                    <ApplicationLogo class="w-20 h-20 text-gray-500 fill-current" />
                </Link>
            </div>

            <div
                class="w-full px-6 py-4 mt-6 overflow-hidden bg-white dark:bg-gray-900 shadow-md sm:max-w-md sm:rounded-lg border border-gray-200 dark:border-gray-700"
            >
            <GlobalAlertComponent/>
                <slot />
            </div>
        </div>
    </div>
</template>
