<template>
    <form class="flex flex-col gap-4">
        <div>
            <h2 class="mt-8 mb-4 text-2xl font-bold">{{ signature ? 'Your Signature' : 'Sign this petition' }}</h2>
        </div>

        <!-- Not logged in -->
        <div class="w-full px-4 py-8 bg-slate-100 rounded-xl" v-if="!user">
            <LoginToView>
                <span class="dark:text-slate-900">Login to sign petition.</span>
            </LoginToView>
        </div>

        <!-- Logged in, not yet signed -->
        <div v-if="user && !signature" class="relative flex flex-col gap-6">

            <!-- Option 1: Wallet -->
            <div class="flex flex-col gap-3 p-4 border border-sky-200 dark:border-sky-800 rounded-xl bg-sky-50 dark:bg-sky-950">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-sky-500 text-white text-xs font-bold shrink-0">1</span>
                    <p class="font-semibold text-sky-700 dark:text-sky-300">Sign with your Cardano Wallet <span class="text-xs font-normal text-sky-500">(recommended)</span></p>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 -mt-1">
                    Your wallet signature is cryptographically verifiable and provides the strongest proof of your identity.
                </p>
                <SignWithWallet :petition="petition$" />
            </div>

            <!-- Divider -->
            <div class="flex items-center gap-3">
                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
                <span class="text-sm font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">or</span>
                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
            </div>

            <!-- Option 2: Email -->
            <div class="flex flex-col gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-400 dark:bg-gray-600 text-white text-xs font-bold shrink-0">2</span>
                    <p class="font-semibold text-gray-700 dark:text-gray-300">Sign with your email address</p>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 -mt-1">
                    Don't have a Cardano wallet? You can still sign using your name and email.
                </p>
                <TextInput v-model="form.firstName" type="text" placeholder="First Name" />
                <TextInput v-model="form.lastName" type="text" placeholder="Last Name" />
                <TextInput v-model="form.email" type="email" placeholder="Email Address" />
                <div class="flex justify-end mt-1">
                    <PrimaryButton :theme="'primary'" class="flex items-center justify-center w-full" :default-class="'py-2'"
                        @click.prevent="submitForm">
                        Sign this petition
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <!-- Already signed -->
        <div v-if="signature" class="relative">
            <SignatureCard :signature="signature" />
        </div>
    </form>
</template>

<script setup lang="ts">
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import SignatureData = App.DataTransferObjects.SignatureData;
import AlertService from '@/shared/Services/alert-service';
import TextInput from '@/Components/TextInput.vue';
import LoginToView from '@/shared/components/LoginToView.vue';
import SignWithWallet from '@/shared/components/SignWithWallet.vue';
import SignatureCard from '@/Pages/Signature/Partials/SignatureCard.vue';
import {usePetitionSignatureStore} from '@/Pages/Petition/stores/petition-signature-store';
import { storeToRefs } from 'pinia';


const props = defineProps<{
    user?: {}
    signature?: SignatureData;
}>();

let petitionSignatureStore = usePetitionSignatureStore();
let {  petition$ } = storeToRefs(petitionSignatureStore);

let form = useForm({
    firstName: '',
    lastName: '',
    email: ''
})

const submitForm = async() => {
    form.post(route('petitions.signatures.store', { petition: petition$.value.hash }),
        {
            onSuccess: async() => {
                await petitionSignatureStore.reloadPetitionData(petition$.value.hash);
                AlertService.show(['Petition Signed '], 'success');
            },
            onError: (errors) => {
                AlertService.show(
                    Object.entries(errors).map(([key, value]) => value)
                );
            },
        })
};
</script>
