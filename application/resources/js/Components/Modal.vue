<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import { useConfigStore } from '@/stores/config-store';
import { storeToRefs } from 'pinia';
import { onClickOutside } from '@vueuse/core';

const props = withDefaults(
    defineProps<{
        show?: boolean;
        maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
        closeable?: boolean;
        modalType?: string;
    }>(),
    {
        show: false,
        maxWidth: '2xl',
        closeable: true,
        modalType: 'default',
    }
);

const emit = defineEmits(['close']);
let configStore = useConfigStore()
let { showModal, showPublishModal } = storeToRefs(configStore);
const target = ref(null);
onClickOutside(target, (event) => close());


watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'visible';
        }
    }
);

const close = () => {
    if (props.closeable) {
        if (props.modalType === 'publish') {
            showPublishModal.value = false;
        } else {
            showModal.value = false
        }
        emit('close');
    }
};

const closeOnEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = 'visible';
});

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
});
</script>

<template>
    <teleport to="body">
        <transition leave-active-class="duration-200">
            <div v-show="show" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 overflow-y-auto sm:px-0"
                scroll-region>
                <transition enter-active-class="duration-300 ease-out" enter-from-class="opacity-0"
                    enter-to-class="opacity-100" leave-active-class="duration-200 ease-in" leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
                    <div v-show="show" class="fixed inset-0 transition-all transform" @click="close">
                        <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900" />
                    </div>
                </transition>

                <transition enter-active-class="duration-300 ease-out"
                    enter-from-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
                    enter-to-class="translate-y-0 opacity-100 sm:scale-100" leave-active-class="duration-200 ease-in"
                    leave-from-class="translate-y-0 opacity-100 sm:scale-100"
                    leave-to-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95">
                    <div v-show="show"
                        class="mb-6 overflow-hidden transition-all transform rounded-lg shadow-xl bg-sky-100 dark:bg-gray-800 sm:w-full sm:mx-auto"
                        :class="maxWidthClass">
                        <div class="flex justify-end w-full">
                            <XMarkIcon class="w-5 h-5 mt-2 mr-2 cursor-pointer" @click="close" />
                        </div>
                        <slot v-if="show" ref="target" />
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
