// @ts-nocheck
import {Blockfrost, Lucid, Tx, toHex,C} from '@lucid-cardano';
import BlockfrostKeysService from './BlockfrostKeysService';
import CardanoWallet from "@/cardano/interface/Wallets";
import AlertService from '@/shared/Services/alert-service';

export {};
declare global {
    interface Window {
        cardano: CardanoWallet;
    }
}

export default class WalletService {
    private api: any;
    private lucid: Lucid;
    private walletName: string;

    constructor(walletName?: string) {
        this.walletName = walletName;
    }

    public async lucidInstance()
    {
        if (!this.lucid || typeof this.lucid === 'undefined') {
            await this.init();
        }
        return this.lucid;
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

    public async getAddress() {
        return <string>await this.lucid?.wallet?.address();
    }

    public async signMessage(wallet: string, msg: string) {
        await this.init(wallet);
        if (!this.api) {
            return;
        }
        const addresses = await this.api.getRewardAddresses();
        return <string>await this.api.signData(addresses[0], msg);
    }

    public async expiredTx(wallet, assets , stakeAddr)
    {
        await this.init(wallet);
        if (!this.api) {
            return;
        }

        const addr = await this.getAddress(wallet)
        return await new Tx(this.lucid).payToAddress(addr,assets).validTo(Date.now() - 1000000).addSigner(stakeAddr).complete();
    }

    public async addressFromHexOrBech32(address: string) {
        try {
            return C.Address.from_bytes(fromHex(address));
        }
        catch (_e) {
            try {
                return C.Address.from_bech32(address);
            }
            catch (_e) {
                throw new Error("Could not deserialize address.");
            }
    }}

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

    protected async init(wallet?: string) {
        const _wallet = wallet || this.walletName;
        if (!!this.lucid || typeof this.lucid !== "undefined") {
            const api = await this.enableWallet(_wallet);
            this.lucid.selectWallet(api);
            this.api = api;
            return;
        }
        let lucid;

        try {
            const api = await this.enableWallet(_wallet);

            if (!api) {
                AlertService.show([`${_wallet} not installed!`], 'error')
                throw new Error(`${_wallet} not installed!`);
            }

            const networkId = await api.getNetworkId();
            const keys = await (new BlockfrostKeysService).getConfig();
            const envNetworkId = keys?.network_id;
            const appUrl = keys?.app_url
            let network;
            switch (envNetworkId) {
                case "0":
                    if (networkId !== 0) {
                        AlertService.show(["Preview wallet needed"], 'error')
                        throw new Error("Preview wallet needed");
                    }
                    network = "Preview";
                    break;

                case "1":
                    if (networkId !== 1) {
                        AlertService.show(["Mainnet wallet needed"], 'error')
                        throw new Error("Mainnet wallet needed");
                    }
                    network = "Mainnet";
                    break;
                default:
                    AlertService.show(["Invalid network"], 'error')
                    throw new Error("Invalid network");
            }

            lucid = await Lucid.new(
                new Blockfrost(`${appUrl}/api`),
                network
            );

            lucid = lucid.selectWallet(api);
            this.lucid = lucid;
            this.poolId = keys.poolId;
            this.api = api;
        } catch (e) {
            throw e;

        }
    }
}
