<template>
    <ModalRoute>
        <div>
            <div class="flex flex-col items-center w-full gap-6 p-6 text-center dark:bg-sky-900">
                <h1 class="text-2xl font-semibold lg:text-3xl 2xl:text-5xl 3xl:text-6xl text-slate-700 dark:text-white">
                    Login
                </h1>

                <div class="output">Sign in with <span v-if="signTx">a hardware</span> wallet </div>

                <div class="w-auto flex-0" >
                    <WalletloginBtn
                            :class="{'pointer-events-none opacity-50': loading}"
                            background-color="bg-slate-900"
                            @signUser="signUser()"
                            :text="signTx?'Sign expired tx with':'Sign message with'" />
                </div>

                <div role="status" :class="{'invisible': !loading}">
                    <svg aria-hidden="true" class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-sky-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>

                <Toggle v-model="signTx"
                        offLabel="Use hardware wallet"
                        onLabel="Use hot wallet"
                        :classes="{
                            container: 'inline-block rounded-xl outline-none focus:ring focus:ring-sky-500 focus:ring-opacity-40 w-48',
                            toggle: 'flex w-full h-6 rounded-xl relative cursor-pointer transition items-center box-content border-0 text-xs lg:text-sm xl:text-md leading-none',
                            toggleOn: 'bg-sky-600 border-sky-600 justify-start font-semibold text-white',
                            toggleOff: 'bg-sky-300 dark:bg-slate-200 border-slate-200 justify-end font-semibold text-slate-700',
                            handle: 'inline-block bg-sky-100 dark:bg-sky-400 w-6 h-6 top-0 rounded-xl absolute transition-all',
                            handleOn: 'left-full transform -translate-x-full',
                            handleOff: 'left-0',
                            handleOnDisabled: 'bg-slate-100 left-full transform -translate-x-full',
                            handleOffDisabled: 'bg-slate-100 left-0',
                            label: 'text-center w-auto px-3 border-box whitespace-nowrap select-none',
                        }"
                />

                <p class="mt-2 text-xs" :class="{'invisible': !signTx}">
                    <span>When you sign in with a hardware, we have you sign an expired transaction to verify your signature.
                        Because the tx is expired, it cannot be submitted  to the blockchain.
                     </span>
                </p>
            </div>
        </div>
    </ModalRoute>
</template>

<script lang="ts" setup>
import Toggle from '@vueform/toggle';
import { ref } from 'vue';
import ModalRoute from '@/Components/ModalRoute.vue';
import WalletloginBtn from '@/cardano/Components/WalletloginBtn.vue';
import { walletMsgLogin } from '@/cardano/Services/WalletLoginService';
import { txLogin } from '@/cardano/Services/WalletLoginService';
import { useModal } from "momentum-modal";
import { router } from '@inertiajs/vue3';
import { useWalletStore } from '@/cardano/stores/wallet-store';
import {storeToRefs} from 'pinia'

const props = defineProps<{
    baseUrl: string;
}>();

const walletStore = useWalletStore();
const { walletData } = storeToRefs(walletStore);
let loading = ref(false);

const { close } = useModal();
let signTx = ref(false)

let signUser = async () => {
    loading.value = true;
    if (signTx.value) {
        await txLogin(walletData.value?.name, walletData.value?.stakeAddress, props.baseUrl);
        close()
        router.visit(props?.baseUrl, {
            preserveScroll: true,
        });
    } else {
        await walletMsgLogin(walletData.value?.name,walletData.value?.stakeAddress, props.baseUrl);
        close()
        router.visit(props?.baseUrl, {
            preserveScroll: true,
        });
    }
}
</script>
