<template>
    <div class="overflow-hidden border border-gray-200 rounded-xl dark:border-gray-700">
        <div
            class="px-6 py-4 -my-3 text-sm leading-6 bg-gray-100 divide-y divide-gray-100 dark:divide-gray-700 dark:bg-gray-900">
            <div class="flex justify-between py-3 gap-x-4">
                <div class="text-gray-600 dark:text-gray-200">
                    Created
                </div>
                <div class="text-gray-700 dark:text-gray-100">
                    {{ formatDate(signature.created_at) }}
                </div>
            </div>
            <div class="flex justify-between py-3 gap-x-4">
                <div class="text-gray-600 dark:text-gray-200">
                    Type
                </div>
                <div class="text-gray-700 dark:text-gray-100">
                    {{ getSignatureType() }}
                </div>
            </div>
            <div class="flex justify-between py-3 gap-x-4" v-if="signature.email_signature">
                <div class="text-gray-600 dark:text-gray-200">
                    Email
                </div>
                <div class="text-gray-700 dark:text-gray-100">
                    {{ signature.email_signature }}
                </div>
            </div>
            <div class="flex justify-between py-3 gap-x-4" v-if="signature.stake_address">
                <div class="text-gray-600 dark:text-gray-200">
                    Address
                </div>
                <div class="text-gray-700 dark:text-gray-100">
                    {{ maskedAddress }}
                </div>
            </div>
            <div class="flex justify-between py-3 gap-x-4" v-if="signature.wallet_signature">
                <div class="text-gray-600 dark:text-gray-200">
                    Signature
                </div>
                <div class="text-gray-700 dark:text-gray-100">
                    {{ maskedSignature }}
                </div>
            </div>
            <ul class="bg-slate-100 dark:bg-slate-700 flex flex-row items-center rounded-tr-lg rounded-br-lg justify-end mt-3">
                <li class="w-auto ocv-link">
                    <img :src="voteAppLogo" alt="Open Chainvote App Logo" class="w-5 h-5">
                </li>
                <li class="w-auto ocv-link">
                    <h1 class="font-bold tracking-tight sm:text-sm xl:text-sm font-display text-slate-900 dark:text-slate-200">
                        ChainVote
                    </h1>
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts" setup>
import SignatureData = App.DataTransferObjects.SignatureData;
import voteAppLogo from '../../../../images/openchainvote.png';

const props = defineProps<{
    signature: SignatureData;
}>();

const formatDate = (dateString: string): string => {
    const options = {month: '2-digit', day: '2-digit', year: '2-digit'};
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const getSignatureType = (): string => {
    if (props.signature.wallet_signature) {
        return 'Wallet';
    } else if (props.signature.email_signature) {
        return 'Email';
    } else {
        return 'Unknown';
    }
};
const maskedAddress = props.signature.stake_address.slice(0, 12) + '****' + props.signature.stake_address.slice(-12);
const maskedSignature = props.signature.wallet_signature.slice(0,10) + '****' + props.signature.wallet_signature.slice(-10)
</script>