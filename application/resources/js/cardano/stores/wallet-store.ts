import {defineStore} from 'pinia';
import {ref, Ref} from 'vue';
import Wallet from '../models/wallet-data';
import { useStorage } from '@vueuse/core';

interface WalletState {
    walletData: Ref<Wallet>;
    walletName: Ref<string>;
    saveWallet: (wallet: Wallet) => void;
    disconnect: () => void;
}

export const useWalletStore = defineStore('wallet', (): WalletState => {
    let walletData:Ref<Wallet> = ref({} as Wallet);
    let walletName: Ref<string> = useStorage('wallet-name', '', localStorage, {mergeDefaults: true});

    function saveWallet(wallet: Wallet) {
        walletData.value = wallet;
        walletName.value = wallet?.name ?? '';
    }

    function disconnect() {
       walletData.value = {} as Wallet;
       walletName.value = '';
    }

    return {
        walletData,
        walletName,
        saveWallet,
        disconnect
    };
});
