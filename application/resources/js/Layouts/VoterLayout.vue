<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import GlobalAlertComponent from '../shared/components/GlobalAlertComponent.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { withDefaults } from "vue";
import Header from "@/Pages/Partials/Header.vue";
import { useDarkModeStore } from "@/stores/dark-mode-store";
import { storeToRefs } from 'pinia';

withDefaults(
    defineProps<{
        page: string;
        canLogin?: boolean;
    }>(), {
    canLogin: true
});

let darkModeStore = useDarkModeStore();
let {isDarkMode} = storeToRefs(darkModeStore);

const user = usePage().props.auth?.user;


</script>

<template>
    <div :class="{'dark': isDarkMode }">
        <div class="min-h-screen bg-slate-200 dark:bg-gray-900">
            <GlobalAlertComponent/>

            <Head :title="page" />

            <div
                class="relative min-h-screen bg-center bg-dots-darker bg-gray-50 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
                <div class="relative z-50 w-full p-6 text-right border-b-8 border-indigo-600">
                    <div class="container">
                        <Header :can-login="canLogin"/>
                    </div>
                </div>

                <main class="z-10 ">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
