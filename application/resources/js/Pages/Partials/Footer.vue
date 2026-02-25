<template>
    <footer class="bg-white dark:bg-gray-950/20 relative">
        <section class="container flex flex-row items-center justify-between w-full py-8">
            <div :class="{ 'w-5/6': !$page.props.auth.user }">
                <nav>
                    <ul class="flex gap-4 text-lg flex-nowrap">
                        <li class="w-auto ocv-link">
                            <Link :href="route('home')">
                                Home
                            </Link>
                        </li>

                        <li class="w-auto ocv-link">
                            <a target="_blank" href="https://github.com/DripDropz/onchain-voting/tree/main/docs">
                                Documentation
                            </a>
                        </li>

                        <li class="w-auto ocv-link">
                            <a target="_blank" href="//github.com/DripDropz/onchain-voting">
                                Github
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </section>
        <section class="bg-sky-50 text-slate-700 dark:bg-sky-950 dark:text-sky-100">
            <div class="container py-2 text-xs">
                <div v-if="config.show_created_by">
                    Created with ♡ by
                    <a href="https://dripdropz.io/" target="_blank" class="font-medium hover:underline">
                        DripDropz
                    </a>
                </div>
            </div>
        </section>
    </footer>
</template>
<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import { useConfigStore } from '@/stores/config-store';
import { storeToRefs } from 'pinia';

let configStore = useConfigStore();
let {config} = storeToRefs(configStore);

withDefaults(defineProps<{
    canLogin?: boolean;
    pageData?: any;
}>(), {
    pageData: null
});

</script>
