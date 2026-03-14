<template>
    <div>
        <ConnectWallet v-if="!walletData?.stakeAddress" :background-color="'bg-sky-400 hover:bg-sky-500'" class="w-full"/>
        <button
            v-else
            @click.prevent="submitVote"
            :disabled="!selectedChoice || isSubmitting"
            class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-sky-600 rounded-lg hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
            <WalletLogo v-if="walletData?.walletName" :wallet="walletData" class="mr-2" />
            <span v-if="isSubmitting">Signing...</span>
            <span v-else>Sign & Submit Vote</span>
        </button>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useWalletStore } from '@/cardano/stores/wallet-store';
import { storeToRefs } from 'pinia';
import WalletService from '@/cardano/Services/wallet-service';
import ConnectWallet from '@/cardano/Components/ConnectWallet.vue';
import WalletLogo from '@/cardano/Components/WalletLogo.vue';
import AlertService from '@/shared/Services/alert-service';
import { fromText } from '@lucid-cardano';
import PollData = App.DataTransferObjects.PollData;

const props = defineProps<{
    poll: PollData;
    selectedChoice: string | null;
}>();

const emit = defineEmits<{
    (e: 'success'): void;
}>();

const walletStore = useWalletStore();
const { walletData } = storeToRefs(walletStore);
const { walletName } = storeToRefs(walletStore);

const isSubmitting = ref(false);

const submitVote = async () => {
    if (!props.selectedChoice || !walletData.value?.stakeAddress) return;

    try {
        isSubmitting.value = true;

        // Create message to sign
        const messageText = `Vote on poll ${props.poll.hash}: ${props.selectedChoice}`;
        const messageHex = fromText(messageText);

        // Sign with wallet
        const signature = await WalletService.getInstance()
            .signMessage(walletName.value, messageHex);

        // Submit vote with signature
        const form = useForm({
            questionHash: props.poll.question?.hash,
            selectedChoiceHash: props.selectedChoice,
            signature: signature.signature,
            stakeAddress: walletData.value.stakeAddress,
        });

        form.post(route('polls.storeQuestionResponse', { poll: props.poll.hash }), {
            onSuccess: () => {
                AlertService.show(['Vote submitted successfully!'], 'success');
                emit('success');
            },
            onError: (errors) => {
                const errorMessage = Object.values(errors).flat()[0] || 'Failed to submit vote';
                AlertService.show([errorMessage], 'error');
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    } catch (error) {
        isSubmitting.value = false;
        console.error('Vote signing error:', error);
        AlertService.show(['Failed to sign vote with wallet. Please try again.'], 'error');
    }
};
</script>
