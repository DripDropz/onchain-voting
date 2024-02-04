<template >
    <div>
        <div class="flex justify-start w-full pb-3">
            <p class="text-xl leading-tight xl:text-2xl ">Signature Goals</p>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="flex flex-col" v-for="(criterion, index) in criteriaRef">
                <span class="mb-1 text-sm text-slate-500"> {{ criterion.name }}</span>
                <label class="relative inline-flex items-center gap-3 cursor-pointer">
                    <input v-model="criterion.value2" @input="saveRule($event, criterion, index)"
                        class="border-0 rounded w-28 focus:ring-0 dark:bg-gray-900 bg-sky-100" :readonly="!!model.ballot">
                    <div >
                        <div v-if="criterion.loading" class="flex flex-row items-center">
                            <spinner class="relative z-30" color="yellow" size="7" />
                            <span>saving!</span>
                        </div>
                        <div v-if="criterion.updated && !criterion.loading" class="flex flex-row items-center">
                            <CheckCircleIcon class="w-5 h-5 text-green-500" />
                            <span> saved!</span>
                        </div>
                    </div>

                </label>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import PollData = App.DataTransferObjects.PollData;
import PetitionData = App.DataTransferObjects.PetitionData;
import axios from 'axios';
import { Spinner } from 'flowbite-vue';
import { CheckCircleIcon } from '@heroicons/vue/24/outline';


const props = withDefaults(defineProps<{
    model?: PetitionData | PollData;
}>(), {
});

let criteria = computed(() => [
    {
        name: 'Visible on site',
        'type': 'tally',
        'value1': 'visible',
        'loading': false,
        'updated': false,
        ...(props.model && props.model.rules ? props.model.rules.filter((item) => item.value1 == 'visible')[0] : {})
    },
    {
        name: 'Feature petition',
        'type': 'tally',
        'loading': false,
        'updated': false,
        'value1': 'feature-petition',
        ...(props.model && props.model.rules ? props.model.rules.filter((item) => item.value1 == 'feature-petition')[0] : {})
    },
    {
        name: 'Ballot eligible',
        'type': 'tally',
        'loading': false,
        'updated': false,
        'value1': 'ballot-eligible',
        ...(props.model && props.model.rules ? props.model.rules.filter((item) => item.value1 == 'ballot-eligible')[0] : {})
    }
]);

let criteriaRef = ref(criteria.value);

let saveRule = (value, criteria, index) => {
    criteriaRef.value[index].loading = true;
    criteriaRef.value[index].updated = true;

    let data = {
        v1: criteria.value1,
        v2: value.target.value,
        type: criteria.type,
        title: criteria.name,
    }

    setTimeout(() => {
        axios.post(route('admin.petitions.rules.saveRule', { petition: props.model.hash }), data)
            .then((res) => {
                criteriaRef.value[index].loading = res.data;
            })
            .finally(() => {
                criteriaRef.value[index].loading = false;
            })
    }, 1000)
}


</script>
