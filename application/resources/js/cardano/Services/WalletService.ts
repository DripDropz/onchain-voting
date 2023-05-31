// @ts-nocheck
import {Blockfrost, Lucid, Network, Tx} from 'lucid-cardano';
import BlockfrostKeysService from './BlockfrostKeysService';
import CardanoWallet from "@/cardano/interface/Wallets";

export {};
declare global {
    interface Window {
        cardano: CardanoWallet;
    }
}



export default class WalletService {
    private api: any;
    private lucid: any;
    private poolId: any;
    private blockfrostUrl: any;
    private projectId: any;

    constructor() {}

    public get lucidInstance()
    {
        return this.lucid;
    }

    public get apiInstance()
    {
        return this.api;
    }

    public getProviderUrl() {
        return this.blockfrostUrl;
    }

    public supports(wallet: string): boolean {
        if (typeof window.cardano === 'undefined') {
            return false;
        }
        return !!window.cardano[wallet];
    }

    public async getStakeAddress(wallet: string = null) {
        if (!!wallet) {
            await this.init(wallet);
        }

        if (!this.api) {
            return;
        }

        return <string>await this.lucid.wallet.rewardAddress();
    }

    public async getParams(wallet: string) {
        await this.init(wallet);

        if (!this.api) {
            return;
        }
        return <string>await this.lucid.wallet.rewardAddress();
    }

    public async getAddress() {
        return <string>await this.lucid.wallet.address();
    }

    public async signMessage(wallet: string, msg: string) {
        await this.init(wallet);
        if (!this.api) {
            return;
        }
        const addresses = await this.api.getRewardAddresses();
        return <string>await this.api.signData(addresses[0], msg);
    }

    public async connectWallet(wallet: string) {
        try {
            if (!this.lucid || typeof this.lucid === 'undefined') {
                await this.init(wallet);
            } else {
                const api = await this.enableWallet(wallet);
                this.lucid.selectWallet(api);
                this.api = api;
            }
        } catch (e) {
            console.log({e});
        }
    }

    protected async enableWallet(wallet: string) {
        if (typeof window.cardano === 'undefined' || !window?.cardano || !window.cardano[wallet]) {
            return Promise.reject(`${wallet} wallet not installed.`);
        }
        console.log(`${wallet} enabled.`);
        return window.cardano[wallet]?.enable();
    }

    protected async init(wallet: string) {
        if (!!this.lucid || typeof this.lucid !== 'undefined') {
            const api = await this.enableWallet(wallet);
            this.lucid.selectWallet(api);
            this.api = api;

            return;
        }
        let lucid;

        try {
            const api = await this.enableWallet(wallet);

            if (!api) {
                console.log(`${wallet} not installed!`);
                return;
            }

            const networkId = await  api.getNetworkId();
            let network;
            switch (networkId) {
                case 0:
                    network = 'Preview';
                    break;
                default:
                    network = 'Mainnet';
            }
            const blockfrostKeysService = new BlockfrostKeysService();
            const keys = await blockfrostKeysService.getConfig();
            this.blockfrostUrl = keys?.blockfrostUrl;
            this.projectId = keys?.projectId

            lucid = await Lucid.new(null,
                new Blockfrost(this.blockfrostUrl, this.projectId),
                network
            );

            lucid = lucid.selectWallet(api);
            this.lucid = lucid;
            this.poolId = keys.poolId;
            this.api = api;
        } catch (e) {
            throw  e;
        }
    }
}
