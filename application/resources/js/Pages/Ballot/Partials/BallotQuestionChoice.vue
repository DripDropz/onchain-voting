<style lang="scss"></style>
<template>
    <li @click="emit('selected', choice)">
        <label class="w-full cursor-pointer">
            <input type="radio" class="sr-only peer" name="choice" :hash="choice.hash" :value="choice.hash" :checked="choice.selected" />
            <div
                class="w-full p-3 text-white transition-all border-2 border-white rounded-full hover:shadow peer-checked:text-indigo-700 peer-checked:bg-indigo-100 peer-checked:ring-indigo-700 peer-checked:ring-4 peer-checked:border-transparent hover:bg-indigo-700">
                <div class="flex items-center justify-between ">
                    <p class="pr-8 text-sm font-semibold">{{
                        choice.title
                    }}</p>
                    <div class="flex-shrink-0 w-4 h-4">
                        <svg fill='currentColor' id="Layer_1_1_" style="enable-background:new 0 0 16 16;" version="1.1"
                            viewBox="0 0 16 16" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <circle cx="8" cy="8" r="8" />
                        </svg>
                    </div>
                </div>
            </div>
        </label>
    </li>
</template>
<script lang="ts" setup>
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;

const emit = defineEmits<{
    (e: 'selected', choice: QuestionChoiceData): void
}>();

const props = defineProps<{
    choice: QuestionChoiceData;
}>();
</script>
