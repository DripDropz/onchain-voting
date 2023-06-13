<template>
    <div class="mt-3">
        <a v-show="walletData?.name"
           href="#"
           @click="loginUser"
           class="flex items-center justify-center w-3/4 gap-3 px-4 py-2 mx-auto text-lg font-medium text-teal-800 rounded-lg shadow bg-slate-200 xl:text-xl 2xl:text-2xl ">
            <WalletLogo :wallet="walletData" />
            <span class="">Sign in with <span v-text="walletData?.name"></span></span>
        </a>

        <div v-show="!walletData?.name"
             class="flex items-center justify-center w-full gap-3 px-4 py-2 mx-auto font-medium xl:text-xl 2xl:text-2xl">
            <CardanoWallet />
        </div>
    </div>

</template>

<script lang="ts" setup>
import CardanoWallet from './ConnectWallet.vue';
import { useWalletStore } from '../stores/wallet-store';
import { storeToRefs } from 'pinia';
import WalletLogo from './WalletLogo.vue';
import Wallet from '../models/wallet-data';


const walletStore = useWalletStore();
const {walletData} = storeToRefs(walletStore);

const emit = defineEmits<{(e: 'signUser', userdata:Wallet):void}>()
    

let loginUser = async () => {
    emit('signUser',walletData.value )
}


</script>