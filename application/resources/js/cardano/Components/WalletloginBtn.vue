<template>
    <div class="mt-3">
        <a v-show="walletData?.name"
           href="#"
           @click.prevent="loginUser()"
           class="flex items-center justify-center gap-3 px-4 py-2 mx-auto text-sm font-medium text-white rounded-lg shadow bg-sky-700 xl:text-md 2xl:text-lg ">

           <WalletLogo :wallet="walletData" />

            <span class="">{{ text }} <span v-text="walletData?.name"></span></span>
        </a>

        <div v-show="!walletData?.name"
             class="flex items-center justify-center w-full gap-3 px-4 py-2 mx-auto font-medium xl:text-xl 2xl:text-2xl">
            <CardanoWallet :background-color="backgroundColor" />
        </div>
    </div>

</template>

<script lang="ts" setup>
import CardanoWallet from './ConnectWallet.vue';
import { useWalletStore } from '../stores/wallet-store';
import { storeToRefs } from 'pinia';
import WalletLogo from './WalletLogo.vue';

withDefaults(
    defineProps<{
    backgroundColor?: string,
    text: string
}>(),
{
    backgroundColor: '',
    text:'Sign in with'
})

const walletStore = useWalletStore();
const {walletData} = storeToRefs(walletStore);

const emit = defineEmits<{(e: 'signUser'):void}>()

let loginUser = async () => {
    emit('signUser' )
}
</script>
