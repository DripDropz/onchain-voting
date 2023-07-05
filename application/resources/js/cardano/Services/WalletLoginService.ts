import { C, fromHex, fromText, getAddressDetails, toHex } from 'lucid-cardano';
import WalletService from './wallet-service';


export async function walletMsgLogin(wallet: string , stakeAddr: string, redirectRoute:string|null = null, data = {}):Promise<{}> {
    const messageHex = fromText('Voter Login')
    const signature = await (new WalletService())
        .signMessage(wallet, messageHex) as {};

    //get stakeAddr hex for comparison in backend
    const { address: { hex: hexAddress } } = getAddressDetails(stakeAddr);
    const res = await window.axios.post(route("login.signMessageLogin"), {
        ...signature,
        stakeAddrHex: hexAddress,
        stakeAddr: stakeAddr,
        redirect: redirectRoute,
        ...data,
    });
    return signature;
}

export async function txLogin(wallet: string , stakeAddr:string, redirectRoute:string|null = null, data = {}) {
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
}