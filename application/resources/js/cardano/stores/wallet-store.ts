import { defineStore } from 'pinia';
import { ref, Ref } from 'vue';
import Wallet from '../models/wallet-data';
import { useStorage } from '@vueuse/core';
import WalletService from '../Services/wallet-service';
import { Lucid } from '@lucid-cardano';


interface WalletState {
    walletData: Ref<Wallet>;
    walletName: Ref<string>;
    saveWallet: (wallet: Wallet) => void;
    disconnect: () => void;
    getLucidInstance: () => Promise<Lucid>; 
}

export const useWalletStore = defineStore('wallet', (): WalletState => {
    const walletData: Ref<Wallet> = ref({} as Wallet);
    const walletName: Ref<string> = useStorage('wallet-name', '', localStorage, { mergeDefaults: true });
    getLucidInstance: () => Promise<Lucid>;

    function saveWallet(wallet: Wallet) {
        walletData.value = wallet;
        walletName.value = wallet?.name ?? '';


    }

    function disconnect() {
        walletData.value = {} as Wallet;
        walletName.value = '';
    }

    async function getLucidInstance() {
        const ws = new WalletService(walletName.value);
        return await ws.lucidInstance();
    }

    return {
        walletData,
        walletName,
        saveWallet,
        disconnect,
        getLucidInstance
    };
});
