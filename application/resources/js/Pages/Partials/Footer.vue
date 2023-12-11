<template>
    <footer>
        <section class="container flex flex-row items-center justify-between w-full py-8">
            <div :class="{ 'w-5/6': !$page.props.auth.user }">
                <nav>
                    <ul class="flex gap-4 text-lg flex-nowrap">
                        <li class="w-auto ocv-link">
                            <Link :href="route('home')">
                                Home
                            </Link>
                        </li>

                        <li class="w-auto ocv-link">
                            <a target="_blank" href="https://github.com/DripDropz/onchain-voting/tree/main/docs">
                                Documentation
                            </a>
                        </li>

                        <li class="w-auto ocv-link">
                            <a target="_blank" href="//github.com/DripDropz/onchain-voting">
                                Github
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="flex flex-row items-center justify-end w-1/3 gap-6 text-white">
                <div class="relative flex items-center gap-0 py-0.5 bg-indigo-700 rounded-lg flex-nowrap hover:bg-indigo-700">
                    <Link preserve-state v-if="user?.hash" href="#" @click.prevent="logout"
                        class="flex items-center h-full gap-2 px-3 py-2 mx-1 bg-indigo-800 rounded-lg hover:bg-indigo-950">
                        <p>Logout</p>
                        <ArrowRightOnRectangleIcon class="w-5 h-5"></ArrowRightOnRectangleIcon>
                    </Link>
                </div>

                <DarkModeButton />
            </div>
        </section>
        <section class="text-indigo-100 dark:bg-indigo-950">
            <div class="container flex justify-between py-2 text-xs">
                <div v-if="config.show_created_by">
                    Created with ♡ by <a href="https://dripdropz.io/" target="_blank" >DripDropz</a>
                </div>
                <div>
                    Hosted with ♡ by  <a href="https://www.lidonation.com/" target="_blank" >Lidonation</a>
                </div>
            </div>
        </section>
    </footer>
</template>
<script lang="ts" setup>
import { Link, router } from '@inertiajs/vue3';
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
