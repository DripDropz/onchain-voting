<template>
    <ModalRoute max-width-class="max-w-xl">
        <div class="container left-0 w-full h-full p-8 bg-slate-50 dark:bg-indigo-900 ">
            <div class="flex flex-col items-center justify-center gap-3" v-if="registrationComplete">
                <h2 class="mb-5 title3">
                    Registration complete!
                </h2>
                <div class="w-4/5">
                    <p class="w-full mb-3 text-center" v-if="!alreadyRegistered">
                        Your registration NFT has been minted to your wallet.
                        Waiting until Registration NFT is in your wallet, then return to vote.
                    </p>

                    <p class="w-full mb-3 text-center" v-if="!!alreadyRegistered">
                        Your registration NFT has alredy been minted to your wallet.
                        You may proceed to voting.
                    </p>

                    <div class="grid grid-cols-2 gap-3 mt-6 text-xl">
                        <a :href="`${config.explorer}/${registrationComplete}`" target="_blank"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-black bg-white rounded-md focus:outline-none hover:cursor-pointer">
                            <LinkIcon class="w-6 h-6" />
                            <span>View Tx</span>
                        </a>
                        <button @click="close()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-black transition duration-150 ease-in-out bg-white rounded-md focus:outline-none hover:cursor-pointer">
                            <LinkIcon class="w-6 h-6" />
                            <span>Close</span>
                        </button>
                    </div>
                </div>
            </div>


            <div v-else class="flex flex-col items-center justify-center">
                <h2 class="mb-5 title3">
                    Register to vote
                </h2>
                <div class="w-4/5">
                    <p class="w-full mb-3 text-center">
                        Clicking "Register" below will mint a registration NFT to your wallet.
                        NFT will need to be in your wallet to vote.
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
        </div>
    </ModalRoute>
</template>
<script lang="ts" setup>
import { ref } from 'vue';
import BallotData = App.DataTransferObjects.BallotData;
import ModalRoute from '@/Components/ModalRoute.vue';
import { LinkIcon, SignalIcon } from '@heroicons/vue/20/solid';
import { computed } from "vue";
import { useVoterStore } from '../Voter/stores/voter-store';
import BallotService from '../Ballot/Services/ballot-service';
import { storeToRefs } from 'pinia';
import { useConfigStore } from '@/stores/config-store';
import { useModal } from 'momentum-modal';

const submittingRegistration = ref(false);
const registrationComplete = ref(null);
let alreadyRegistered = ref(null);

const voterStore = useVoterStore();

const configStore = useConfigStore();
const { config } = storeToRefs(configStore);

const { close } = useModal();

const props = defineProps<{
    ballot: BallotData;
}>();

const votingPower = computed(() => voterStore.userVotingPower(props?.ballot?.hash));

async function registerToVote() {
    submittingRegistration.value = true;
    if (props.ballot?.hash) {
        const data = await BallotService.register(props.ballot?.hash);
        if (data?.tx) {
            registrationComplete.value = data?.tx;
        } else {
            registrationComplete.value = data?.existingTx
            alreadyRegistered.value = true;
        }

    }
    submittingRegistration.value = false;
}

</script>
