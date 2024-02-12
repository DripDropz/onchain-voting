<script setup lang="ts">
import BallotData = App.DataTransferObjects.BallotData;
import ModalRoute from "@/Components/ModalRoute.vue";
import CreateUpdateQuestionForm from "@/Pages/Auth/Question/Partials/CreateUpdateQuestionForm.vue";
import Nav from '../Breadcrumbs.vue';

defineProps<{
    ballot: BallotData;
    questionTypes: string[];
    questionsStatuses: string[];
    crumbs: []
}>();
</script>

<template>
    <ModalRoute>
        <div class="flex flex-col">
            <div class="max-w-7xl space-y-6 p-6">
                <CreateUpdateQuestionForm
                    :ballot="ballot"
                    :question-statuses="questionsStatuses"
                    :question-types="questionTypes"
                    class="max-w-xl" />
            </div>
        </div>
    </ModalRoute>
</template>
