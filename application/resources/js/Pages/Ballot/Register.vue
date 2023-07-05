<template>
    <ModalRoute max-width-class="max-w-xl">
        <div class="container left-0 flex flex-col items-center justify-center w-full h-full p-8 bg-slate-50 dark:bg-indigo-900 ">
            <h2 class="mb-5 title3">
                Register to vote
            </h2>
            <div class="w-4/5">
                <p class="w-full mb-3 text-center">
                    Clicking "Register" below will mint a registration NFT to your wallet.
                    Nft will need to be in your wallet to vote.
                </p>
                <div @click="registerToVote"
                    class="flex flex-col items-center justify-center w-full gap-4 p-4 border border-indigo-400 border-dashed rounded-lg hover:cursor-pointer hover:border-white">
                    <SignalIcon class="w-16 h-16" />
                    <div class="text-xl lg:text-2xl xl:text-3xl">
                        Register
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        Your Voting Power: <span class="font-black text-md xl:text-lg">{{ votingPower }}</span>
                    </div>
                </div>
            </div>
        </div>
    </ModalRoute>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import ModalRoute from '@/Components/ModalRoute.vue';
import { SignalIcon } from '@heroicons/vue/20/solid';
import { computed } from "vue";
import { useVoterStore } from '../Voter/stores/voter-store';
import BallotService from '../Ballot/Services/ballot-service';
const voterStore = useVoterStore();

const props = defineProps<{
    ballot: BallotData;
}>();

const votingPower = computed(() => voterStore.userVotingPower(props?.ballot?.hash));

function registerToVote() {
    BallotService.register(props.ballot.hash);
}

</script>
