<template>
    <draggable
        v-model="questionChoices"
        itemKey="questionChoices.title"
        group="questionChoices"
        tag="ul"
        class="overflow-auto divide-y divide-gray-800 max-h-80 cursor-pointer"
        drag-class="drag"
        ghost-class="ghost"
        @change="updatePosition"
    >
        <template #item="{ element: questionChoice }">
            <QuestionChoiceListItem :choice="questionChoice" />
        </template>
    </draggable>
</template>

<script setup lang="ts">
import { ref } from "vue";
import draggable from "vuedraggable";
import axios from "axios";
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import QuestionChoiceListItem from "@/Pages/Auth/Question/QuestionChoice/Partials/QuestionChoiceListItem.vue";

const props = withDefaults(
    defineProps<{
        choices?: QuestionChoiceData[];
    }>(),
    {}
);

let questionChoices = ref(props.choices);

function updatePosition(e: any) {
    const movedChoice = e.moved;

    if (!movedChoice) return;

    const index = movedChoice.newIndex;
    const choice = questionChoices.value?.[index];
    const prevChoice = questionChoices.value?.[index - 1];
    const nextChoice = questionChoices.value?.[index + 1];

    let position;

    if (prevChoice && nextChoice) {
        position = (prevChoice.order + nextChoice.order) / 2;
    } else if (prevChoice) {
        position = prevChoice.order + 1;
    } else if (nextChoice) {
        position = nextChoice.order / 2;
    } else {
        position = 0;
    }

    choice.order = position;

    axios.post(route("update.position", { order: position, hash: choice.hash }));

    questionChoices.value = [...questionChoices.value];
}

</script>
