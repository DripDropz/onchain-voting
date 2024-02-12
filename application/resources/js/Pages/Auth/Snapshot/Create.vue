<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import CreateUpdateSnapshotForm from "@/Pages/Auth/Snapshot/Partials/CreateUpdateSnapshotForm.vue";
import Nav from '../Breadcrumbs.vue';

const props = defineProps<{
    crumbs: []
}>();
</script>

<template>
    <Head title="Snapshot" />

    <AdminLayout>
        <template #header>
            <Nav :crumbs="props.crumbs"/>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <CreateUpdateSnapshotForm class="w-full" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
