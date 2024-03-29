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

        <div class="flex flex-col gap-2 h-20">
            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                Receiving address
            </div>
            <div class="text-xs text-gray-600 break-words dark:text-gray-400 w-96">
                {{ address }}
            </div>
        </div>

        <WalletBalance :policy="policy"/>

        <div class="flex flex-col gap-3 mt-5">
            <div class="flex flex-col gap-2">
                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    Image Link
                </div>
                <div class="text-xs text-gray-600 break-words dark:text-gray-400 w-96">
                    <span v-if="policy?.image_link && !showEditForm && !showAddForm">
                        {{ policy?.image_link }}
                    </span>
                    <span v-else>
                        <p v-if="(showEditForm || showAddForm)">
                            {{ showEditForm ? 'Please provide a new "IPFS" or "Arweave link"' :
                                'Please provide an "IPFS" or "Arweave link"' }}
                        </p>
                        <TextInput v-if="(showEditForm || showAddForm)" type="text" v-model="imageLink"
                            class="block w-full mt-1 text-black rounded-md shadow-sm border-sky-600 focus:border-teal-500 focus:ring-teal-500 sm:text-sm" />
                        <span v-if="!policy?.image_link && !showEditForm && !showAddForm">
                            No image link available.
                        </span>
                    </span>
                </div>
                <div v-if="successMessage">
                    <div class="text-sm font-semibold text-green-500">
                        {{ successMessage }}
                    </div>
                </div>
                <div v-if="errorMessage">
                    <div class="text-sm font-semibold text-red-500">
                        {{ errorMessage }}
                    </div>
                </div>
            </div>

            <div class="flex ">
                <button v-if="!policy?.image_link && !showEditForm && !showAddForm" @click="showAddForm = true"
                    class="inline-flex items-center px-3 py-2 mt-5 text-sm font-semibold text-white rounded-md shadow-sm bg-sky-600 hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                    Add Image
                </button>

                <button v-if="policy?.image_link && !showEditForm && !showAddForm" @click="showEditForm = true"
                    class="inline-flex items-center px-3 py-2 mt-5 text-sm font-semibold text-white rounded-md shadow-sm bg-sky-600 hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                    Edit Image Link
                </button>

                <button v-if="showEditForm || showAddForm" @click="submitImageLink" :disabled="loading"
                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white rounded-md shadow-sm bg-sky-600 hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                    <span v-if="loading" class="mr-2">Submitting...</span>
                    <span>Save</span>
                </button>
            </div>

        </div>



    </div>
</template>
<script lang="ts" setup>
import { ref, defineProps } from 'vue';
import PolicyData = App.DataTransferObjects.PolicyData;
import BallotData = App.DataTransferObjects.BallotData;
import AlertService from '@/shared/Services/alert-service';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import TextInput from '@/Components/TextInput.vue';
import WalletBalance from './WalletBalance.vue';

const props = defineProps<{
    policy: PolicyData;
    address: string;
    ballot: BallotData;
}>();
const showEditForm = ref(false);
const showAddForm = ref(false);
const imageLink = ref(props.policy?.image_link || '');

const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

function submitImageLink() {
    if (!imageLink.value) {
        errorMessage.value = 'Please provide a valid image link.';
        return;
    }
    loading.value = true;

    axios.post(
        route(
            'admin.ballots.policies.imageLink',
            {
                ballot: props.ballot?.hash,
                policy: props.policy.hash
            }
        ),
        {
            link: imageLink.value
        }
    )
        .then(() => {
            successMessage.value = 'Image link submitted successfully.';
            setTimeout(() => {
                successMessage.value = '';
            }, 3000);
        })
        .catch(() => {
            errorMessage.value = 'Error submitting image link. Please try again.';
        })
        .finally(() => {
            loading.value = false;
            showEditForm.value = false;
            showAddForm.value = false;

            props.policy.image_link = imageLink.value;
        });
}

</script>
