<template>
   <div v-if="isFileParsed" class="flex flex-col justify-center gap-4 p-6 bg-gray-50 dark:bg-gray-800">
        <header class="mb-2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Confirm CSV Parsing
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Confirm the imported csv file containing stake_address,voting_power of all the members of your organization.
            </p>
        </header>
        <table class="text-left text-gray-600 table-auto dark:text-gray-100">
            <thead>
                <tr>
                    <th class="p-2 border border-slate-300">
                        Voter ID (stake_address)
                    </th>
                    <th class="p-2 border border-slate-300">
                        Voting Power
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="voter in glimpseParsedContent">
                    <td class="p-2 border border-slate-300">
                        {{ voter.voter_id.slice(0, 15) }}...{{ voter.voter_id.slice(-10) }}
                    </td>
                    <td class="p-2 border border-slate-300">
                        {{ voter.voting_power }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div
            class="flex items-center justify-between py-3 mt-2">
            <div class="flex text-gray-600 dark:text-gray-500">
                <span>Total Imports : {{ humanNumber(Object.keys(parsedFileContent).length, 10) }}</span>
            </div>
            <div class="flex flex-row flex-shrink-0 gap-4">
                <button
                    @click.prevent="cancelParsing"
                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-red-600 rounded-md shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Cancel
                </button>
                <button
                    @click.prevent="confirmParsing"
                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Confirm
                </button>
            </div>
        </div>
    </div>
    <div v-else class="flex flex-col justify-center gap-4 p-6">
        <header class="mb-2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Import Voting Power
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Import a csv file containing stake_address,voting_power of all the members of your organization.
            </p>
        </header>

        <div class="col-span-full" @dragenter.prevent @dragover.prevent @drop="dropFile($event)">
            <div class="flex justify-center px-6 py-10 mt-2 border border-dashed rounded-lg border-gray-900/25 dark:border-gray-700">
                <div class="text-center ">
                    <FolderOpenIcon class="w-12 h-12 mx-auto text-gray-300" aria-hidden="true" />
                    <div class="flex mt-4 text-sm leading-6 text-gray-600 dark:text-gray-100">
                        <label for="file-upload" class="relative font-semibold text-indigo-600 rounded-md cursor-pointer focus-within:outline-none focus-within:ring-none hover:text-indigo-500">
                            <span>Upload a file</span>
                            <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="inputFile($event)"/>
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs leading-5 text-gray-600 dark:text-gray-100">csv up to 100MB</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { FolderOpenIcon } from '@heroicons/vue/24/outline';
import SnapshotData = App.DataTransferObjects.SnapshotData;
import VoterData = App.DataTransferObjects.VoterData;
import { ref } from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import axios from "axios";
import humanNumber from "@/utils/human-number";
import AlertService from '@/shared/Services/alert-service';

const props = defineProps<{
    snapshot: SnapshotData;
}>();

let parsedFileContent = ref<VoterData[]>([]);
let glimpseParsedContent = ref<VoterData[]>([]);

let uploadingFile = ref(false);
let isFileParsed = ref(false);

let dropFile = (event:any) => {
    event.preventDefault();
    const file = event.dataTransfer.files[0];
    parseFile(file);
}

let inputFile = (event:any) => {
    event.preventDefault();
    const file = event.target.files[0];
    parseFile(file);
}

let parseFile =  (fileObject:any) => {
    uploadingFile.value = true;

    const formData = new FormData();
    formData.append('file', fileObject);

    axios.post('/api/parse/csv', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
    .then(response => {
        parsedFileContent.value = response.data?.data;
        glimpseParsedContent.value = response.data?.partialData;
        uploadingFile.value = false;
        isFileParsed.value = true;
    })
    .catch(error => {
        console.error(error);
    });
}

let cancelParsing = () => {
    parsedFileContent.value = [];
    glimpseParsedContent.value = [];
    isFileParsed.value = false;
}

let confirmParsing = () => {
    const form = useForm({parsedCsv: parsedFileContent.value});

    form.post(route('admin.snapshots.powers.csv.store', {snapshot: props.snapshot.hash}), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            AlertService.show(['Voting power added to snapshot successfully'], 'success');
        },
        onError: (errors) => {
            Object.entries(errors).forEach(([key, value]) => {
                AlertService.show([value], 'info');
            });
        },
    })
}

</script>
