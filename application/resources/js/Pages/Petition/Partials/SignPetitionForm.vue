<template>
    <div class="space-y-4">
        <p class="text-sm font-semibold text-white">
            {{ signature ? 'Your Signature' : 'Sign this petition' }}
        </p>

        <!-- Not logged in -->
        <div v-if="!user" class="rounded-xl bg-gray-800/60 border border-gray-700 px-4 py-5 text-center space-y-3">
            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/15 border border-sky-500/20">
                <PencilSquareIcon class="w-5 h-5 text-sky-400" />
            </div>
            <p class="text-sm text-gray-400">Connect your wallet to sign this petition</p>
            <LoginToView>
                <span class="sr-only">Login to sign</span>
            </LoginToView>
        </div>

        <!-- Logged in, not yet signed -->
        <div v-else-if="!signature" class="rounded-xl bg-sky-950/40 border border-sky-700/30 p-4 space-y-3">
            <div class="space-y-0.5">
                <p class="text-sm font-semibold text-sky-300">Sign with your Cardano Wallet</p>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Your wallet signature is cryptographically verifiable and provides the strongest proof of your identity.
                </p>
            </div>
            <SignWithWallet :petition="petition$" />
        </div>

        <!-- Already signed -->
        <div v-else>
            <SignatureCard :signature="signature" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { PencilSquareIcon } from '@heroicons/vue/20/solid';
import SignatureData = App.DataTransferObjects.SignatureData;
import LoginToView from '@/shared/components/LoginToView.vue';
import SignWithWallet from '@/shared/components/SignWithWallet.vue';
import SignatureCard from '@/Pages/Signature/Partials/SignatureCard.vue';
import { usePetitionSignatureStore } from '@/Pages/Petition/stores/petition-signature-store';
import { storeToRefs } from 'pinia';

defineProps<{
    user?: {}
    signature?: SignatureData;
}>();

const petitionSignatureStore = usePetitionSignatureStore();
const { petition$ } = storeToRefs(petitionSignatureStore);
</script>
