<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import QuestionData = App.DataTransferObjects.QuestionData;
import CreateUpdateQuestionForm from './Partials/CreateUpdateQuestionForm.vue';
import Nav from '../Breadcrumbs.vue';


const props = defineProps<{
    ballot: BallotData;
    question: QuestionData;
    crumbs: []
}>();
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <Nav :crumbs="props.crumbs"/>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <CreateUpdateQuestionForm
                        :ballot="ballot"
                        :question="question"
                        :questionStatuses=[question?.status]
                        :questionTypes=[question?.type]
                        class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
