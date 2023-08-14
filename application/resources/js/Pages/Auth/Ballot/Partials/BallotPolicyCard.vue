<template>
<div class="flex flex-col gap-4">
    <div class="flex flex-row justify-between">
        <div class="flex flex-col gap-2">
            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                <slot></slot>
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400">
                This policy will be used to register voters.
            </div>
        </div>
    </div>
    <div class="flex flex-col gap-2">
        <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
            Policy Script
        </div>
        <div class="text-xs text-gray-600 dark:text-gray-400">
            {{ policy?.script }}
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
            Policy ID
        </div>
        <div class="text-xs text-gray-600 dark:text-gray-400">
            {{ policy?.policy_id }}
        </div>
    </div>
</div>
</template>
<script lang="ts" setup>
import PolicyData = App.DataTransferObjects.PolicyData;
defineProps<{
    policy: PolicyData;
}>();
</script>
