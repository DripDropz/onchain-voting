<template>
    <ModalRoute v-if="! showComponent">
        <VotingPowerImporterComponent :snapshot="snapshot"/>
    </ModalRoute>
    <div v-else>
        <VotingPowerImporterComponent :snapshot="snapshot"/>
    </div>
</template>

<script lang="ts" setup>
import ModalRoute from "@/Components/ModalRoute.vue";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import { computed } from "vue";
import VotingPowerImporterComponent from '@/Components/VotingPowerImporterComponent.vue';

const props = defineProps<{
    snapshot: SnapshotData;
    asComponent?: boolean;
}>();

const showComponent = computed(() => {
    return props.asComponent == true ? true : false;
});
</script>
