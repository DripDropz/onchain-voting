<template>
    <div
        class="py-16 overflow-hidden border border-gray-300 border-dashed rounded-xl dark:border-gray-700 hover:border-sky-600">
        <Link as="button" :href="route('admin.ballots.snapshots.add', {'ballot': ballot.hash})"
              class="flex flex-col items-center justify-center w-full h-full gap-2 px-6 py-4 leading-6 text-gray-500 text-md xl:text-xl dark:text-gray-400">
            <PlusIcon class="w-6 h-6"/>
            <span>Add a Snapshot</span>
        </Link>
    </div>
</template>
<script setup lang="ts">
import {Link} from '@inertiajs/vue3';
import {PlusIcon} from '@heroicons/vue/20/solid';
import BallotData = App.DataTransferObjects.BallotData;

defineProps<{
    ballot: BallotData;
}>();
</script>
