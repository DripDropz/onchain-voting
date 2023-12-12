<template>
    <div
        class="overflow-hidden border border-gray-300 border-dashed rounded-xl dark:border-gray-700 hover:border-sky-600">
        <Link as="button"
            :preserve-scroll="false"
            :preserve-state="false"
            :href="route(
                  'admin.ballots.questions.choices.create',
                   { 'ballot': ballot.hash, 'question': question.hash})"
              class="flex flex-col items-center justify-center w-full gap-2 px-6 py-4 leading-6 text-gray-500 text-md xl:text-xl dark:text-gray-400 h-28">
            <PlusIcon class="w-6 h-6 mb-1"/>
            <span>Add Choice</span>
        </Link>
    </div>
</template>
<script setup lang="ts">
import {Link} from '@inertiajs/vue3';
import {PlusIcon} from '@heroicons/vue/20/solid';
import QuestionData = App.DataTransferObjects.QuestionData;
import BallotData = App.DataTransferObjects.BallotData;

defineProps<{
    ballot: BallotData;
    question: QuestionData;
}>();

</script>
