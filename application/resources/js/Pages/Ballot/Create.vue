<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import CreateUpdateBallotForm from "@/Pages/Ballot/Partials/CreateUpdateBallotForm.vue";

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create Ballot</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <CreateUpdateBallotForm
                        :status="status"
                        class="max-w-2xl"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
