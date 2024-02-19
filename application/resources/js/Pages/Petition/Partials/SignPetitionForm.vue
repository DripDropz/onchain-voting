<template>
    <form class="flex flex-col gap-4">
        <div>
            <h2 class="mt-8 mb-4 text-2xl font-bold">{{ signature ? 'Signature' : 'Sign this petition' }}</h2>
        </div>

        <div class="w-full px-4 py-8 bg-slate-100 rounded-xl" v-if="!user">
            <LoginToView>
                <span class="dark:text-slate-900">Login to sign petition.</span>
            </LoginToView>
        </div>

        <div v-if="user && !signature" class="relative">
            <SignWithWallet :petition="petition$" />
            <Divider />
            <div class="sticky flex flex-col gap-3">
                <TextInput v-model="form.firstName" type="text" placeholder="First Name" />
                <TextInput v-model="form.lastName" type="text" placeholder="Last Name" />
                <TextInput v-model="form.email" type="email" placeholder="Email" />
            </div>
            <div class="flex justify-end mt-3">
                <PrimaryButton :theme="'primary'" class="flex items-center justify-center w-full" :default-class="'py-2'"
                    @click.prevent="submitForm">
                    Sign this petition
                </PrimaryButton>
            </div>
        </div>

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
import Divider from '@/shared/components/Divider.vue';
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
