// @ts-nocheck
import {Blockfrost, Lucid, Tx, toHex, C} from '@lucid-cardano';
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
    
    private static instances: Map<string, WalletService> = new Map();

    constructor(walletName?: string) {
        this.walletName = walletName;
    }

    public static getInstance(walletName?: string): WalletService {
        const key = walletName || 'default';
        if (!WalletService.instances.has(key)) {
            WalletService.instances.set(key, new WalletService(walletName));
        }
        return WalletService.instances.get(key);
    }

    public async lucidInstance() {
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

    public async expiredTx(wallet, assets, stakeAddr) {
        await this.init(wallet);
        if (!this.api) {
            return;
        }

        const addr = await this.getAddress(wallet)
        return await new Tx(this.lucid).payToAddress(addr, assets).validTo(Date.now() - 1000000).addSigner(stakeAddr).complete();
    }

    public async addressFromHexOrBech32(address: string) {
        try {
            return C.Address.from_bytes(fromHex(address));
        } catch (_e) {
            try {
                return C.Address.from_bech32(address);
            } catch (_e) {
                throw new Error("Could not deserialize address.");
            }
        }
    }

    public async connectWallet(wallet: string) {
        try {
            if (!this.lucid || typeof this.lucid === 'undefined') {
                await this.init(wallet);
            } else {
                // Wallet already connected, just switch to new wallet if different
                const api = await this.enableWallet(wallet);
                this.lucid.selectWallet(api);
                this.api = api;
            }
        } catch (e) {
            throw e;
        }
    }

    protected async enableWallet(wallet: string) {
        if (typeof window.cardano === 'undefined' || !window?.cardano || !window.cardano[wallet]) {
            return Promise.reject(`${wallet} wallet not installed.`);
        }
        return window.cardano[wallet]?.enable();
    }

    protected async init(wallet?: string) {
        const _wallet = wallet || this.walletName;
        
        if (this.lucid && typeof this.lucid !== "undefined") {
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
            
            // CIP-30 networkId: 0 = Testnet (Preprod/Preview), 1 = Mainnet
            // envNetworkId from config: 0 = testnet, 1 = mainnet
            const envNet = parseInt(envNetworkId);
            
            if (envNet === 0) {
                // Testnet configured (Preprod/Preview)
                if (networkId !== 0) {
                    AlertService.show(["Testnet wallet needed (Preprod/Preview)"], 'error')
                    throw new Error("Testnet wallet needed");
                }
                network = "Preprod";
            } else if (envNet === 1) {
                // Mainnet configured
                if (networkId !== 1) {
                    AlertService.show(["Mainnet wallet needed"], 'error')
                    throw new Error("Mainnet wallet needed");
                }
                network = "Mainnet";
            } else if (envNet === 42) {
                // Custom testnet
                if (networkId !== 42) {
                    AlertService.show(["Custom testnet wallet needed"], 'error')
                    throw new Error("Custom testnet wallet needed");
                }
                network = "Custom";
            } else {
                // Unknown config - accept whatever the wallet is on
                network = networkId === 1 ? "Mainnet" : "Preprod";
            }

            lucid = await Lucid.new(
                new Blockfrost(`${appUrl}/api/query`),
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
