<template>
    <ModalRoute>
        <div>
            <div class="p-3">
                <h3>Toggle with custom labels</h3>
                <div class="output">Data: {{ example4 }}</div>
                <Toggle v-model="example4" 
                        onLabel="Funded proposals "
                        offLabel="All proposals"
                        :classes="{
                            container: 'inline-block rounded-xl outline-none focus:ring focus:ring-indigo-700 focus:ring-opacity-30 w-32',
                            toggle: 'flex w-full h-4 rounded-xl relative cursor-pointer transition items-center box-content border-2 text-xs leading-none',
                            toggleOn: 'bg-indigo-500 border-indigo-500 justify-start font-semibold text-white',
                            toggleOff: 'bg-slate-200 border-slate-200 justify-end font-semibold text-slate-700',
                            handle: 'inline-block bg-white w-4 h-4 top-0 rounded-xl absolute transition-all',
                            handleOn: 'left-full transform -translate-x-full',
                            handleOff: 'left-0',
                            handleOnDisabled: 'bg-slate-100 left-full transform -translate-x-full',
                            handleOffDisabled: 'bg-slate-100 left-0',
                            label: 'text-center w-auto px-2 border-box whitespace-nowrap select-none',

                        }"
                        />
            </div>
            
            <div class="p-2">
                <WalletloginBtn @signUser="signUser($event)"/>
            </div>
        </div>
    </ModalRoute>
</template>

<script lang="ts" setup>
import Toggle from '@vueform/toggle';
import { ref } from 'vue';
import ModalRoute from '@/Components/ModalRoute.vue';
import WalletloginBtn from '@/cardano/Components/WalletloginBtn.vue';
import Wallet from '@/cardano/models/wallet-data';
import { walletMsgLogin } from '@/cardano/Services/WalletLoginService';

const props = defineProps<{
    baseUrl?: string;
}>();

let example4 = ref(false)

let signUser = (userData:Wallet) => {
    let res = walletMsgLogin(userData?.name,userData?.stakeAddress,props.baseUrl)
}

</script>