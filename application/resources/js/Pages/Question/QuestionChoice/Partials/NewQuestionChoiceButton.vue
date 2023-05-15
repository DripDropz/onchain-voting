<template>
    <div
        class="overflow-hidden rounded-xl border border-dashed border-gray-200 dark:border-gray-700 hover:border-indigo-600">
        <Link as="button"
              :href="route(
                  'ballots.questions.choices.create',
                   { 'ballot': question.ballot?.hash, 'question': question.hash})"
              class="px-6 py-4 text-md xl:text-xl text-gray-500 dark:text-gray-400 leading-6 flex flex-col justify-center items-center w-full h-40 gap-2">
            <PlusIcon class="h-6 w-6"/>
            <span>Add Choice</span>
        </Link>
    </div>
</template>
<script setup lang="ts">
import {Link} from '@inertiajs/vue3';
import {PlusIcon} from '@heroicons/vue/20/solid';
import QuestionData = App.DataTransferObjects.QuestionData;

const props = defineProps<{
    question: QuestionData;
}>();

</script>
