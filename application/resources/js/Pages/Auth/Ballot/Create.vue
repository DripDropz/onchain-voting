<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import CreateUpdateBallotForm from "@/Pages/Auth/Ballot/Partials/CreateUpdateBallotForm.vue";
import Nav from '../Breadcrumbs.vue';

defineProps<{
    status?: string;
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
                    <CreateUpdateBallotForm
                        :status="status"
                        class="w-full"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
