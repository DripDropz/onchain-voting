<template>
    <Head title="Ballot" />

    <AdminLayout>
        <template #header>
            <Nav :crumbs="props.crumbs"/>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <CreateUpdateSnapshotForm :snapshot="snapshot" class="w-full" />
                </section>

                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <VotingPowerList v-if="votingPowers?.length > 0" :snapshot="snapshot"/>
                    <VotingPowerImporterComponent v-else class="max-w-xl" :snapshot="snapshot"/>
                </section>

                <section class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                    <DeleteSnapshotForm :snapshot="snapshot" class="max-w-xl" />
                </section>
            </div>
        </div>
    </AdminLayout>
</template>
<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from "@inertiajs/vue3";
import { computed, ComputedRef} from "vue";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import VotingPowerData = App.DataTransferObjects.VotingPowerData;
import AlertService from '@/shared/Services/alert-service';
import CreateUpdateSnapshotForm from "@/Pages/Auth/Snapshot/Partials/CreateUpdateSnapshotForm.vue";
import DeleteSnapshotForm from "@/Pages/Auth/Snapshot/Partials/DeleteSnapshotForm.vue";
import VotingPowerList from './Partials/VotingPowerList.vue';
import VotingPowerImporterComponent from '@/Components/VotingPowerImporterComponent.vue';
import { useSnapshotStore } from '@/stores/snapshot-store';
import { storeToRefs } from 'pinia';
import Nav from '../Breadcrumbs.vue';

const props = defineProps<{
    snapshot: SnapshotData;
    crumbs: []
}>();

let snapshotHash:ComputedRef<any> = computed(() => props.snapshot.hash);

if (usePage().props?.errors) {
    AlertService.show(Object.values(usePage().props.errors), 'info');
}

let snapshotStore = useSnapshotStore();
snapshotStore.loadVotingPowers(snapshotHash.value);
let { votingPowersData} = storeToRefs(snapshotStore);
const votingPowers: ComputedRef<VotingPowerData[]> = computed(() => votingPowersData.value);
</script>
