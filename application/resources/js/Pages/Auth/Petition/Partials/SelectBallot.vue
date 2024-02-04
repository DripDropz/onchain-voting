<template >
    <div v-if="!findQuestion">
        <Multiselect placeholder="Search for ballots" noOptionsText="Try typing more chars"
            noResultsText="Try typing more chars" v-model="ballotHash" value-prop="hash" label="title" :closeOnSelect="true"
            :minChars="3" :max="1" :options="ballots" :searchable="true" :classes="{
                container: 'multiselect   px-1 py-2 flex-wrap w-full dark:bg-gray-800 bg-white dark:border-gray-800 rounded-t-xl',
                containerOpen: 'rounded-t-xl',
                containerActive: 'shadow-none shadow-transparent box-shadow-none dark:bg-gray-900 rounded-t-xl',
                tagsSearch: 'w-full absolute top-0 left-0 inset-0 outline-none dark:bg-gray-900 dark:text-white focus:ring-0 appearance-none custom-input border-0 text-base font-sans bg-primary-50/20 pl-1 rtl:pl-0 rtl:pr-1',
                tag: 'multiselect-tag bg-white whitespace-normal ',
                tags: 'multiselect-tags px-2',
                dropdown: 'w-full dark:bg-gray-700 max-h-80 overflow-scroll',
                optionPointed: 'text-gray-800 bg-sky-100  dark:bg-gray-900 ',
                optionSelected: ' bg-sky-100 dark:bg-gray-900',
                dropdownHidden: 'hidden',
                search: 'dark:bg-gray-900 multiselect-search bg-sky-100 focus:ring-slate-100 '
            }" />
        <div class="flex justify-between px-4 mt-8 mb-2">
            <PrimaryButton @click="emit('create-ballot')">
                Create ballot instead
            </PrimaryButton>
            <PrimaryButton :theme="'primary'" v-if="ballotHash"
                @click='(ballot?.questions?.length ? submit() : findQuestion = true)'>
                Use Ballot
            </PrimaryButton>
        </div>
    </div>
    <div v-else>
        <div v-if="!ballot?.questions?.length" class="px-2 pt-2">
            <p class="mb-2 font-bold"> Question Details</p>
            <label for="title" class="sr-only">Title</label>
            <TextInput type="text" name="title" id="title" v-model="questionTitle"
                class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-sky-100 dark:bg-gray-900"
                placeholder="Title" />
        </div>

        <div class="flex justify-between px-4 mt-8 mb-2">
            <PrimaryButton @click="findQuestion = false">
                <ArrowLeftIcon class="w-4 h-4" />
                Back
            </PrimaryButton>
            <PrimaryButton :theme="'primary'" @click="submit">
                submit
            </PrimaryButton>
        </div>
    </div>
</template>

<script lang="ts" setup>
import Multiselect from '@vueform/multiselect';
import { computed, onMounted, ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BallotData = App.DataTransferObjects.BallotData;
import { useBallotStore } from '@/stores/ballot-store';
import { storeToRefs } from 'pinia';
import TextInput from '@/Components/TextInput.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';


let ballotHash = ref(null)
let questionTitle = ref(null);
let findQuestion = ref(false);
const ballotstore = useBallotStore()
let { ballots } = storeToRefs(ballotstore);
let ballot = computed(() => ballots.value.find((ballot) => ballot.hash == ballotHash.value))



const emit = defineEmits<{
    (event: 'create-ballot'): void
    (event: 'submit', ballotDetails: {}): void
}>()

let submit = () => {
    emit('submit', {
       ballotHash: ballotHash.value,
        questionTitle:questionTitle.value
    })
}

onMounted(() => {
    if (!ballots.value.length) {
        ballotstore.loadAllBallots();
    }
})
</script>