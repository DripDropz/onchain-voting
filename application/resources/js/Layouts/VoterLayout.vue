<script setup lang="ts">
import GlobalAlertComponent from '../shared/components/GlobalAlertComponent.vue';
import {Head} from '@inertiajs/vue3';
import Header from "@/Pages/Partials/Header.vue";
import Footer from "@/Pages/Partials/Footer.vue";
import {useConfigStore} from "@/stores/config-store";
import {storeToRefs} from 'pinia';
import {Modal} from 'momentum-modal';
import PageActions from "@/Components/PageActions.vue";
import Nav from '@/Pages/NavCrumbs.vue';

withDefaults(
    defineProps<{
        page: string;
        pageData?: any;
        canLogin?: boolean;
        actions?: [];
        crumbs?: [];
    }>(), {
        canLogin: true
    });

let configStore = useConfigStore();
let {isDarkMode, showModal} = storeToRefs(configStore);

</script>

<template>
    <div class="h-full overflow-y-auto" :class="{'dark': isDarkMode }">
        <div class="h-full min-h-screen bg-white dark:bg-gray-900">

            <Head :title="page"/>

            <div
                class="relative flex flex-col justify-start min-h-screen bg-center bg-dots-darker dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
                <div class="relative z-50 w-full py-4 text-right border-b border-slate-100">
                    <div class="container">
                        <Header :can-login="canLogin" :pageData="pageData"/>
                    </div>
                </div>

                <nav v-if="!$page.component.startsWith('Home')"
                     class="bg-white shadow dark:bg-gray-800" role="navigation" :crumbs="[]">
                    <div class="container">
                        <div class="flex justify-between items-center">
                            <div class="breadcrumbs-wrapper">
                                <Nav :crumbs="crumbs"/>
                            </div>
                            <div class="actions-wrapper h-full">
                                <PageActions :actions="actions"/>
                            </div>
                        </div>
                    </div>
                </nav>

                <main class="z-10 flex flex-col flex-1 w-full">
                    <slot/>
                </main>

                <div class="relative z-50 w-full mt-auto text-right border-t border-slate-300">
                    <Footer :pageData="pageData"/>
                </div>
            </div>

            <div class="fixed top-0 left-0 z-50 flex items-end justify-end w-full h-full pointer-events-none">
                <GlobalAlertComponent/>
            </div>

            <Modal/>
        </div>
    </div>
</template>

<style>
/* .bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
} */

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
