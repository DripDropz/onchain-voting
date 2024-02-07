<template>
    <div class="flex flex-col h-full items-center">
        <ul v-if="actions?.length > 0" v-for="action in actions">
            <li>
                <NavLink type="button" :href="action.link" v-if="!!action.link"
                         class="px-3 py-0.5 text-xs font-semibold text-white rounded shadow-sm bg-sky-500 dark:text-white hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                >
                    {{action.label}}
                </NavLink>
                <PrimaryButton
                    :theme="'primary'"
                    @click="configStore.toggleModal()"
                    v-if="!!action.clickAction" :disabled="action.disabled"
                    :otherClasses="action.disabled ? 'cursor-not-allowed' :''"
                    class="px-2 py-1 text-xs font-semibold text-white rounded-lg shadow-sm bg-sky-500 dark:text-white hover:bg-primary-500 hover:text-black focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                >
                    {{action.label}}
                </PrimaryButton>
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import NavLink from "@/Components/NavLink.vue";
import PrimaryButton from "./PrimaryButton.vue";
import { useConfigStore } from "@/stores/config-store";

let configStore = useConfigStore();

defineProps<{
    actions?: any[],
}>();

</script>
