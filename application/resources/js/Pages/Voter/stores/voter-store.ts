import {defineStore} from 'pinia';
import {onMounted, ref, Ref, computed} from 'vue';
import VoterService from '../Services/voter-service';

export const useVoterStore = defineStore('voter', () => {
    let voter = ref<Boolean|null>(null);
    const voterPowers: Ref<{[key: string]: BigInt}> = ref({});

    async function loadVoter(voterId: string) {
        const voter = await VoterService.loadVoter(voterId);
    }

    async function loadVotingPower(voterId: string, ballotHash: string) {
        if (voterPowers.value[ballotHash]) {
            return;
        }
        const voterPower = await VoterService.loadVotingPower(voterId, ballotHash);
        voterPowers.value[ballotHash] = voterPower;
    }

    // onMounted();

    return {
        voter,
        voterPowers,
        loadVoter,
        loadVotingPower
    }
});


