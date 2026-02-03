import { defineStore, storeToRefs } from 'pinia';
import { ref, Ref, watch } from 'vue';
import humanNumber from "@/utils/human-number";
import { useWalletStore } from '@/cardano/stores/wallet-store';
import WalletService from '@/cardano/Services/wallet-service';
import { PolicyId, UTxO } from '@lucid-cardano';
import axios from 'axios';
import BallotService from '@/Pages/Ballot/Services/ballot-service';
import { useConfigStore } from '@/stores/config-store';

export const useVoterStore = defineStore('voter', () => {
    let voter = ref<Boolean | null>(null);
    const voterPowers: Ref<{ [key: string]: BigInt }> = ref({});
    const voterRegistrations: Ref<{ [key: string]: { policyId?: PolicyId, registration?: UTxO } }> = ref({});
    const walletStore = useWalletStore();
    const { walletName } = storeToRefs(walletStore);
    const ws = new WalletService(walletName.value);
    let confirmedOnChain = ref(false);
    let confirmationsComplete = ref(false);
    let confirmationCount = ref(0);

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
        await useWalletStore();
        // get policy id
        const policyIdRes = await axios.get(route('ballot.policyId', { policyType: 'registration', ballot: ballotHash }))
        const txHash = (await axios.get(route('ballot.txHash', { ballot: ballotHash }))).data;



        if (policyIdRes.status === 200) {
            policyId = policyIdRes.data;
        } else {
            return;
        }

        // find policy in user's wallet
        const lucid = await ws.lucidInstance();
        const addr = await lucid.wallet?.address();
        const utxos = (await axios.get(route('blockfrost-query') + `/addresses/${addr}/utxos`)).data;
        let registration = utxos.find((item) => item.tx_hash == txHash)
        const assets = registration.amount.reduce((acc, curr) => {
            if (curr.unit === 'lovelace') {
                acc.lovelace = curr.quantity;
            } else {
                acc[curr.unit] = curr.quantity;
            }
            return acc;
        }, {});
        registration = {
            txHash: registration.tx_hash,
            outputIndex: registration.output_index,
            assets,
            address: registration.address
        };


        if (registration?.address) {
            voterRegistrations.value[ballotHash] = { policyId, registration: registration };
        }
    }

    async function loadVoter(voterId: string, ballotHash: string) {
        if (voterPowers.value[ballotHash]) {
            return;
        }
        voterPowers.value[ballotHash] = await BallotService.loadVotingPower(voterId, ballotHash);
    }

    async function onChainConfirmation(ballotHash: string) {
        const lucid = await ws.lucidInstance();
        let txHash = voterRegistrations.value[ballotHash].registration.txHash
        if (txHash) {
            confirmedOnChain.value = await lucid.awaitTx(txHash, 2000)
        }
    }

    async function frostConfirm(ballotHash) {
        const maxConfirmationCount = 7;
        for (let i = 0; i < maxConfirmationCount; i++) {
            await loadRegistration(ballotHash);
            confirmationCount.value++;
        }
    }

    const userVotingPower = function (ballotHash: string) {
        if (!ballotHash || !voterPowers.value[ballotHash]) {
            return '-';
        }

        return humanNumber(voterPowers.value[ballotHash], 5);
    };

    watch(confirmationCount, () => {
        if (confirmationCount.value == 7) {
            confirmationsComplete.value = true;
        }
    });


    return {
        voter,
        voterPowers,
        voterRegistrations,
        ballotRegistration,
        loadVotingPower,
        userVotingPower,
        loadRegistration,
        registeredForBallot,
        confirmedOnChain,
        onChainConfirmation: onChainConfirmation,
        confirmationCount,
        confirmationsComplete,
        frostConfirm,
    }
});


