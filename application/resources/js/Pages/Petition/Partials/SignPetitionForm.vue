<template>
    <form class="flex flex-col gap-4" >
        <div>
            <h2 class="mt-8 mb-4 text-2xl font-bold">Sign this petition</h2>
        </div>

        <LoginToview v-if="!user" :class-height="''" :text="'Login to sign this petitions.'" />
        <div v-else>
            <SignWithWallet :petition="petition" />
            <Divider />
            <div class="flex flex-col gap-3">
                <TextInput v-model="form.firstName" type="text" placeholder="First Name" />
                <TextInput v-model="form.lastName" type="text" placeholder="Last Name" />
                <TextInput v-model="form.email" type="email" placeholder="Email" />
            </div>
            <div class="flex justify-end ">
                <PrimaryButton :theme="'primary'" class="flex items-center justify-center w-full" @click.prevent="submitForm">
                    Sign this petition
                </PrimaryButton>
            </div>
        </div>


    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import PetitionData = App.DataTransferObjects.PetitionData;
import AlertService from '@/shared/Services/alert-service';
import TextInput from '@/Components/TextInput.vue';
import LoginToview from '@/shared/components/LoginToview.vue';
import Divider from '@/shared/components/Divider.vue';
import SignWithWallet from '@/shared/components/SignWithWallet.vue';


const props = defineProps<{
    petition?: PetitionData;
    user?: {}
}>();

let form = useForm({
    firstName: '',
    lastName: '',
    email: ''
})



const submitForm = () => {
    form.post(route('petitions.signatures.store', { petition: props.petition.hash }),
        {
            onSuccess: () => {
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
