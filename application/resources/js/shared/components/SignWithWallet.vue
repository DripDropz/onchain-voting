<template >
    <div>
        <ConnectWallet v-if="!walletData.stakeAddress" :background-color="'bg-sky-400 hover:bg-sky-500'" class="w-full"/>
        <PrimaryButton :theme="'primary'" class="w-full py-1" v-else @click.prevent="submitSignature">
            Sign with
            <WalletLogo :wallet="walletData" />
        </PrimaryButton>
    </div>
</template>

<script lang="ts" setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import WalletLogo from '@/cardano/Components/WalletLogo.vue';
import { useWalletStore } from '@/cardano/stores/wallet-store';
import { storeToRefs } from 'pinia';
import ConnectWallet from '@/cardano/Components/ConnectWallet.vue';
import WalletService from '@/cardano/Services/wallet-service';
import { fromText, getAddressDetails } from '@lucid-cardano';
import PetitionData = App.DataTransferObjects.PetitionData;
import AlertService from '../Services/alert-service';
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/vue3';
import { usePetitionSignatureStore } from '@/Pages/Petition/stores/petition-signature-store';

const walletStore = useWalletStore();
const { walletData } = storeToRefs(walletStore);
const { walletName } = storeToRefs(walletStore);

const props = defineProps<{
    petition?: PetitionData;
    user?: {}
}>();

let petitionSignatureStore = usePetitionSignatureStore();
let submitSignature = async () => {

    const messageHex = fromText(`Sign petition ${props.petition.hash}`)
    const signature = await (new WalletService())
        .signMessage(walletName.value, messageHex);

    let form = useForm({
       signature: signature.signature,
       stakeAddress:walletData.value.stakeAddress
    })

    form.post(route('petitions.signatures.store', { petition: props.petition.hash }),
        {
            onSuccess: () => {
                petitionSignatureStore.reloadPetitionData(props.petition.hash).then();
                AlertService.show(['Petition Signed '], 'success');
            },
            onError: (errors) => {
                AlertService.show(
                    Object.entries(errors).map(([key, value]) => value)
                );
            },
        })
}


</script>
