<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import CreateUpdateSnapshotForm from "@/Pages/Auth/Snapshot/Partials/CreateUpdateSnapshotForm.vue";

defineProps<{}>();
</script>

<template>
    <Head title="Snapshot" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create Snapshot</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <CreateUpdateSnapshotForm class="w-full" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
