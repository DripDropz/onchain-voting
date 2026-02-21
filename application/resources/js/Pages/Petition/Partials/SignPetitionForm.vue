<template>
    <form class="flex flex-col gap-4">
        <div>
            <h2 class="mt-8 mb-4 text-2xl font-bold dark:text-white">{{ signature ? 'Your Signature' : 'Sign this petition' }}</h2>
        </div>

        <!-- Not logged in -->
        <div class="w-full px-4 py-8 bg-slate-100 dark:bg-gray-800 rounded-xl" v-if="!user">
            <LoginToView>
                <span class="dark:text-slate-300">Login to sign this petition.</span>
            </LoginToView>
        </div>

        <!-- Logged in, not yet signed -->
        <div v-if="user && !signature" class="flex flex-col gap-3">
            <div class="flex flex-col gap-3 p-4 border border-sky-200 dark:border-sky-800 rounded-xl bg-sky-50 dark:bg-sky-950/60">
                <div class="flex flex-col gap-1 mb-1">
                    <p class="font-semibold text-sky-700 dark:text-sky-300">Sign with your Cardano Wallet</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Your wallet signature is cryptographically verifiable and provides the strongest proof of your identity.
                    </p>
                </div>
                <SignWithWallet :petition="petition$" />
            </div>
        </div>

        <!-- Already signed -->
        <div v-if="signature" class="relative">
            <SignatureCard :signature="signature" />
        </div>
    </form>
</template>

<script setup lang="ts">
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
