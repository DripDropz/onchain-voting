import { getAddressDetails } from 'lucid-cardano';
import WalletService from '../Services/WalletService';


export async function walletMsgLogin(wallet: string , stakeAddress: string, redirectRoute:string|null = null, data = {}) {
    const encoder = new TextEncoder();
    const messageHex = Array.from(encoder.encode('Voter Login'))
        .map((byte) => byte.toString(16).padStart(2, '0'))
        .join('');
    const signature = await (new WalletService())
        .signMessage(wallet, messageHex) as {};

    //get stakeAddr hex for comparison in backend  
    const { address: { hex: hexAddress } } = getAddressDetails(stakeAddress);

    const res = await window.axios.post(route('walletLogin'), {
        ...signature,
        accountHex: hexAddress,
        stakeAddr: stakeAddress,
        redirect: redirectRoute,
        ...data
    });
    return signature;
}

export async function txLogin(wallet: string , stakeAddress: string, redirectRoute:string|null = null, data = {}) {
    
}