<template>
    <section>
        <div class="relative">
            <div
                class="overflow-hidden border border-gray-300 rounded-lg shadow-sm dark:border-gray-700 focus-within:border-indigo-500 dark:focus-within:border-indigo-600 focus-within:ring-1 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-500">
                <header
                    class="block w-full p-3 text-gray-900 border-0 resize-none dark:text-gray-100 xl:p-4 sm:text-sm sm:leading-6 bg-slate-200 dark:bg-gray-900">
                    <div class="flex flex-wrap items-center justify-start gap-2 sm:flex-nowrap">
                        <h2 class="text-lg font-medium">
                            {{ snapshot.title }}
                        </h2>
                    </div>
                    <p class="">
                        {{ snapshot.description }}
                    </p>
                </header>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Policy
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ snapshot.policy_id }}
                    </p>
                </div>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Status
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ snapshot.status }}
                    </p>
                </div>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Type
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ snapshot.type }}
                    </p>
                </div>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Created
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ snapshot.created_at }}
                    </p>
                </div>

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <div class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                        Last Updated
                    </div>
                    <p class="relative block w-full flex flex-1 border-0 p-2.5 sm:text-sm sm:leading-6 capitalize font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-slate-200 dark:bg-gray-900 rounded-lg">
                        {{ snapshot.updated_at }}
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

            <div v-if="!snapshot?.live" class="absolute bottom-0 inset-x-px">
                <div
                    class="flex items-center justify-end px-2 py-3 space-x-3 border-t border-gray-200 dark:border-gray-700 sm:px-3">
                    <div class="flex items-center">
                        <DangerButton class="gap-2">
                            <ToolTip type="info" :tip="tipMessage"/>
                            <button @click="removeSnapshot"
                            :disabled="disabled"
                            :class="{ 'opacity-25': disabled }"
                            >Remove</button>
                        </DangerButton>
                    </div>
                    <div class="flex-shrink-0">
                        <Link as="button"
                              :href="route( 'admin.snapshots.edit', { 'snapshot': snapshot?.hash})"
                              class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <span>Edit</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import {Link, router } from '@inertiajs/vue3';
import { ref } from "vue";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import DangerButton from '@/Components/DangerButton.vue';
import ToolTip from '@/Components/ToolTip.vue';
import AlertService from '@/shared/Services/alert-service';
import AdminBallotService from '../../Ballot/Services/admin-ballot-service';

const props = defineProps<{
    status?: String;
    snapshot?: SnapshotData;
    ballot?: String;
}>();

let tipMessage = ref("Click 'Remove' to remove the snapshot from the ballot.");
let disabled = ref(false);

let removeSnapshot = async () => {
    const data = {
        ballot: props.ballot,
        snapshot: props.snapshot?.hash,

    }
    const res = await AdminBallotService.unlinkSnapshot(data);
    if (res) {
        tipMessage.value = res;
        disabled.value = true;
    } else{
        AlertService.show(['Snapshot removed from ballot'], 'success');
        router.reload();
    }
}
</script>
