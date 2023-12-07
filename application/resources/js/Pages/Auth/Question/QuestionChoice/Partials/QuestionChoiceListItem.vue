<script setup lang="ts">
import AlertService from '@/shared/Services/alert-service';
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import BallotData = App.DataTransferObjects.BallotData;
import QuestionData = App.DataTransferObjects.QuestionData;
import {Link} from '@inertiajs/vue3';
import {useForm} from '@inertiajs/vue3';

const props = defineProps<{
    choice: QuestionChoiceData;
    ballot: BallotData;
    question: QuestionData;
}>();

const form = useForm({
    choice: props.choice.hash,
});

function submit() {
    form.delete(route('admin.ballots.questions.choices.delete', {ballot: props.ballot?.hash, question: props.question?.hash, choice: props.choice?.hash}), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            AlertService.show(['Choice deleted successfully'], 'success');
            close();
        },
        onError: (errors) => {
            AlertService.show(
                Object
                .entries(errors)
                .map(([key, value]) => value)
            );
        },
    });
}
</script>

<template>
    <li>
        <div class="flex items-center justify-between px-2 py-5 rounded-sm gap-x-6">
            <div class="flex gap-x-4">
                <div class="flex-auto min-w-0">
                    <p
                        class="text-sm font-semibold leading-6 text-black dark:text-white"
                    >
                        {{ choice.title }}
                    </p>
                    <p class="mt-1 overflow-hidden text-xs leading-5 text-gray-600 dark:text-white">
                        {{ choice.description }}
                    </p>
                </div>
            </div>
           <div class="flex items-center space-x-4">
            <Link as="button"
                :preserve-scroll="false"
                :preserve-state="false"
                :href="route(
                    'admin.ballots.questions.choices.choice.edit',
                    { 'ballot': ballot.hash, 'question': question.hash, 'choice': choice.hash})">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </Link>
            <button @click.prevent="submit" v-if="ballot.status !== 'published'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
           </div>

            <!--        <div class="hidden sm:flex sm:flex-col sm:items-end">-->
            <!--            <p class="text-sm leading-6 text-white">{{ choice.hash }}</p>-->
            <!--        </div>-->
        </div>
    </li>
</template>
<style scoped>
.drag {
     @apply bg-gray-900;
     @apply dark:bg-indigo-600;

}
.ghost {
    background: lightgray;
}
 .ghost > div {
    visibility: hidden;
 }
</style>
