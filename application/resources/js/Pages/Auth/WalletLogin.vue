<template>
    <ModalRoute>
        <div>
            <div class="flex flex-col items-center w-full gap-6 p-6 text-center dark:bg-sky-900">
                <h1 class="text-2xl font-semibold lg:text-3xl 2xl:text-5xl 3xl:text-6xl text-slate-700 dark:text-white">
                    Login
                </h1>

                <div class="output">Sign in with <span v-if="signTx">a hardware</span> wallet </div>

                <div class="w-auto mb-6 flex-0" >
                    <WalletloginBtn
                            background-color="bg-slate-900"
                            @signUser="signUser()"
                            :text="signTx?'Sign expired tx with':'Sign message with'" />
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

const { close } = useModal();
let signTx = ref(false)

let signUser = async () => {
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
