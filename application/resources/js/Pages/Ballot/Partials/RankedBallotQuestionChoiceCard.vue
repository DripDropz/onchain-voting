<template>
    <draggable v-model="choices" item-key="choice.hash" group="choice" tag="transition-group"
    class="cursor-pointer "
    :component-data="{
        tag: 'ul',
        type: 'transition-group',
        name: 'fade',
    }" animation="300" ghost-class="ghost" @change="updatePosition($event)" >
        <template #item="{ element: choice }">
            <li class="cursor-move " :key="choice.hash">
                <label class="w-full cursor-pointer" :for="choice.hash">
                    <input type="checkbox" class="opacity-0 peer" name="choice" :checked="choice.selected" :id="choice.hash"
                        @change="toggleCheckbox(choice)" />
                    <div class="w-full p-3 transition-all border-2 border-white rounded-full hover:shadow"
                        :class="{ 'text-sky-700 bg-sky-100 ring-sky-700 ring-4 border-transparent ': choice.selected, 'text-white hover:bg-sky-700': !choice.selected }">
                        <div class="flex items-center justify-between ">
                            <p :for="choice.hash" class="pr-8 text-sm font-semibold">
                                {{ choice.title }}</p>
                            <div class="flex-shrink-0 px-1 py-0 rounded-full ">
                                <span v-html="choice.selected ? getPosition(choice.hash) : ''">
                                </span>
                            </div>
                        </div>
                    </div>
                </label>
            </li>
        </template>
    </draggable>
</template>

<script setup lang="ts">
import { Ref, computed, ref, watch } from "vue";
import QuestionChoiceData = App.DataTransferObjects.QuestionChoiceData;
import draggable from "vuedraggable";

const props = defineProps<{
    choices: QuestionChoiceData[];
    // response: QuestionData;
}>();

let choices = ref(props.choices);
let drag = ref(false);

const selectedChoices = computed(() => {
    return choices.value?.filter(choice => choice.selected)
        .map((choice, index) => ({ hash: choice.hash, index: index }));
});

watch(selectedChoices, () => {
    emit('ranked', selectedChoices.value);
});

const emit = defineEmits<{
    (event: 'ranked', choices): void
}>()

function toggleCheckbox(choice) {
    choice.selected = !choice.selected;
    rearrangeAutomatically(choice);
}

function rearrangeAutomatically(changedChoice) {
    const hash = changedChoice.hash;
    const index = findIndex(hash);

    const selectedCount = choices.value.filter(i => i.selected).length;

    if (selectedCount === 1 && changedChoice.selected) {
        let [item] = choices.value.splice(index, 1);
        choices.value.unshift(item);
    } else {
        if (changedChoice.selected) {
            let [item] = choices.value.splice(index, 1);
            choices.value.splice(selectedCount - 1, 0, item);
        } else {
            let [item] = choices.value.splice(index, 1);
            choices.value.push(item);
        }
    }
}


let updatePosition = (e) => {
    let hash;
    if (e.moved) {
        hash = e.moved.element.hash;
        const index = findIndex(hash);
        choices.value[index]['selected'] = true;
    } else {

    }
};

function findIndex(hash) {
    return choices.value.findIndex((item) => item.hash == hash)
}

let getPosition = (hash) => {
    let index = findIndex(hash)
    if (index == 0) {
        return '1<sup>st</sup> pick';
    } else if (index == 1) {
        return '2<sup>nd</sup> pick'
    } else if (index == 2) {
        return '3<sup>rd</sup> pick'
    } else {
        return index + 1 + '<sup>th</sup> pick'
    }
}
</script>
