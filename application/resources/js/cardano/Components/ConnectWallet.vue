<template>
    <div>
        <button
            @click.prevent="open=!open"
            :aria-expanded="open"
            class="p-0 border-none"
            type="button">
            <slot></slot>
                <span class="relative inline-flex justify-between gap-2 px-2 py-2 font-semibold text-white menu-link" :class="[{'rounded-t-lg': open, 'rounded-lg': !open}, backgroundColor]">
                    <span
                        v-show="walletLoading"
                                class="flex items-center justify-center w-4 p-1 bg-white rounded-full bg-opacity-90">
                        <svg
                            class="relative w-3 h-3 border-t-2 border-b-2 rounded-full animate-spin border-primary-600"
                            viewBox="0 0 24 24"></svg>
                    </span>
                </span>



            <span class="flex gap-2 tracking-wide" v-show="!walletData?.address">
                <span>Connect Your Wallet</span>
                <span class="text-slate-100" aria-hidden="true">&darr;</span>
            </span>
        </button>

        <div
            v-show="open"
            style="display: none;"
            ref="target"
            class="absolute z-40 w-48 mt-3 overflow-visible bg-white rounded-bl-sm rounded-br-sm shadow-md">
            <div class="flex flex-col items-center gap-2 py-1 divide-y divide-slate-800 divide-opacity-40" role="none">
                <a v-for="wallet in WalletList" href="#" @click.prevent="(open = !open); walletService.supports(wallet?.name) ? enableWallet(wallet?.name) : ''"
                   class="inline-flex block w-full gap-2 px-4 py-2 text-xl text-gray-700"
                   :class="{'hidden' : !walletService.supports(wallet?.name)}"
                   role="menuitem"
                   :disabled="!walletService.supports(wallet?.name)"
                   tabindex="-1">
                    <img :alt="wallet?.altText" class="w-6 h-auto"
                         :src="wallet?.imageSrc"/>
                    <span>{{wallet?.walletName}}</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { WalletList } from '../utils/wallet-list';
import WalletService from '../Services/WalletService';
import Wallet from '../models/wallet-data'
import { ref, Ref } from "vue";
import { useWalletStore } from "../stores/wallet-store"
import { storeToRefs } from 'pinia';

const props = withDefaults(
    defineProps<{
        backgroundColor?: string,
    }>(),
    {
        backgroundColor: 'bg-indigo-800'
    });

let open: Ref<boolean> = ref(false);
let walletLoading = ref(false);

let backgroundColor = ref(props.backgroundColor);
const walletStore = useWalletStore();
const { walletData } = storeToRefs(walletStore);
const {walletName} = storeToRefs(walletStore);

const walletService = new WalletService()

async function enableWallet(wallet: string) {
    walletLoading.value = true;
    let walletData = {
        name: wallet
    } as Wallet;
    try {
        await walletService.connectWallet(wallet);
        walletData = {
            ...walletData,
            ...await getWalletAddress(),
        }

        walletStore.saveWallet(walletData);
        walletLoading.value = false;
    } catch (e) {
        console.log(e);
    }
}

async function getWalletAddress(): Promise<Wallet> {
    return {
        address: await walletService.getAddress(),
        stakeAddress: await walletService.getStakeAddress()
    } as Wallet
}

if(walletName.value){
    enableWallet(walletName.value)
}

</script>
