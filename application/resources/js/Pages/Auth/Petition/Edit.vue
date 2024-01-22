<template>
    <Head title="Petition" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumbs :crumbs="props.crumbs" />
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="flex flex-col gap-8 p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white">
                    <div class="flex justify-center w-full py-3">
                        <p class="text-xl leading-tight xl:text-2xl "> {{ petition.title }}</p>
                    </div>
                    <div class="flex flex-col w-full lg:flex-row gap-y-4">
                        <div class="flex flex-col w-full lg:w-2/3 lg:px-12">
                            <div class="flex items-center justify-center w-full mb-6">
                                <label for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX.
                                            800x400px)</p>
                                    </div>
                                    <input id="dropzone-file" type="file" class="hidden"
                                        @change="(e) => handleFileChange(e)" />
                                </label>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex flex-col mb-8">
                                    <span class="text-xl leading-tight xl:text-2xl">
                                        {{ petition.user.name }}
                                    </span>
                                    <span class="text-sm text-slate-500">
                                        Started this petition on {{ moment(petition.created_at).format("Do MMMM YYYY") }}
                                    </span>
                                </div>
                                <div class="p-2 border border-gray-300 sm:rounded-lg dark:border-gray-600">
                                    {{ petition.description }}
                                </div>
                            </div>
                        </div>
                        <div class="w-full lg:w-1/3">
                            <div class="flex flex-col lg:px-12 gap-y-4">
                                <div>
                                    <div class="flex justify-start w-full pb-3">
                                        <p class="text-xl leading-tight xl:text-2xl "> Petition Criteria</p>
                                    </div>
                                    <div class="p-2 overflow-auto max-h-48 "
                                        :class="{ 'hover:border dark:hover:border-gray-500 hover:border-gray-300': petition?.categories.length > 3 }">
                                        <div v-for="category in petition?.categories"
                                            class="flex flex-row items-center justify-between w-full gap-2 py-3 border-b border-gray-200 border-opacity-40 dark:border-gray-600 ">
                                            <div class="flex flex-col text-sm">
                                                <span class="font-bold">{{ category.title }}</span>
                                                <span class="font-light text-slate-500"> Drippz</span>
                                            </div>
                                            <div>
                                                <label class="relative inline-flex items-center cursor-pointer"
                                                    :for="'category_' + category.id">
                                                    <input type="checkbox" :id="'category_' + category.id" v-model="catIds"
                                                        :value="category.id" class="sr-only peer" checked
                                                        @change="(e) => updateSelectedCat(e.target.checked, category.id)">
                                                    <div
                                                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-sky-300 dark:peer-focus:ring-sky-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-sky-400">
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-start w-full pb-3">
                                        <p class="text-xl leading-tight xl:text-2xl ">Signature Goals</p>
                                    </div>
                                    <div class="flex flex-col gap-y-4">
                                        <div class="flex flex-col">
                                            <span class="mb-1 text-sm text-slate-500"> Visible on site</span>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="number" value=""
                                                    class="border-0 rounded w-28 focus:ring-0 dark:bg-gray-900 bg-sky-100">
                                            </label>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="mb-1 text-sm text-slate-500"> Feature petition</span>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="number" value=""
                                                    class="border-0 rounded w-28 focus:ring-0 dark:bg-gray-900 bg-sky-100">
                                            </label>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="mb-1 text-sm text-slate-500"> Ballot eligible</span>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="number" value=""
                                                    class="border-0 rounded w-28 focus:ring-0 dark:bg-gray-900 bg-sky-100">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="sticky bottom-0 flex justify-end gap-5 px-16 bg-white sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white ">
            <button @click="submit('rejected')"
                class="inline-flex items-center gap-x-2 rounded-md bg-white px-8 py-2.5 font-semibold text-sky-400 shadow-sm hover:bg-sky-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 border border-sky-400">
                Reject
            </button>
            <button @click="submit('approved')"
                class="inline-flex items-center gap-x-2 rounded-md bg-sky-400 px-8 py-2.5 font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                Approve
            </button>
        </div>
    </AuthenticatedLayout>
</template>

<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Nav from '../Breadcrumbs.vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import moment from 'moment-timezone';
import TextareaInput from '@/Components/TextareaInput.vue';
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Breadcrumbs from "@/Pages/Auth/Breadcrumbs.vue";
import axios from 'axios';

const props = defineProps<{
    petition: PetitionData;
    crumbs: []
}>();
let catIds = ref(props.petition.categories.map(item => item.id))


let updateSelectedCat = (checked, id) => {
    if (!checked) {
        catIds.value = catIds.value.filter(item => item !== id)
    } else {
        catIds.value.includes(id) ? '' : catIds.value.push(id)
    }
    form.catIds = catIds.value;
}

let handleFileChange = (event) => {
    const fileInput = event.target;
    const file = fileInput.files[0];
    const photoPreview = URL.createObjectURL(file)

    console.log('Selected file:', photoPreview);
}

let submit = (status) => {
    axios.post(route('admin.petitions.update', { petition: props.petition.hash, status: status }))
}

</script>
