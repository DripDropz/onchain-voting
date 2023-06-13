<template>
    <div class="flex flex-row items-center justify-between w-full py-3">
        <div :class="{ 'w-5/6': !$page.props.auth.user }">
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
        <div class="flex flex-row items-center justify-end w-1/3 gap-6 text-white">
            <div class="relative flex items-center gap-0 py-0.5 bg-indigo-600 rounded-lg flex-nowrap hover:bg-indigo-500">
                <ConnectWallet></ConnectWallet>
                <Link :href="route('walletLogin.show')" v-if="!user?.hash"
                    class="flex items-center h-full gap-2 px-3 py-2 mx-1 bg-indigo-800 rounded-lg hover:bg-indigo-950">
                    <p>Login</p>
                    <ArrowRightOnRectangleIcon class="w-5 h-5"></ArrowRightOnRectangleIcon>
                </Link>
            </div>
            <DarkModeButton />
        </div>
    </div>
</template>
<script lang="ts" setup>

import { Head, Link } from '@inertiajs/vue3';
import ConnectWallet from "@/cardano/Components/ConnectWallet.vue";
import { useDarkModeStore } from "@/stores/dark-mode-store";
import DarkModeButton from '@/shared/components/DarkModeButton.vue';
import { ArrowRightIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline';
import { usePage } from "@inertiajs/vue3";

const user = usePage().props.auth.user;

defineProps<{
    canLogin?: boolean;
}>();


</script>
