<template>
    <div class="w-full">
        <div class="flex flex-row">
            <button @click.prevent="open = !open" :aria-expanded="open"
                class="flex flex-row justify-center w-full p-1.5 " :theme="'primary2'"
                :class="[backgroundColor, textColor, !!walletData?.address?'rounded-l-md':'rounded-md']" type="button">
                <!-- <slot></slot> -->
                <span v-show="walletLoading"
                    class="flex items-center justify-center p-1 mr-1 bg-white rounded-full bg-opacity-90">
                    <svg aria-hidden="true"
                        class="inline w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-sky-600"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>
                </span>
                <span class="flex items-center gap-2 tracking-wide" v-show="walletData?.address && !walletLoading">
                    <span :class="[textColor]" v-text="(lastFiveCharacters) + ' connected'"
                        class="  h-full border-primary-200 border-opacity-50 p-0.5 capitalize text-sm">
                    </span>

                </span>

                <span class="flex gap-2 text-sm tracking-wide" v-show="!walletData?.address && !walletLoading">
                    <span>Connect Your Wallet</span>
                    <span :class="[textColor]" aria-hidden="true">&darr;</span>
                </span>
                <span class="flex gap-2 tracking-wide" v-show="walletLoading">
                    <span :class="[textColor]">Connecting</span>
                    <span :class="[textColor]" aria-hidden="true">&darr;</span>
                </span>
            </button>
            <button @click.prevent="disconnectWallet" class="bg-white rounded-r-md" v-if="!!walletData?.address">
                <XMarkIcon class="w-5 h-5" aria-hidden="true" :class="[textColor]" />
            </button>
        </div>


        <div v-show="open" style="display: none;" ref="target"
            class="absolute z-20 justify-center w-full overflow-visible rounded-b-lg shadow-md bg-sky-100 max-w-64">
            <div v-if="WalletList.length === 0" class="px-4 py-2 text-center text-gray-700">
                No wallets found
            </div>
            <div v-else class="flex flex-col items-center gap-2 py-1 divide-y divide-slate-800 divide-opacity-40"
                role="none">
                <a v-for="wallet in WalletList" href="#"
                    @click.prevent="(open = !open); walletService.supports(wallet?.name) ? enableWallet(wallet?.name) : ''"
                    class="inline-flex w-full gap-2 px-4 py-2 text-xl text-gray-700"
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
import WalletService from '../Services/wallet-service';
import Wallet from '../models/wallet-data'
import { computed, ref, Ref } from "vue";
import { useWalletStore } from "../stores/wallet-store"
import { storeToRefs } from 'pinia';
import { onClickOutside } from '@vueuse/core';
import { XMarkIcon } from "@heroicons/vue/20/solid";
import AlertService from '@/shared/Services/alert-service';
import PrimaryButton from '@/Components/PrimaryButton.vue';


const props = withDefaults(
    defineProps<{
        backgroundColor?: string,
    }>(),
    {
        backgroundColor: '',
    });

let open: Ref<boolean> = ref(false);
let walletLoading = ref(false);

let textColor = computed(() => props.backgroundColor == 'bg-white' ? 'text-sky-500' : 'text-white')

// let backgroundColor = ref(props.backgroundColor);
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

if (walletName.value?.length > 0) {
    enableWallet(walletName.value)
}

const target = ref(null);

onClickOutside(target, (event) => open.value = false);

const lastFiveCharacters = computed(() => {
    return walletData.value.stakeAddress?.slice(-5);
})

function disconnectWallet() {
    walletStore.disconnect();
    if (!walletData.value.address) {
        AlertService.show(["Wallet disconnected successfully."], "success");
    }
}
</script>
