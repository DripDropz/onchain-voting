import { C, fromHex, fromText, getAddressDetails, toHex } from '@lucid-cardano';
import WalletService from './wallet-service';
import AlertService from '@/shared/Services/alert-service';


export async function walletMsgLogin(wallet: string , stakeAddr: string, redirectRoute:string|null = null, data = {}):Promise<{}> {
    try {
        const messageHex = fromText('Voter Login')
        const signature = await (new WalletService())
            .signMessage(wallet, messageHex) as {};

        //get stakeAddr hex for comparison in backend
        const { address: { hex: hexAddress } } = getAddressDetails(stakeAddr);
        await window.axios.post(route("login.signMessageLogin"), {
            ...signature,
            stakeAddrHex: hexAddress,
            stakeAddr: stakeAddr,
            redirect: redirectRoute,
            ...data,
        });
        return signature;
    } catch (error: any) {
        AlertService.show([error.message], 'error')
    }
}

export async function txLogin(wallet: string , stakeAddr:string, redirectRoute:string|null = null, data = {}) {
    try {
        const rawTx = await(new WalletService()).expiredTx(
            wallet,
            { lovelace: BigInt(0) },
            stakeAddr
        );
        const signedTx = (await rawTx?.sign().complete())?.toString();


        const res = await window.axios.post(route("login.signTxLogin"), {
            txHash: signedTx,
            stakeAddr: stakeAddr,
            redirect: redirectRoute,
            ...data,
        });
    } catch (error: any) {
        AlertService.show([error.message], 'error')
    }
}
