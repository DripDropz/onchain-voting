import axios from "axios";
import WalletService from "@/cardano/Services/wallet-service";
import { useWalletStore } from "@/cardano/stores/wallet-store";
import { C, Lucid, UTxO, Unit, fromHex, toHex, toText } from "lucid-cardano";
import { useVoterStore } from "@/Pages/Voter/stores/voter-store";

export default class BallotService {
    protected static lucid: Lucid;

    static async loadVotingPower(voterId: string, ballotHash: string) {
        try {
            let response = await axios.get(
                route("voters.power", { voterId, ballot: ballotHash })
            );
            return response.data;
        } catch (error: any) {
            if(error.response.status = 404){
                return 0;
            };
        }
    }

    static async saveBallotResponse(
        voterId: string,
        choices: string[],
        ballot: string
    ) {
        try {
            return await axios.post(
                route("voters.ballot-responses.save", { voterId }),
                { choices, ballot}
            );
        } catch (error) {
            console.log(error);
        }
    }

    static async submitVote(ballotHash: string) {
        try {
            // collect registration token
            const voterStore = useVoterStore();
            const registration = voterStore.ballotRegistration(ballotHash);
            const lucid = await BallotService.getLucidInstance();

            if (!lucid)
                return

            let utxos = (await lucid.wallet.getUtxos()).splice(0, 1);

            if (utxos.length > 1) {
                utxos = utxos.filter((utxo: UTxO) => {
                    return utxo.txHash != registration?.registration?.txHash;
                });
            }

            // submit vote and registration token
            let response = await axios.post(
                route(
                    "ballot.startVoting",
                    { ballot: ballotHash }
                ),
                {
                    registration,
                    utxos: [...utxos],
                }
            );

            const tx = lucid.fromTx(response.data.tx);
            const witnesses = await tx.partialSign();

            // send to backend for minter witness and submission
            response = await axios.post(
                route("ballot.completeVoting", {
                    ballot: ballotHash
                }),
                {
                    addr: (await lucid.wallet.address()),
                    tx: tx.toString(),
                    witnesses
                }
            );
            return response.data;
        } catch (error) {
            console.log(error);
            return null
        }
    }

    static async register(ballotHash: string) {
        try {
            // get tx from the backend
            const lucid = await BallotService.getLucidInstance();
            let response = await axios.post(
                route('ballot.register.store', {
                    ballot: ballotHash
                }),
                {
                    addr: (await lucid.wallet.address())

                }
            );
            if (response.data?.existingTx) {
                return response.data
            }
            const tx = lucid.fromTx(response.data.tx);
            const witnesses = await tx.partialSign();

            // send to backend for minter witness and submission
            response = await axios.post(
                route("ballot.register.submit", {
                    ballot: ballotHash
                }),
                {
                    addr: (await lucid.wallet.address()),
                    tx: tx.toString(),
                    witnesses
                }
            );
            return response.data;
        } catch (error) {
            console.log(error);
        }
    }

    protected static async getLucidInstance() {
        if (BallotService.lucid) { return BallotService.lucid; }
        try {
            // get connected wallet form the store
            const walletStore = useWalletStore();
            const { walletName } = walletStore;

            const ws = new WalletService(walletName);
            BallotService.lucid = await ws.lucidInstance();
            return BallotService.lucid;
        } catch (error) {
            console.log(error);
        }
    }
}
