<template>
    <ModalRoute :max-width-class="'max-w-[55rem]'">
        <div class="flex flex-col h-full p-6 border border-slate-200 gap-y-4 dark:border-slate-700 ">
            <p class="text-xl font-bold leading-tight xl:text-2xl">Make Petition Rules</p>
            <div class="flex flex-col gap-2">
                <p>Title </p>
                <TextInput class="w-full" :Placeholder="'Title here...'" v-model="form.title" />
            </div>

            <div class="flex flex-col gap-2">
                <p>Policy Id </p>
                <TextInput class="w-full" :Placeholder="'Paste Policy here...'" v-model="form.policy" />
            </div>

            <div class="flex flex-col gap-4">
                <div v-if="!assetData && working">
                    <p>
                        Fetching asset data, please wait to confirm...
                    </p>
                    <div role="status" class="flex flex-col gap-6 p-3 lg:flex-row">
                        <div class="flex items-center justify-center w-64 h-64 mb-4 rounded bg-slate-300 dark:bg-slate-700">
                            <svg class="w-10 h-10 text-slate-200 dark:text-slate-600" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                                <path
                                    d="M14.066 0H7v5a2 2 0 0 1-2 2H0v11a1.97 1.97 0 0 0 1.934 2h12.132A1.97 1.97 0 0 0 16 18V2a1.97 1.97 0 0 0-1.934-2ZM10.5 6a1.5 1.5 0 1 1 0 2.999A1.5 1.5 0 0 1 10.5 6Zm2.221 10.515a1 1 0 0 1-.858.485h-8a1 1 0 0 1-.9-1.43L5.6 10.039a.978.978 0 0 1 .936-.57 1 1 0 0 1 .9.632l1.181 2.981.541-1a.945.945 0 0 1 .883-.522 1 1 0 0 1 .879.529l1.832 3.438a1 1 0 0 1-.031.988Z" />
                                <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.98 2.98 0 0 0 .13 5H5Z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex flex-col py-3 border-b border-gray-600 border-opacity-40 dark:border-gray-600">
                                <div class="h-2.5 bg-gray-600 rounded-full dark:bg-gray-700 w-24 mb-4"></div>
                                <div class="h-2.5 bg-gray-600 rounded-full dark:bg-gray-700 w-80 mb-4"></div>
                            </div>
                            <div class="flex-col py-3 border-b border-gray-600 border-opacity-40 dark:border-gray-600">
                                <div class="h-2.5 bg-gray-600 rounded-full dark:bg-gray-700 w-24 mb-4"></div>
                                <div class="h-2.5 bg-gray-600 rounded-full dark:bg-gray-700 w-80 mb-4"></div>
                            </div>
                            <div class="flex-col py-3 border-b border-gray-600 border-opacity-40 dark:border-gray-600">
                                <div class="h-2.5 bg-gray-600 rounded-full dark:bg-gray-700 w-24 mb-4"></div>
                                <div class="h-2.5 bg-gray-600 rounded-full dark:bg-gray-700 w-80 mb-4"></div>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="w-full gap-4 " v-if="assetData">
                    <p class="mb-4 font-bold">
                        We found {{ assetCount }} assets under this policy. Here is your first asset's detail.
                    </p>
                    <div class="flex flex-col gap-6 p-3 lg:flex-row">
                        <UseImage :src="assetImageUrl" class="w-64 h-64">
                            <template #loading>
                                <div
                                    class="flex items-center justify-center w-64 h-64 mb-4 rounded bg-slate-300 dark:bg-slate-700 animate-pulse">
                                    <svg class="w-10 h-10 text-slate-200 dark:text-slate-600" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                                        <path
                                            d="M14.066 0H7v5a2 2 0 0 1-2 2H0v11a1.97 1.97 0 0 0 1.934 2h12.132A1.97 1.97 0 0 0 16 18V2a1.97 1.97 0 0 0-1.934-2ZM10.5 6a1.5 1.5 0 1 1 0 2.999A1.5 1.5 0 0 1 10.5 6Zm2.221 10.515a1 1 0 0 1-.858.485h-8a1 1 0 0 1-.9-1.43L5.6 10.039a.978.978 0 0 1 .936-.57 1 1 0 0 1 .9.632l1.181 2.981.541-1a.945.945 0 0 1 .883-.522 1 1 0 0 1 .879.529l1.832 3.438a1 1 0 0 1-.031.988Z" />
                                        <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.98 2.98 0 0 0 .13 5H5Z" />
                                    </svg>
                                </div>
                            </template>

                            <template #error>

                            </template>
                        </UseImage>
                        <div class="flex flex-col w-3/4 gap-4 italic justify-items-center ">
                            <div>
                                <div
                                    class="flex flex-col justify-start gap-2 py-3 border-b border-slate-400 border-opacity-40 dark:border-slate-600 text-wrap">
                                    <span class="mr-2 font-bold">Fingerprint:</span> <span class="break-all">
                                        {{ assetData.value?.metadata?.name ?? assetData.value?.onchain_metadata?.name ??
                                            assetData.value?.name ?? assetData.fingerprint }}
                                    </span>
                                </div>
