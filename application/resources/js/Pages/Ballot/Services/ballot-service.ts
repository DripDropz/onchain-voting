import axios from "axios";
import WalletService from "@/cardano/Services/wallet-service";
import { useWalletStore } from "@/cardano/stores/wallet-store";
import {Unit, toText} from "lucid-cardano";

export default class BallotService {
    static async register(ballotHash: string) {
        try {
            // get connected wallet form the store
            const walletStore = useWalletStore();
            const {walletName, walletData} = walletStore;

            // get lucid instance
            const ws = new WalletService(walletName);

            // build mint tx with lucid
            const lucid = await ws.lucidInstance();

            // lucid.selectWalletFromSeed(request?.body?.seed);
            // const mintingPolicy = await this.getPolicy(lucid);
            // const policyId: PolicyId = lucid.utils.mintingPolicyToId(mintingPolicy);


            // const unit: Unit = policyId + toText('Registration Token 1');

            // const tx = await lucid
            // .newTx()
            // .payToAddress(walletData.address, {lovelace: BigInt(2000000), [unit]: 1n })
            // .mintAssets({ [unit]: 1n } )
            // .validTo(Date.now() + 100000 )
            // .attachMetadata(721, {[policyId]: { [nft.key]: nft.metadata}})
            // .complete();

            // get user user witness

            // send to backend for minter witness and submission
            console.log({lucid});
            let response = await axios.post(route("ballot.register.store", { ballot: ballotHash }));
            return response.data;
        } catch (error) {
            console.log(error);
        }
    }
}
