<template>
    <Link :href="route('home')">
        <img :src="config.logo ?? voteAppLogo" alt="Open Chainvote App Logo" class="w-10 h-10">
    </Link>
</template>

<script lang="ts" setup>
import {useConfigStore} from '@/stores/config-store';
import {storeToRefs} from 'pinia';
import {Link} from '@inertiajs/vue3';
import voteAppLogo from '../../images/openchainvote.png';

let configStore = useConfigStore();
let {config} = storeToRefs(configStore);
</script>
