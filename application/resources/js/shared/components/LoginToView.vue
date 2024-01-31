<template>
    <div v-if="!user" class="flex flex-col items-center justify-center gap-10">
        <p class="text-2xl font-bold text-center ">
            <slot></slot>
        </p>
        <Link :href="route('login.wallet')"
              class="inline-flex items-center px-8 py-2 font-semibold text-white border rounded-md shadow-sm gap-x-2 bg-sky-500 hover:bg-slate-600 hover:cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 border-sky-400">
            Login
        </Link>
    </div>
</template>

<script lang="ts" setup>
import UserData = App.DataTransferObjects.UserData;
import {Link} from '@inertiajs/vue3';


withDefaults(defineProps<{
    user?: UserData;
}>(), {});

</script>
