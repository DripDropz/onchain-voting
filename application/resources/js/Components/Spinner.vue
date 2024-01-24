<template>
    <div class="absolute top-0 left-0 z-30 flex flex-col items-center justify-center w-full h-full">
        <spinner class="relative z-30" color="yellow" size="10" />
        <div v-if="props.message" class="mt-3">
            {{ props.message }}
        </div>
    </div>
</template>
<script lang="ts" setup>
import { Spinner } from 'flowbite-vue';

const props = defineProps<{
    message?: string;
}>();
</script>
