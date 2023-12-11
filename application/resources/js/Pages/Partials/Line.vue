<template>
    <span class="h-2 w-full flex flex-1 mt-3" :class="classes"></span>
</template>
<script lang="ts" setup>
    withDefaults(defineProps<{
      classes?: string[];
    }>(), {
      classes: () => ['bg-sky-500', 'dark:bg-sky-100']
    });
</script>
