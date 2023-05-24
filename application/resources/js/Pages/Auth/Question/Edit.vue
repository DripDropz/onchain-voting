<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import QuestionData = App.DataTransferObjects.QuestionData;
import CreateUpdateQuestionForm from './Partials/CreateUpdateQuestionForm.vue';


const props = defineProps<{
    ballot: BallotData;
    question: QuestionData
}>();
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Editing <b class="font-bold">{{question.title}}</b> Question</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <CreateUpdateQuestionForm
                        :ballot="ballot"
                        :question="question"
                        :questionStatuses=[question?.status]
                        :questionTypes=[question?.type]
                        class="max-w-xl" />
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
