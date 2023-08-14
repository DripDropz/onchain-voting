<template>
    <div class="flex flex-row items-center justify-between w-full py-3">
        <div :class="{ 'w-5/6': !$page.props.auth.user }">
            <nav>
                <ul class="flex items-center gap-3 flex-nowrap">
                    <li class="w-auto ocv-link">
                        <Link :href="route('home')">
                            <img :src="config.logo" alt="Open Chainvote App Logo" class="w-10 h-10">
                        </Link>
                    </li>
                    <li class="w-auto ocv-link">
                        <Link :href="route('home')">
                            Open Chainvote
                        </Link>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="flex flex-row items-center justify-end w-1/3 gap-6 text-white">
            <div class="relative flex items-center gap-0 py-0.5 bg-indigo-700 rounded-lg flex-nowrap hover:bg-indigo-700">

                <div class="hover:text-yellow-400">
                    <ConnectWallet background-color="bg-indigo-700"></ConnectWallet>
                </div>

                <Link :href="route('login.wallet', { hash: pageData?.hash })" v-if="!user?.hash"
                    class="flex items-center h-full gap-2 px-3 py-2 mx-1 bg-indigo-800 rounded-lg hover:bg-indigo-950">
                <p>Login</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="relative w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                </Link>
                <Link preserve-state v-if="user?.hash" href="#" @click.prevent="logout"
                    class="flex items-center h-full gap-2 px-3 py-2 mx-1 bg-indigo-800 rounded-lg hover:bg-indigo-950">
                    <p>Logout</p>
                    <ArrowRightOnRectangleIcon class="w-5 h-5"></ArrowRightOnRectangleIcon>
                </Link>
            </div>
            <DarkModeButton />
        </div>
    </div>
</template>
<script lang="ts" setup>
import { Link, router } from '@inertiajs/vue3';
import ConnectWallet from "@/cardano/Components/ConnectWallet.vue";
import DarkModeButton from '@/shared/components/DarkModeButton.vue';
import { ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline';
import { usePage } from "@inertiajs/vue3";
import { useWalletStore } from '@/cardano/stores/wallet-store';
import { useConfigStore } from '@/stores/config-store';
import { storeToRefs } from 'pinia';

const user = usePage().props.auth.user;
const walletStore = useWalletStore();

let configStore = useConfigStore();
let {config} = storeToRefs(configStore);

withDefaults(defineProps<{
    canLogin?: boolean;
    pageData?: any;
}>(), {
    pageData: null
});
function logout() {
    router.post(route('logout'));
    walletStore.disconnect();
    window.location.reload();
}

</script>
