<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useGlobalAlert } from "@/store/global-alert-store";
import setAlert from "@/utils/set-alert";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import SnapshotCard from "@/Pages/Auth/Snapshot/Partials/SnapshotCard.vue";

defineProps<{
    snapshot: SnapshotData;
}>();

const alertStore = useGlobalAlert();
const errors1 = computed(() => usePage().props.errors).value;
if (errors1.error) {
    alertStore.showAlert(setAlert(errors1.error, 'info'))
}
</script>

<template>
    <Head title="Ballot" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Viewing
                    <b>{{ snapshot.title }}</b> Snapshot</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <SnapshotCard :snapshot="snapshot" class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>