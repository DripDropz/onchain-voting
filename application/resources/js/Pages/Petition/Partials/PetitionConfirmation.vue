<template>
    <div class="p-6 bg-white dark:bg-gray-800 space-y-6 text-center">
        <div>
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-sky-100 dark:bg-sky-900/40">
                <SignalIcon class="h-7 w-7 text-sky-500" aria-hidden="true" />
            </div>
            <div class="mt-4 space-y-2">
                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Sign in to create a petition
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    You need to be logged in to create a petition on OpenChainVote.
                </p>
            </div>
        </div>
        <div class="flex justify-center gap-3">
            <button
                type="button"
                class="inline-flex justify-center rounded-md bg-white dark:bg-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600"
                @click="$emit('close')"
            >
                Cancel
            </button>
            <button
                @click.prevent="goToLogin"
                class="inline-flex justify-center rounded-md bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
            >
                Sign in
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { SignalIcon } from '@heroicons/vue/24/outline';
import { router } from '@inertiajs/vue3';

const emit = defineEmits(['close']);

function goToLogin() {
    emit('close');
    router.visit(route('login.wallet'));
}
</script>
