<template>
    <div>
        <CreateBallotPolicyWallet v-model:seedphrase="form.seedphrase" v-if="creatingWallet "/>

        <ImportBallotPolicyWallet v-if=" importWallet" />

        <div v-if="(creatingWallet || importWallet)" >
           <span class="flex my-2 ">
            <button @click.prevent ="changeOption" class="flex flex-row items-center justify-center font-bold leading-6 text-sky-600 rounded-md hover:text-sky-500 xl:text-xl">
                {{ creatingWallet ? 'Import' : 'Create' }}
            </button>
            <span class="ml-2"> Instead.</span>
           </span>
        </div>

        <div v-if="!(creatingWallet  || importWallet)"   class="flex flex-col justify-center gap-8 p-16 overflow-hidden border border-gray-300 border-dashed rounded-xl dark:border-gray-700">
            <button @click.prevent="creatingWallet = true"
                class="flex flex-row items-center justify-center gap-2 px-6 py-4 leading-6 text-gray-500 bg-gray-200 rounded-md focus-0 dark:bg-gray-900 dark:hover:bg-gray-950 text-md xl:text-xl dark:text-gray-400">
                <PlusIcon class="w-6 h-6" />
                <span class="text-sm">Create new Wallet</span>
            </button>
            <button @click.prevent="importWallet = true"
                class="flex flex-row items-center justify-center gap-2 px-6 py-4 leading-6 text-gray-500 bg-gray-200 rounded-md focus-0 dark:bg-gray-900 dark:hover:bg-gray-950 text-md xl:text-xl dark:text-gray-400">
                <ArrowDownTrayIcon class="w-5 h-5" />
                <span class="text-sm">Import existing seedphrase</span>
            </button>
        </div>
    </div>
</template>
<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3';
import { ArrowDownTrayIcon, PlusIcon } from '@heroicons/vue/20/solid';
import BallotData = App.DataTransferObjects.BallotData;
import { ref } from 'vue';
import CreateBallotPolicyWallet from './CreateBallotPolicyWallet.vue';
import ImportBallotPolicyWallet from './ImportBallotPolicyWallet.vue';

let creatingWallet = ref(false);
let importWallet = ref(false);

let changeOption = () => {
    creatingWallet.value = !creatingWallet.value
    importWallet.value = !importWallet.value
}

defineProps<{
    ballot: BallotData;
    form: InertiaForm<{ seedphrase: string }>
}>();
</script>
