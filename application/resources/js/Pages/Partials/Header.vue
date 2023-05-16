<template>
    <div class="flex flex-row justify-between w-full py-3">
        <div>
            <nav>
                <ul class="flex gap-3 flex-nowrap">
                    <li class="ocv-link w-auto">
                       <Link :href="route('home')">
                           Open Chainvote
                       </Link>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="flex flex-row justify-end gap-3" v-if="canLogin">
            <Link
                v-if="$page.props.auth.user"
                :href="route('admin.dashboard')"
                class="ocv-link"
            >Dashboard
            </Link>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="ocv-link"
                >Log in
                </Link>
            </template>
        </div>
    </div>
</template>
<script lang="ts" setup>
import {Head, Link} from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
}>();
</script>
