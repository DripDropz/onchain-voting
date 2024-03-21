<template>
    <Head title="Petition" />

    <AdminLayout>
        <template #header>
            <Breadcrumbs :crumbs="props.crumbs" />
        </template>

        <div class="py-12">
            <div class="space-y-6 inner-container">
                <div class="flex flex-col gap-8 p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white">
                    <div class="flex justify-center w-full py-3">
                        <p class="text-xl leading-tight xl:text-2xl "> {{ petition.title }}</p>
                    </div>
                    <div class="flex flex-col w-full lg:flex-row gap-y-4">
                        <div class="flex flex-col w-full lg:w-2/3 lg:px-12">

                            <!-- Dropzone  -->
                            <div class="flex items-center justify-center w-full mb-6" @dragover.prevent @drop="handleDrop">
                                <label for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <span v-if="uploading">
                                        <svg aria-hidden="true"
                                            class="w-16 h-16 text-gray-200 animate-spin dark:text-gray-400 fill-sky-800"
                                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                fill="currentColor" />
                                            <path
                                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                fill="currentFill" />
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Uploading...</p>
                                    </span>

                                    <span v-else>
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                <span class="font-semibold">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX.
                                                800x400px)</p>
                                        </div>
                                    </span>
                                </label>
                                <input id="dropzone-file" type="file" class="hidden" @change="handleFileChange" />
                            </div>
                            <div class="flex flex-col">
                                <div class="flex flex-col mb-8">
                                    <span class="text-xl leading-tight xl:text-2xl">
                                        {{ petition?.user?.name }}
                                    </span>
                                    <span class="text-sm text-slate-500">
                                        Started this petition on {{
                                            moment(petition.created_at).format("Do MMMM YYYY")
                                        }}
                                    </span>
                                </div>
                                <div class="p-2 border border-gray-300 sm:rounded-lg dark:border-gray-600">
                                    {{ petition.description }}
                                </div>
                            </div>
                        </div>
                        <div class="w-full lg:w-1/3">
                            <div class="flex flex-col lg:px-12 gap-y-4">
                                <div class="pb-3">
                                    <p class="text-lg font-semibold text-gray-500 dark:text-gray-400">Petition Status: {{
                                        petition.status }}</p>
                                </div>
                                <div>
                                    <div class="flex justify-start w-full pb-3">
                                        <p class="text-xl leading-tight xl:text-2xl "> Petition Criteria</p>
                                    </div>
                                    <Criteria :model="petition" />
                                </div>
                                <div>
                                    <TallyCriteria :model="petition" @update="() => router.reload()" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky bottom-0 flex justify-end px-16 bg-white sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white ">
            <div class="flex gap-5 justify-end container" v-if="!petition.ballot">
                <button
                    class="inline-flex items-center gap-x-2 rounded-md bg-white px-8 py-2.5 font-semibold text-sky-400 shadow-sm hover:bg-sky-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 border border-sky-400">
                    Reject
                </button>
                <button v-if="petition.status !== 'approved'" @click="approve"
                    class="inline-flex items-center gap-x-2 rounded-md bg-sky-400 px-8 py-2.5 font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                    Approve
                </button>
                <Link :href="route('admin.petitions.toBallot', { petition: petition.hash })"
                    v-if="petition.status === 'approved'" :disabled="petition.status != 'approved' || !moveToBallot" :class="{
                        'cursor-not-allowed bg-slate-400 px-2': petition.status != 'approved' || !moveToBallot,
                        'bg-sky-400 hover:bg-sky-600 py-2.5 px-8': petition.status == 'approved' && moveToBallot,

                    }" class="inline-flex items-center gap-1 font-semibold text-white rounded-md shadow-sm">
                <span>Move to Ballot</span><span v-if="!moveToBallot" class="text-xs">- goal(s) unmet</span>
                </Link>
            </div>
            <div class="flex items-center gap-5" v-else>
                <span class="font-bold">Petition moved to ballot</span>
                <Link :href="route('ballot.view', { ballot: petition.ballot.hash })">
                <PrimaryButton :theme="'primary'">
                    view ballot
                    <ArrowTopRightOnSquareIcon class="w-5 h-5" />
                </PrimaryButton>
                </Link>

            </div>

        </div>
    </AdminLayout>
</template>

<script lang="ts" setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import moment from 'moment-timezone';
import { Head } from '@inertiajs/vue3';
import Criteria from '@/shared/components/Criteria.vue';
import TallyCriteria from "@/shared/components/TallyCriteria.vue"
import { router, useForm } from '@inertiajs/vue3';
import Breadcrumbs from "@/Pages/Auth/Breadcrumbs.vue";
import AlertService from '@/shared/Services/alert-service';
import { useConfigStore } from '@/stores/config-store';
import { storeToRefs } from 'pinia';
import { Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ArrowTopRightOnSquareIcon } from '@heroicons/vue/20/solid';
import { computed, ref } from 'vue';
import compareValues from '@/utils/compare-values';

const props = defineProps<{
    petition: PetitionData;
    crumbs: []
}>();

const form = useForm({
    status: props?.petition?.status ?? 'draft',
});

let configStore = useConfigStore()
let { showModal } = storeToRefs(configStore);

let moveToBallot = computed(() => {
    const minSignatureReq = props.petition.petition_goals['ballot-eligible']?.['value2'];
    const operator = props.petition.petition_goals['ballot-eligible']?.['operator'];
    return compareValues(props.petition.signatures_count, minSignatureReq, operator)
})

const MAX_WIDTH = 800;
const MAX_HEIGHT = 400;

const uploading = ref(false);

const handleDrop = (event) => {
    event.preventDefault();
    const file = event.dataTransfer.files[0];
    handleFile(file);
};

const handleFileChange = (event) => {
    const fileInput = event.target;
    const file = fileInput.files[0];
    handleFile(file);
};

const validateImage = (file) => {
    const allowedTypes = ['image/svg+xml', 'image/png', 'image/jpeg', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        AlertService.show(['Only SVG, PNG, JPG, or GIF images are allowed.'], 'error');
        return false;
    }

    const img = new Image();
    img.src = URL.createObjectURL(file);

    return new Promise((resolve) => {
        img.onload = () => {
            if (img.width > MAX_WIDTH || img.height > MAX_HEIGHT) {
                AlertService.show([`Maximum resolution allowed is ${MAX_WIDTH}x${MAX_HEIGHT}px.`], 'error');
                resolve(false);
            } else {
                resolve(true);
            }
        };
    });
};

const handleFile = async (file) => {
    if (file) {
        const isValid = await validateImage(file);
        if (isValid) {
            AlertService.show(['File uploading'], 'success');
            uploading.value = true;
            setTimeout(() => {
                uploading.value = false;
            }, 2000);
        }
    }
};
const approve = () => {
    form.status = 'approved';
    AlertService.show(['Petition Approved Successfully'], 'success');
    form.patch(route('admin.petitions.update', { petition: props.petition?.hash }));
}



</script>
