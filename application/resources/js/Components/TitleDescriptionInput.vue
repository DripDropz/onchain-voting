<script setup lang="ts">
import {onMounted, ref} from 'vue';

defineProps<{
    modelValue: { title: string, description: string };
}>();

const emit = defineEmits(['update:modelValue']);

const input = ref<HTMLTextAreaElement | null>(null);
let title = ref<HTMLInputElement | null>(null);
let description = ref<HTMLTextAreaElement | null>(null);
let updateModelValue = () => {
    console.log('description::', description.value);
    emit('update:modelValue', {
        title: (title as HTMLInputElement).value,
        description: (description as HTMLTextAreaElement).value
    });
}

onMounted(() => {
    if (title.value?.hasAttribute('autofocus')) {
        title.value?.focus();
    }
    if (description.value?.hasAttribute('autofocus')) {
        description.value?.focus();
    }
});

defineExpose({focus: () => title.value?.focus() || description.value?.focus()});
</script>

<template>
    <label for="title" class="sr-only">Title</label>
    <input type="text" name="title" id="title" :value="modelValue?.title" ref="title"
           @input="updateModelValue"
           class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-white dark:bg-gray-900"
           placeholder="Title"/>

    <label for="description" class="sr-only">Description</label>
    <textarea rows="4"
              name="description"
              id="description"
              :value="modelValue?.description"
              ref="description"
              @input="updateModelValue"
              class="block w-full py-0 pb-4 text-gray-900 bg-white border-0 resize-none dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6 dark:bg-gray-900"
              placeholder="Write a description..."/>


    <!--    <textarea-->
    <!--        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"-->
    <!--        :value="modelValue"-->
    <!--        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"-->
    <!--        ref="input"-->
    <!--    />-->

</template>
