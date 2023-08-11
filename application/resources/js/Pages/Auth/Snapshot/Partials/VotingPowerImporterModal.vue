<template>
    <ModalRoute>
        <VotingPowerImporterComponent :snapshot="snapshot"/>
    </ModalRoute>
</template>

<script lang="ts" setup>
import ModalRoute from "@/Components/ModalRoute.vue";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import VotingPowerImporterComponent from '@/Components/VotingPowerImporterComponent.vue';

defineProps<{
    snapshot: SnapshotData;
}>();
</script>
