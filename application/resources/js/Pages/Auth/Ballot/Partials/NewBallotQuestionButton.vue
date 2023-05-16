<template>
    <div
        class="overflow-hidden rounded-xl border border-dashed border-gray-300 dark:border-gray-700 hover:border-indigo-600 py-16">
        <Link as="button" :href="route('admin.ballots.questions.create', {'ballot': ballot.hash})"
              class="px-6 py-4 text-md xl:text-xl text-gray-500 dark:text-gray-400 leading-6 flex flex-col justify-center items-center w-full h-full gap-2">
            <PlusIcon class="h-6 w-6"/>
            <span>Add a Question</span>
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
