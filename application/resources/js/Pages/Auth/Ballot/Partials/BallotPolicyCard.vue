<template>
    <div class="flex flex-col gap-4">
        <div class="flex flex-row justify-between">
            <div class="flex flex-col gap-2">
                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    <slot></slot>
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400">
                    This policy will be used to register voters.
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                Policy Script
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400">
                {{ policy?.script }}
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                Policy ID
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400">
                {{ policy?.policy_id }}
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                Receiving address
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400 w-96 break-words">
                {{ address }}
            </div>
        </div>
    </div>
    <div class="mt-5" v-if="showDiv">
        <p class="w-full mb-3 text-center text-white">
            Please provide an "IPFS" or "Arweave link"
        </p>
        <input type="text" name="" id="" v-model="link"
               class="mb-6 block w-full border-sky-600 rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm text-black"/>
        <button @click="submitImageLink" type="submit"
                class=" mt-3 inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
            Save
        </button>
    </div>
    <button v-else @click="toggleDiv"
            class=" mt-5 inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
        Add Image
    </button>
</template>
<script lang="ts" setup>
import {ref} from 'vue';
import PolicyData = App.DataTransferObjects.PolicyData;
import BallotData = App.DataTransferObjects.BallotData;
import AlertService from '@/shared/Services/alert-service';
import {router} from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps<{
    policy: PolicyData;
    address: string;
    ballot: BallotData;
}>();

const showDiv = ref(false);

const link = ref(null);

const toggleDiv = () => {
    showDiv.value = !showDiv.value;
};

function submitImageLink() {
    axios.post(
        route(
            'admin.ballots.policies.imageLink',
            {
                ballot: props.ballot?.hash,
                policy: props.policy.hash
            }
        ),
        {
            link: link.value
        }
    );
}

</script>
