<template>
    <div class="flex flex-row items-center justify-between w-full py-3">
        <div :class="{'w-5/6':!$page.props.auth.user}">
            <nav>
                <ul class="flex gap-3 flex-nowrap">
                    <li class="w-auto ocv-link">
                       <Link :href="route('home')">
                           Open Chainvote
                       </Link>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="flex flex-row items-center justify-between w-1/3 text-white">
            <div class="flex flex-row " :class="{'w-1/6 gap-3 px-12':!$page.props.auth.user}" v-if="canLogin">
    <!--            <Link-->
    <!--                v-if="$page.props.auth.user"-->
    <!--                :href="route('admin.dashboard')"-->
    <!--                class="ocv-link"-->
    <!--            >Dashboard-->
    <!--            </Link>-->
    
    
                <ConnectWallet></ConnectWallet>
            </div>
            <button type="button" @click="darkModeStore.toggleDarkMode()" aria-label="Toggle dark mode" class="px-3 py-2 transition rounded-full shadow-lg group bg-white/90 shadow-zinc-800/5 ring-1 ring-zinc-900/5 backdrop-blur dark:bg-zinc-800/90 dark:ring-white/10 dark:hover:ring-white/20">
                <svg viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="h-6 w-6 fill-zinc-100 stroke-zinc-500 transition group-hover:fill-zinc-200 group-hover:stroke-zinc-700 dark:hidden [@media(prefers-color-scheme:dark)]:fill-teal-50 [@media(prefers-color-scheme:dark)]:stroke-teal-500 [@media(prefers-color-scheme:dark)]:group-hover:fill-teal-50 [@media(prefers-color-scheme:dark)]:group-hover:stroke-teal-600">
                    <path d="M8 12.25A4.25 4.25 0 0 1 12.25 8v0a4.25 4.25 0 0 1 4.25 4.25v0a4.25 4.25 0 0 1-4.25 4.25v0A4.25 4.25 0 0 1 8 12.25v0Z">
    
                    </path>
                    <path d="M12.25 3v1.5M21.5 12.25H20M18.791 18.791l-1.06-1.06M18.791 5.709l-1.06 1.06M12.25 20v1.5M4.5 12.25H3M6.77 6.77 5.709 5.709M6.77 17.73l-1.061 1.061" fill="none">

                    </path>
                </svg>
                <svg viewBox="0 0 24 24" aria-hidden="true" class="hidden h-6 w-6 fill-zinc-700 stroke-zinc-500 transition dark:block [@media(prefers-color-scheme:dark)]:group-hover:stroke-zinc-400 [@media_not_(prefers-color-scheme:dark)]:fill-teal-400/10 [@media_not_(prefers-color-scheme:dark)]:stroke-teal-500">
                    <path d="M17.25 16.22a6.937 6.937 0 0 1-9.47-9.47 7.451 7.451 0 1 0 9.47 9.47ZM12.75 7C17 7 17 2.75 17 2.75S17 7 21.25 7C17 7 17 11.25 17 11.25S17 7 12.75 7Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">

                    </path>
                </svg>
            </button>
        </div>
    </div>
</template>
<script lang="ts" setup>

import {Head, Link} from '@inertiajs/vue3';
import ConnectWallet from "@/cardano/Components/ConnectWallet.vue";
import { useDarkModeStore } from "@/store/dark-mode-store";
import { storeToRefs } from 'pinia';

defineProps<{
    canLogin?: boolean;
}>();

let darkModeStore = useDarkModeStore();
let {isDarkMode} = storeToRefs(darkModeStore);


</script>