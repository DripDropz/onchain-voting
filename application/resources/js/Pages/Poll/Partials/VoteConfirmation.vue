<template>
    <div class="p-4 bg-white dark:bg-gray-800 space-y-6">
        <div>
            <div
                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-sky-300"
            >
                <CheckBadgeIcon class="h-7 w-7" aria-hidden="true" />
            </div>
            <div class="mt-3 text-center sm:mt-5">
                <h1
                    class="font-semibold leading-6 text-gray-900 dark:text-white text-lg"
                >
                    Submit your vote and make your voice heard!
                </h1>
                <div class="mt-2">
                    <p v-if="poll.publish_on_chain" class="text-sm text-gray-500 dark:text-white">
                        Results are stored immutably on the blockchain, ensuring
                        transparency and trust.
                    </p>
                    <p v-if="!poll.publish_on_chain" class="text-sm text-gray-500 dark:text-white">
                        The poll results will not be recorded on the blockchain.
                    </p>
                    <span class="font-extrabold text-gray-500">
                        Publish onchain: {{ poll.publish_on_chain }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex justify-center gap-10">
            <button
                type="button"
                class="mt-3 inline-flex justify-center rounded-md bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-100 sm:col-start-1 sm:mt-0"
                @click.prevent="$emit('close')"
                ref="cancelButtonRef"
            >
                Nevermind
            </button>
            <button
                @click.prevent="$emit('submit')"
                class="inline-flex justify-center rounded-md bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:col-start-2"
            >
                Yes, that works
            </button>
        </div>
    </div>
</template>
<script setup lang="ts">
import { CheckBadgeIcon } from "@heroicons/vue/24/outline";
import PollData = App.DataTransferObjects.PollData;

withDefaults(
    defineProps<{
        poll: PollData;
    }>(),
    {}
);
</script>
