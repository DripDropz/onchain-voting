<template>
<Link :preserve-scroll="true" :href="route('login.wallet', {hash: pageData.hash})" class="absolute top-0 left-0 flex items-center justify-center w-full h-full p-8 text-sky-700 rounded-lg bg-sky-800/50">
    <span class="px-4 py-2 text-lg font-bold bg-sky-100 rounded-full xl:text-text">
        Login to Vote
    </span>
</Link>
</template>
<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';

defineProps<{
    pageData?:any
}>()

</script>
