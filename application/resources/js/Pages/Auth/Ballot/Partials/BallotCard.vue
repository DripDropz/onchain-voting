<template>
    <section>
        <div class="relative">
            <div
                class="overflow-hidden border border-gray-300 rounded-lg shadow-sm dark:border-gray-700 focus-within:border-sky-500 dark:focus-within:border-sky-600 focus-within:ring-1 focus-within:ring-sky-500 dark:focus-within:ring-sky-500">
                <header
                    class="block w-full p-3 text-gray-900 border-0 resize-none dark:text-gray-100 xl:p-4 sm:text-sm sm:leading-6 bg-slate-200 dark:bg-gray-900">
                    <div class="flex flex-wrap items-center justify-start gap-2 sm:flex-nowrap">
                        <h2 class="text-lg font-medium">
                            {{ ballot.title }}
                        </h2>
                        <BallotStatusBadge :ballot="ballot"></BallotStatusBadge>
                    </div>
                    <p class="">
                        {{ ballot.description }}
                    </p>
                </header>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Ballot Start
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ ballot.started_at }}
                    </p>
                </div>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Ballot End
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ ballot.ended_at }}
                    </p>
                </div>


                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">Version</div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ ballot.version }}
                    </p>
                </div>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Ballot Status
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ ballot.status }}
                    </p>
                </div>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Ballot Type
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ ballot.type }}
                    </p>
                </div>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Last Updated
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ ballot.updated_at }}
                    </p>
                </div>

                <div aria-hidden="true">
                    <div class="py-3">
                        <div class="h-10"/>
                    </div>
                    <div class="h-px"/>
                    <div class="py-3">
                        <div class="py-px">
                            <div class="h-10"/>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!ballot?.live" class="absolute bottom-0 inset-x-px">
                <div
                    class="flex items-center justify-between px-2 py-3 space-x-3 border-t border-gray-200 dark:border-gray-700 sm:px-3">
                    <div class="flex">

                    </div>
                    <div class="flex-shrink-0">
                        <Link as="button"
                              :href="route( 'admin.ballots.edit', { 'ballot': ballot?.hash})"
                              class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                            <span>Edit</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import {Link, usePage} from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import BallotStatusBadge from "@/Pages/Auth/Ballot/Partials/BallotStatusBadge.vue";

defineProps<{
    status?: String;
    ballot?: BallotData;
}>();

</script>
