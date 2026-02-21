<template>
    <div>
        <div class="flex justify-start w-full pb-3">
            <p class="text-xl leading-tight xl:text-2xl">Signature Goals</p>
        </div>
        <div class="flex flex-col gap-y-5">
            <div class="flex flex-col gap-1" v-for="(criterion, index) in criteriaRef" :key="criterion.value1">
                <span class="text-sm text-slate-500">{{ criterion.name }}</span>
                <div class="flex items-center gap-2">
                    <input
                        v-model="criterion.value2"
                        @input="criterion.dirty = true; criterion.saved = false"
                        class="border-0 rounded w-28 focus:ring-0 dark:bg-gray-900 bg-sky-100"
                        :readonly="!!model.ballot"
                        type="number"
                        min="0"
                    />
                    <button
                        v-if="!model.ballot"
                        @click="saveRule(criterion, index)"
                        :disabled="criterion.loading || !criterion.dirty"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                        :class="criterion.dirty && !criterion.loading ? 'bg-sky-500 hover:bg-sky-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'"
                    >
                        <span v-if="criterion.loading" class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            Saving…
                        </span>
                        <span v-else>Save</span>
                    </button>
                    <span v-if="criterion.saved && !criterion.loading" class="flex items-center gap-1 text-xs text-green-600 dark:text-green-400">
                        <CheckCircleIcon class="w-4 h-4" />
                        Saved
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import PollData = App.DataTransferObjects.PollData;
import PetitionData = App.DataTransferObjects.PetitionData;
import axios from 'axios';
import { CheckCircleIcon } from '@heroicons/vue/24/outline';

const props = withDefaults(defineProps<{
    model?: PetitionData | PollData;
}>(), {});

const emit = defineEmits(['update']);

const makeCriterion = (name: string, value1: string) => {
    const existing = props.model?.rules?.find((item) => item.value1 === value1);
    return {
        name,
        type: 'tally',
        value1,
        value2: existing?.value2 ?? '',
        loading: false,
        dirty: false,
        saved: false,
    };
};

const criteriaRef = ref([
    makeCriterion('Visible on site',  'visible'),
    makeCriterion('Feature petition', 'feature-petition'),
    makeCriterion('Ballot eligible',  'ballot-eligible'),
]);

const saveRule = async (criterion: any, index: number) => {
    criteriaRef.value[index].loading = true;

    const data = {
        v1:    criterion.value1,
        v2:    criterion.value2,
        type:  criterion.type,
        title: criterion.name,
    };

    try {
        await axios.post(route('admin.petitions.rules.saveRule', { petition: props.model.hash }), data);
        criteriaRef.value[index].saved = true;
        criteriaRef.value[index].dirty = false;
        emit('update');
    } finally {
        criteriaRef.value[index].loading = false;
    }
};
</script>
