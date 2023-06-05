<template>
    <div class="flex flex-col justify-center gap-4">
        <header class="mb-2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Import Voting Power
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Import a csv file containing staking_address,voting_power of all the members of your organization.
            </p>
        </header>

        <div class="col-span-full">
            <div class="flex justify-center px-6 py-10 mt-2 border border-dashed rounded-lg border-gray-900/25 dark:border-gray-700">
                <div class="text-center ">
                    <FolderOpenIcon class="w-12 h-12 mx-auto text-gray-300" aria-hidden="true" />
                    <div class="flex mt-4 text-sm leading-6 text-gray-600 dark:text-gray-100">
                        <label for="file-upload" class="relative font-semibold text-indigo-600 rounded-md cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                            <span>Upload a file</span>
                            <input id="file-upload" name="file-upload" type="file" class="sr-only" />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs leading-5 text-gray-600 dark:text-gray-100">csv up to 100MB</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { FolderOpenIcon } from '@heroicons/vue/24/outline';
</script>
