<template>
    <div v-if="fileParsed" class="flex flex-col justify-center gap-4 p-6 bg-gray-50 dark:bg-gray-800">
        <header class="mb-2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Confirm CSV Parsing
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Confirm the imported csv file containing stake_address,voting_power of all the members of your organization.
            </p>
        </header>
        <div>
            <div v-if="!uploadingFile">
                <table class="text-left text-gray-600 table-auto dark:text-gray-100">
                    <thead>
                        <tr>
                            <th class="p-2 border border-slate-300">
                                Voter ID
                            </th>
                            <th class="p-2 border border-slate-300">
                                Voting Power
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="voter in parsedSample">
                            <td class="p-2 border border-slate-300">
                                {{ voter.voter_id.slice(0, 15) }}...{{ voter.voter_id.slice(-10) }}
                            </td>
                            <td class="p-2 border border-slate-300">
                                {{ voter.voting_power }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex items-center justify-between py-3 mt-2">
                    <div class="flex text-gray-600 dark:text-gray-500">
                        <span>Total Imports : {{ humanNumber(totalParsed, 10) }}</span>
                    </div>
                    <div class="flex flex-row flex-shrink-0 gap-4">
                        <button @click.prevent="cancelParsing"
                            class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-red-600 rounded-md shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                            Cancel
                        </button>
                        <button @click.prevent="confirmParsing"
                            class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white rounded-md shadow-sm bg-sky-600 hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="uploadingFile"
                class="flex flex-row justify-center w-full px-6 py-10 mt-2 border border-dashed rounded-lg border-gray-900/25 dark:border-gray-700">
                <div class="flex flex-col items-center justify-center">
                    <div class="flex flex-col w-16 rounded-full bg-sky-100 bg-opacity-90">
                        <svg aria-hidden="true" class="w-16 h-16 text-gray-200 animate-spin dark:text-gray-400 fill-sky-800"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>

                    </div>
                    <div class="mt-2 text-gray-600 dark:text-gray-400">
                        Uploading File
                    </div>
                </div>
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

        <!-- <div v-if="parsingFile"> -->
        <div v-if="parsingFile"
            class="flex flex-row justify-center w-full px-6 py-10 mt-2 border border-dashed rounded-lg border-gray-900/25 dark:border-gray-700">
            <div class="flex flex-col items-center justify-center">
                <div class="flex flex-col w-16 rounded-full bg-sky-100 bg-opacity-90">
                    <svg aria-hidden="true" class="w-16 h-16 text-gray-200 animate-spin dark:text-gray-400 fill-sky-800"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>

                </div>
                <div class="mt-2 text-gray-600 dark:text-gray-400">
                    Parsing File
                </div>
            </div>
        </div>
        <!-- </div> -->

        <div v-if="!parsingFile" class="col-span-full" @dragenter.prevent @dragover.prevent @drop="inputFile($event)">
            <div
                class="flex justify-center px-6 py-10 mt-2 border border-dashed rounded-lg border-gray-900/25 dark:border-gray-700">
                <div class="text-center ">
                    <FolderOpenIcon class="w-12 h-12 mx-auto text-gray-300" aria-hidden="true" />
                    <div class="flex mt-4 text-sm leading-6 text-gray-600 dark:text-gray-100">
                        <label for="file-upload"
                            class="relative font-semibold rounded-md cursor-pointer text-sky-600 focus-within:outline-none focus-within:ring-none hover:text-sky-500">
                            <span>Upload a file</span>
                            <input id="file-upload" name="file-upload" type="file" class="sr-only"
                                @change="inputFile($event)" ref="fileUploads" />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs leading-5 text-gray-600 dark:text-gray-100">csv up to 300MB</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { FolderOpenIcon } from '@heroicons/vue/24/outline';
import SnapshotData = App.DataTransferObjects.SnapshotData;
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from "axios";
import humanNumber from "@/utils/human-number";
import AlertService from '@/shared/Services/alert-service';
import { useModal } from 'momentum-modal';
import Vapor from '@laravel-vapor';


const props = defineProps<{
    snapshot: SnapshotData;
    redirectPage?: any;
}>();

const { close } = useModal();
let fileUploads = ref();

let parsedSample = ref<any[]>([]);
let totalParsed = ref<number>(0);

let parsingFile = ref(false);
let uploadingFile = ref(false);
let fileParsed = ref(false);

let file = null;
let chunks = ref([]);
let chunksCount = ref<number>(0);

// let dropFile = (event: any) => {

//     parsingFile.value = true;
//     event.preventDefault();

//     file = event.dataTransfer.files[0];
//     if (file) {
//         createChunks(file);
//     }
// }

let inputFile = (event: any) => {
    parsingFile.value = true;
    event.preventDefault();
    
    if (event.dataTransfer?.files?.[0]) {
        file = event.dataTransfer?.files[0]        
    } else {
        file = event.target.files[0];
    }
    Vapor.store(file, {
        // visibility: 'public-read',
        // progress: progress => {
        //     console.log(progress);
        //     // this.uploadProgress = Math.round(progress * 100);
        // }
    }).then(response => {
        console.log({ response,file });
        
        axios.post(`/api/parse/csv`, {
            key: response.key,
            filename: 'vp_' + props.snapshot.hash + '.csv'
        }).then((res) => {
            parsedSample.value = res.data.sample_data;
            totalParsed.value = res.data.total_uploaded;
            parsingFile.value = false;
            fileParsed.value = true;
        })
    });


}

// let createChunks = async (file: any) => {
//     let size = 1024 * 1054 * 5 * 3;//process 5mb size
//     chunksCount.value = Math.ceil(file.size / size);

//     for (let i = 0; i < chunksCount.value; i++) {
//         chunks.value.push(file.slice(
//             i * size, Math.min(i * size + size, file.size), file.type
//         ));
//             console.log({ [i]: chunks.value });
//     }



//     if (chunksCount.value == chunks.value.length) {
//         await parse();
//     }
// }

// let parse = async () => {
//     let dataIsParsed = await parseData();
//     if (dataIsParsed) {
//         setTimeout(() => {
//             getParsedData();
//         }, 1000 * 5)
//     }
// };

let cancelParsing = () => {
    axios.post('/api/parse/csv/cancel', { filename: 'vp_' + props.snapshot.hash + '.csv' })
        .then(() => {
            fileParsed.value = false;
        });
}

let confirmParsing = async () => {
    uploadingFile.value = true;
    await axios.post(`/api/upload/csv/${props.snapshot.hash}`, { filename: 'vp_' + props.snapshot.hash + '.csv' })
        .then(() => {
            AlertService.show(['File upload started'], 'success');
            close();
            setTimeout(() => {
                router.visit(route('admin.snapshots.view', { snapshot: props.snapshot.hash }, {
                    preserveScroll: true,
                    preserveState: false,
                    replace: true,
                }));
            }, 1000 * 1)
        });
}

// let parseData = async () => {
//     for (let index = 0; index < chunks.value.length; index++) {
//         const chunk = chunks.value[index];

//         let formData = new FormData;
//         formData.append('is_last', chunks.value.length === 1 ? 'true' : 'false')
//         formData.append('file', chunk, 'vp_' + props.snapshot.hash + '.csv');
//         formData.append('count', index + '');

//         await axios.post('/api/parse/csv', formData, {
//             headers: {
//                 'Content-Type': 'multipart/form-data',
//             },
//         });
//     }

//     return true;
// }

let getParsedData = async () => {
    const params = {
        count: 10,
    };

    await axios.get(`/api/parsed/csv/vp_${props.snapshot.hash}.csv`, { params })
        .then((res) => {
            parsedSample.value = res.data.sample_data;
            totalParsed.value = res.data.total_uploaded;
            parsingFile.value = false;
            fileParsed.value = true;
        })
}
</script>