<!--                                <div-->
<!--                                    class="flex flex-col justify-start w-full gap-2 py-3 border-b border-slate-400 border-opacity-40 dark:border-slate-600 text-wrap">-->
<!--                                    <span class="mr-2 font-bold">Policy:</span> <span class="break-all">{{-->
<!--                                        assetData.policy_id }}</span>-->
<!--                                </div>-->
                                <div
                                    class="flex flex-col justify-start w-full gap-2 py-3 border-b border-slate-400 border-opacity-40 dark:border-slate-600 text-wrap">
                                    <span class="mr-2 font-bold">Asset :</span> <span class="break-all">{{ assetData.asset
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end w-full">
                        <PrimaryButton :theme="'primary'" @click="saveRule">
                            Save Rule
                        </PrimaryButton>
                    </div>

                </div>

            </div>
        </div>
    </ModalRoute>
</template>

<script lang="ts" setup>
import ModalRoute from '@/Components/ModalRoute.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import { computed, ref, watch } from 'vue';
import { UseImage } from '@vueuse/components';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { router, useForm } from '@inertiajs/vue3';
import PetitionData = App.DataTransferObjects.PetitionData;
import AlertService from '@/shared/Services/alert-service';
import { useModal } from 'momentum-modal';


const props = withDefaults(defineProps<{
    petition?: PetitionData;
    type?: string
}>(), {
});


let assetData = ref();
let working = ref(false)
let assetCount = ref();
let { close } = useModal()

let form = useForm({
    policy: '',
    title: '',
    type: props.type,
})

let query = async () => {
    working.value = true;
    let res = (await axios.get(route('frost.asset', { policy: form.policy }))).data;
    assetData.value = res.asset;
    assetCount.value = res.assetCount;
    working.value = false;
}

let assetImageUrl = computed(() => {
    let url: string = assetData.value?.metadata?.image ?? assetData.value?.onchain_metadata?.image ?? assetData.value?.image;
    if (url?.includes('ipfs://')) {
        url = url.replace('ipfs://', 'https://ipfs.io/ipfs/');
    } else if (url?.includes('ar://')) {
        url = url.replace('ar://', 'https://arweave.net/');
    }
    return url;
})

let saveRule = () => {
    form.post(route('petitions.rules.saveRule', { petition: props.petition.hash }),
        {
            onSuccess: () => {
                AlertService.show(['Rule created'], 'success');
                close();
            },
            onError: (errors) => {
                AlertService.show(
                    Object.entries(errors).map(([key, value]) => value)
                );
            },
        })
}


watch(() => form.policy, () => {
    query();
})

</script>


