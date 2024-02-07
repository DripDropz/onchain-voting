<template>
    <Link href="#" :preserve-scroll="true" @click.prevent="registerUser()"
          :disabled="!votingPowerCheck && votingPowerCheck !== null"
          class="absolute top-0 left-0 flex items-center justify-center w-full h-full p-8 text-sky-700 rounded-lg bg-sky-800/50 hover:cursor-pointer">
        <div
            class="flex flex-col transition ease-in-out delay-150 px-4 py-2 text-lg font-bold bg-sky-100 rounded-full xl:text-text hover:-translate-y-1 hover:scale-110 duration-300"
            :class="{'opacity-90 cursor-not-allowed': !votingPowerCheck && votingPowerCheck !== null}">
            <span>Register to Vote</span>
            <span
                v-if="!votingPowerCheck && votingPowerCheck !== null"
                class="text-xs text-gray-500">
                Registration is disabled due to missing voting power!
            </span>
        </div>
    </Link>
</template>
<script lang="ts" setup>
import BallotData = App.DataTransferObjects.BallotData;
import {router, Link} from '@inertiajs/vue3';
import AlertService from '@/shared/Services/alert-service';
import {useVoterStore} from "@/Pages/Voter/stores/voter-store";
import {useWalletStore} from "@/cardano/stores/wallet-store";
import {storeToRefs} from "pinia";
import {watch, ref} from "vue";

const props = defineProps<{
    pageData?: any,
    ballot: BallotData
    hasVotingPower?: boolean
}>()

const walletStore = useWalletStore();
const voterStore = useVoterStore();

let votingPowerCheck = ref(null);
let {walletData: wallet} = storeToRefs(walletStore);
let {voterPowers} = storeToRefs(voterStore);

watch(wallet, () => {
    if (wallet.value?.stakeAddress && props.ballot?.hash) {
        voterStore
            .loadVotingPower(wallet.value.stakeAddress, props.ballot.hash)
            .then(() => {
                if (Number(voterPowers.value[props.ballot.hash]) > 0) {
                    votingPowerCheck.value = true;
                } else if (Number(voterPowers.value[props.ballot.hash]) === 0) {
                    votingPowerCheck.value = false;
                }
            });
    }
});

function registerUser() {
    if (props.hasVotingPower) {
        router.get(route('ballot.register.view', {ballot: props.ballot.hash}));
    } else {
        AlertService.show(["Voting power missing. Please contact support."], 'info');
    }
}
</script>
