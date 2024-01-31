<template>
    <div class="p-4 bg-white dark:bg-gray-800 space-y-6">
        <div>
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-sky-300">
                <SignalIcon class="h-7 w-7" aria-hidden="true" />
            </div>
            <div class="mt-3 text-center sm:mt-5">
                <h1 class="font-semibold leading-6 text-gray-900 dark:text-white text-lg">
                    Are you ok with the petition's criteria?
                </h1>
                <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-white">
                        In order for folks to sign your petition they'll need to
                        meet the following criteria:

                        <span class="font-extrabold">
                            Staked to NEWM NFT held:Drippyz, FT amount: 100A
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="flex justify-center gap-10">
            <button
                type="button"
                class="mt-3 inline-flex justify-center rounded-md bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-100 sm:col-start-1 sm:mt-0"
                @click="$emit('close')"
                ref="cancelButtonRef">
                Nevermind
            </button>
            <Link :href="route('petitions.create')"
                  class="inline-flex justify-center rounded-md bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:col-start-2"
            >
                Yes, that works
            </Link>
        </div>
    </div>
</template>
<script setup lang="ts">
import {SignalIcon} from '@heroicons/vue/24/outline'
import {Link} from '@inertiajs/vue3';
</script>
