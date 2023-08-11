import {defineStore} from 'pinia';
import {ref, Ref} from 'vue';
import VoterService from '../Services/voter-service';
import humanNumber from "@/utils/human-number";

export const useVoterStore = defineStore('voter', () => {
    let voter = ref<Boolean|null>(null);
    const voterPowers: Ref<{[key: string]: BigInt}> = ref({});

    async function loadVotingPower(voterId: string, ballotHash: string) {
        if (voterPowers.value[ballotHash]) {
            return;
        }
        const voterPower = await VoterService.loadVotingPower(voterId, ballotHash);
        voterPowers.value[ballotHash] = voterPower;
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
        loadVotingPower,
        userVotingPower,

    }
});


