<template>
    <div class="relative">
        <div @click="isOpen = !isOpen">
            <button class="flex gap-2 items-center">
                <slot name="trigger" />
                <InformationCircleIcon :class="`w-${props.iconSize} h-${props.iconSize}`" aria-hidden="true" />
            </button>
        </div>
        <div
            v-show="isOpen"
            class="fixed inset-0"
            @click="isOpen = false"
        ></div>
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="isOpen"
                class="absolute z-20 mt-4 right-0 shadow-md w-auto content-stretch overflow-visible"
                @click="isOpen = false"
            >
                <div class="relative w-[420px]">
                    <slot name="content" />
                </div>
            </div>
        </transition>
    </div>
</template>

<script lang="ts" setup>
import { defineProps, ref } from "vue";
import { InformationCircleIcon } from "@heroicons/vue/20/solid";

const props = defineProps({
  iconSize: {
    type: String,
  }
})
const isOpen = ref(false);
</script>
