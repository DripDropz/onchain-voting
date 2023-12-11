import {defineStore, storeToRefs} from 'pinia';
import {ref, Ref} from 'vue';
import humanNumber from "@/utils/human-number";
import {useWalletStore} from '@/cardano/stores/wallet-store';
import WalletService from '@/cardano/Services/wallet-service';
import {PolicyId, UTxO} from 'lucid-cardano';
import axios from 'axios';
import BallotService from '@/Pages/Ballot/Services/ballot-service';

export const useVoterStore = defineStore('voter', () => {
    let voter = ref<Boolean|null>(null);
    const voterPowers: Ref<{[key: string]: BigInt}> = ref({});
    const voterRegistrations: Ref<{[key: string]: {policyId?: PolicyId, registration?: UTxO}}> = ref({});

    function registeredForBallot(ballotHash: string) {
        return !!voterRegistrations.value[ballotHash];
    }

    function ballotRegistration(ballotHash: string) {
        return voterRegistrations.value[ballotHash];
    }

    async function loadVotingPower(voterId: string, ballotHash: string) {
        if (voterPowers.value[ballotHash]) {
            return;
        }
        voterPowers.value[ballotHash] = await BallotService.loadVotingPower(voterId, ballotHash);
    }

    async function loadRegistration(ballotHash: string) {
        let policyId: string = '';
        // get policy id
        const policyIdRes = await axios.get(route('ballot.policyId', {policyType: 'registration', ballot: ballotHash}))
        const walletStore = useWalletStore();
        const {walletName} = storeToRefs(walletStore);

        if ( policyIdRes.status === 200) {
            policyId = policyIdRes.data;
        } else {
            return;
        }

        // find policy in user's wallet
        const ws = new WalletService(walletName.value);
        const lucid = await ws.lucidInstance();
        const utxos = await lucid.wallet.getUtxos();

        const registrations = utxos.filter((utxo: UTxO) =>
         Object.keys(utxo.assets).some(asset => asset.includes(policyId))
        );
        if ( registrations.length > 0 ) {
            voterRegistrations.value[ballotHash] = {policyId, registration: registrations[0]};
        }
    }

    async function loadVoter(voterId: string, ballotHash: string) {
        if (voterPowers.value[ballotHash]) {
            return;
        }
        voterPowers.value[ballotHash] = await BallotService.loadVotingPower(voterId, ballotHash);
    }

    const userVotingPower = function(ballotHash: string){
        if (!ballotHash || !voterPowers.value[ballotHash]) {
            return '-';
        };

        return humanNumber(voterPowers.value[ballotHash], 5);
    };

    return {
        voter,
        voterPowers,
        voterRegistrations,
        ballotRegistration,
        loadVotingPower,
        userVotingPower,
        loadRegistration,
        registeredForBallot
    }
});


