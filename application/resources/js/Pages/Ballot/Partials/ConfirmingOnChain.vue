<template>
    <div
        class="absolute top-0 left-0 flex items-center justify-center w-full h-full p-8 text-white rounded-lg bg-sky-800/50">
        <Spinner class="z-30" color="yellow" size="30" :message="'Confirming registration onchain ...'" />
    </div>
</template>
<script lang="ts" setup>
import Spinner from '@/Components/Spinner.vue';
</script>
