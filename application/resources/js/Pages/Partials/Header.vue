<template>
    <div class="flex flex-row items-center justify-between w-full py-3">
        <div :class="{ 'w-5/6': !$page.props.auth.user }">
            <nav>
                <ul class="flex items-center gap-3 flex-nowrap">
                    <li class="w-auto ocv-link">
                        <Link :href="route('home')">
                        <img :src="config.logo ?? voteAppLogo" alt="Open Chainvote App Logo" class="w-10 h-10">
                        </Link>
                    </li>
                    <li class="w-auto ocv-link">
                        <Link :href="route('home')">
                        <h1
                            class="font-bold tracking-tight sm:text-xl xl:text-3xl font-display text-slate-900 dark:text-slate-200">
                            Open ChainVote</h1>
                        </Link>
                    </li>
                    <li
                        class="items-end justify-between hidden gap-8 p-1 ml-8 text-lg lg:flex font-display text-slate-900 dark:text-slate-200">
                        <Link v-for="option in menuOptions" :href="option.href" :class="[
                            currentUri == option.uri
                                ? 'border-b-2 border-sky-300 dark:border-sky-500 font-medium text-sky-300 dark:text-sky-300 focus:outline-none focus:border-sky-700'
                                : 'border-b-2 border-transparent font-medium  hover:text-sky-500 text-slate-900 dark:hover:text-sky-300 dark:text-slate-200 hover:border-sky-500 dark:hover:border-sky-300 focus:text-sky-500 dark:focus:text-sky-300 focus:border-sky-500 dark:focus:border-sky-300',
                            'block'
                        ]">
                            {{ option.name }}
                        </Link>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="flex flex-row items-center justify-end w-1/3 gap-6 text-white">
            <div
                class="relative lg:flex items-center gap-0 py-0.5 pl-1 bg-sky-400 rounded-lg flex-nowrap hover:bg-sky-300 hidden">
                <div class="relative hover:text-yellow-400" >
                    <ConnectWallet background-color="bg-white"></ConnectWallet>
                </div>

                <Link :href="route('login.wallet', { hash: pageData?.hash })" v-if="!user?.hash"
                    class="flex items-center h-full gap-2 px-3 py-2 mx-1 bg-sky-400 rounde-lg hover:bg-sky-400">
                <p>Login</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="relative w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                </Link>
                <Link preserve-state v-if="user?.hash" href="#" @click.prevent="logout"
                    class="flex items-center h-full gap-2 px-3 py-2 mx-1 bg-sky-400 rounde-lg hover:bg-sky-400">
                <p>Logout</p>
                <ArrowRightOnRectangleIcon class="w-5 h-5"></ArrowRightOnRectangleIcon>
                </Link>
            </div>
            <div class="lg:hidden">
                <div class="relative flex flex-col justify-between">
                    <button @click="showMenu = !showMenu"
                        class="flex items-end justify-end w-full p-1 text-sm font-medium text-center text-white rounded-lg bg-sky-400 hover:bg-sky-500 focus:ring-4 focus:outline-none focus:ring-sky-300 dark:bg-sky-400 dark:hover:bg-sky-500 dark:focus:ring-sky-700"
                        type="button">
                        <Bars3Icon class="w-5 h-5" />
                    </button>

                    <!-- Dropdown menu -->
                    <div v-if="showMenu" ref="target"
                        class="absolute z-40 w-24 mt-12 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                            <li v-for="option in menuOptions">
                                <a :href="option.href"
                                    class="flex justify-start p-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{
                                        option.name }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <DarkModeButton />
        </div>
    </div>
</template>
<script lang="ts" setup>
import { Link, router } from '@inertiajs/vue3';
import ConnectWallet from "@/cardano/Components/ConnectWallet.vue";
import DarkModeButton from '@/shared/components/DarkModeButton.vue';
import { ArrowRightOnRectangleIcon, Bars3Icon } from '@heroicons/vue/24/outline';
import { usePage } from "@inertiajs/vue3";
import { useWalletStore } from '@/cardano/stores/wallet-store';
import { useConfigStore } from '@/stores/config-store';
import { storeToRefs } from 'pinia';
import { ref } from 'vue';
import { onClickOutside } from '@vueuse/core';
import voteAppLogo from '../../../images/openchainvote.png';

const user = usePage().props.auth.user;
const walletStore = useWalletStore();

let configStore = useConfigStore();
let { config } = storeToRefs(configStore);

const props = withDefaults(defineProps<{
    canLogin?: boolean;
    pageData?: any;
}>(), {
    pageData: null
});

const currentUri =  usePage().props.ziggy.uri;
let showMenu = ref(false);
const menuOptions = [
    { name: 'Ballots', href: route('ballots.index'), uri: '/ballots' },
    { name: 'Petitions', href: route('petitions.index'), uri: '/petitions' },
    { name: 'Polls', href: route('polls.index'), uri: '/polls' },
];

function logout() {
    router.post(route('logout'));
    walletStore.disconnect();
    window.location.reload();
}

const target = ref(null);

onClickOutside(target, (event) => showMenu.value = false);

</script>
