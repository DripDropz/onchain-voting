<template>
    <li @click="props.isBallotOpen ? emit('selected', [choice]) : ''">
        <label :for="choice.hash" :class="[props.isBallotOpen ? 'w-full cursor-pointer' : 'w-full cursor-not-allowed opacity-75']">
            <input type="radio" class="sr-only peer"
                   :name="choice.hash" :value="choice.hash"
                   :checked="choice.selected"
                   :disabled="!props.isBallotOpen" />

            <span
                class="w-full block p-3 mb-3 text-white transition-all border-2 border-white rounded-full hover:shadow peer-checked:text-sky-700 peer-checked:bg-sky-100 peer-checked:border-transparent hover:bg-sky-700">
                <span class="flex items-center justify-between ">
                    <span class="pr-8 text-sm font-semibold">{{
                        choice.title
                    }}</span>
                    <span class="flex-shrink-0 w-4 h-4">
                        <svg fill='currentColor' id="Layer_1_1_" style="enable-background:new 0 0 16 16;" version="1.1"
                            viewBox="0 0 16 16" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <circle cx="8" cy="8" r="8" />
                        </svg>
                    </span>
                </span>
            </span>
        </label>
    </li>
</template>
<script lang="ts" setup>
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;

const emit = defineEmits<{
    (e: 'selected', choices: QuestionChoiceData[]): void
}>();

const props = defineProps<{
    choice: QuestionChoiceData;
    isBallotOpen: boolean
}>();
</script>
