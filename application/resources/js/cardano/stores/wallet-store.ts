import {defineStore} from 'pinia';
import {ref, Ref} from 'vue';
import Wallet from '../models/wallet-data';
import { useStorage } from '@vueuse/core';

interface WalletState {
    walletData: Ref<Wallet|null>;
    walletName: Ref<string|null>;
    saveWallet: (wallet: Wallet) => void;
    disconnect: () => void;
}

export const useWalletStore = defineStore('wallet', (): WalletState => {
    let walletData:Ref<Wallet|null> = ref(null);
    let walletName: Ref<string|null> = useStorage('wallet-name', '', localStorage, {mergeDefaults: true});

    function saveWallet(wallet: Wallet) {
        walletData.value = wallet;
        walletName.value = wallet?.name ?? '';
    }

    function disconnect() {
       walletData.value = null;
       walletName.value = null;
    }

    return {
        walletData,
        walletName,
        saveWallet,
        disconnect
    };
});
