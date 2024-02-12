<template>
    <div class="flex items-center w-96">
        <div class="flex flex-col gap-2">
            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                Wallet balance
            </div>
            <div
                class="text-xs text-gray-600 break-words dark:text-gray-400 w-96"
            >
                <span
                    v-if="loading"
                    class="px-3 py-1 text-xs font-medium leading-none text-center text-blue-800 bg-blue-200 rounded-full animate-pulse dark:bg-blue-900 dark:text-blue-200"
                    >loading...</span
                >
                <span v-if="!loading">{{ policy?.wallet_balance }} Ada</span>
            </div>
        </div>

        <button
            v-if="!loading"
            @click="refreshBalance"
            type="button"
            class="rounded bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
        >
            Refresh
        </button>
    </div>
</template>
<script lang="ts" setup>
import { ref } from "vue";
import PolicyData = App.DataTransferObjects.PolicyData;
import { router } from "@inertiajs/vue3";

const props = defineProps<{
    policy: PolicyData;
}>();

let loading = ref(false);

function refreshBalance(){
    router.reload({
        onStart: () => {
            loading.value = true;
        },
        onFinish: () => {
            loading.value = false;
        },
    })
}
</script>
