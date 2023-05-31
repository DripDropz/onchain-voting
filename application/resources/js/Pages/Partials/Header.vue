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
            <div class="flex "  v-if="canLogin">
    <!--            <Link-->
    <!--                v-if="$page.props.auth.user"-->
    <!--                :href="route('admin.dashboard')"-->
    <!--                class="ocv-link"-->
    <!--            >Dashboard-->
    <!--            </Link>-->


                <ConnectWallet></ConnectWallet>
            </div>
            <DarkModeButton />
        </div>
    </div>
</template>
<script lang="ts" setup>

import {Head, Link} from '@inertiajs/vue3';
import ConnectWallet from "@/cardano/Components/ConnectWallet.vue";
import { useDarkModeStore } from "@/stores/dark-mode-store";
import DarkModeButton from '@/shared/components/DarkModeButton.vue';

defineProps<{
    canLogin?: boolean;
}>();


</script>
