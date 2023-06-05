<template>
    <div>
        <button @click.prevent="open = !open" :aria-expanded="open" class="flex flex-row p-2 border-none rounded-md"
        :class="[backgroundColor]"
            type="button">
            <slot></slot>
            <span v-show="walletLoading"
                class="flex items-center justify-center w-4 p-1 mt-1 mr-1 bg-white rounded-full bg-opacity-90">
                <svg aria-hidden="true" 
                    class="w-3 h-3 text-gray-200 animate-spin dark:text-gray-400 fill-indigo-800" viewBox="0 0 100 101" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor" />
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill" />
                </svg>
            </span>
            <span class="flex items-center gap-2 tracking-wide" v-show="walletData?.address && !walletLoading">
                <span v-text="(walletData?.name) + ' connected'"
                    class=" text-slate-200 h-full border-primary-200 border-opacity-50 p-0.5 capitalize">
                </span>
            </span>

            <span class="flex gap-2 tracking-wide" v-show="!walletData?.address && !walletLoading">
                <span>Connect Your Wallet</span>
                <span class="text-slate-100" aria-hidden="true">&darr;</span>
            </span>
            <span class="flex gap-2 tracking-wide" v-show="walletLoading">
                <span>Connecting</span>
                <span class="text-slate-100" aria-hidden="true">&darr;</span>
            </span>
        </button>

        <div v-show="open" style="display: none;" ref="target"
            class="absolute z-40 justify-center mt-3 overflow-visible bg-white rounded-bl-sm rounded-br-sm shadow-md w-36 ">
            <div class="flex flex-col items-center gap-2 py-1 divide-y divide-slate-800 divide-opacity-40" role="none">
                <a v-for="wallet in WalletList" href="#"
                    @click.prevent="(open = !open); walletService.supports(wallet?.name) ? enableWallet(wallet?.name) : ''"
                    class="inline-flex block w-full gap-2 px-4 py-2 text-xl text-gray-700"
                    :class="{ 'hidden': !walletService.supports(wallet?.name) }" role="menuitem">
                    <img :alt="wallet?.altText" class="w-6 h-auto" :src="wallet?.imageSrc" />
                    <span>{{ wallet?.walletName }}</span>
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
import {onClickOutside} from '@vueuse/core';


const props = withDefaults(
    defineProps<{
        backgroundColor?: string,
    }>(),
    {
        backgroundColor: 'bg-indigo-600'
    });

let open: Ref<boolean> = ref(false);
let walletLoading = ref(false);

let backgroundColor = ref(props.backgroundColor);
const walletStore = useWalletStore();
const { walletData } = storeToRefs(walletStore);
const { walletName } = storeToRefs(walletStore);

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

if (walletName.value) {
    enableWallet(walletName.value)
}

const target = ref(null);
onClickOutside(target, (event) => open.value = false);

</script>
