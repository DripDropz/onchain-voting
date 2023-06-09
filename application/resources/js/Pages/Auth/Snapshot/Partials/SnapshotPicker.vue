<template>
<div class="w-96">
  <Multiselect
            placeholder="Search for snapshot"
            noOptionsText="Try typing more chars"
            noResultsText="Try typing more chars"
            v-model="selectedRef"
            value-prop="hash"
            label="title"
            mode="tags"
            @search-change="search"
            :closeOnSelect="true"
            :minChars="3"
            :options="searchResults"
            :searchable="true"
            :classes="{
                container: 'multiselect border border-lg px-1 py-2 flex-wrap w-full dark:bg-gray-900 dark:border-gray-900',
                containerActive: 'shadow-none shadow-transparent box-shadow-none dark:bg-gray-900',
                tagsSearch: 'w-full absolute top-0 left-0 inset-0 outline-none dark:bg-gray-900 dark:text-white focus:ring-0 appearance-none custom-input border-0 text-base font-sans bg-white pl-1 rtl:pl-0 rtl:pr-1',
                tag: 'multiselect-tag bg-indigo-700 whitespace-normal',
                tags: 'multiselect-tags px-2',
                dropdown: 'w-full dark:bg-gray-700 ',
                optionPointed: 'text-gray-800 bg-indigo-700 text-white',
                optionSelected: 'text-white bg-green-500',
                dropdownHidden: 'hidden',


            }"
        />
</div>

</template>
<script setup lang="ts">
import Multiselect from '@vueform/multiselect';
import { ref } from 'vue';
import {useSnapshotStore} from "../store/snapshot-store"
import { storeToRefs } from 'pinia';
import { Ref, watch} from 'vue';

const snapshotStore = useSnapshotStore();
const {searchResults} = storeToRefs(snapshotStore)

let selectedRef:Ref<string|null> = ref(null);

const emit = defineEmits<{
    (e: 'snapshot', modelValue:string | null): void
}>();

watch(selectedRef, () => emit('snapshot', selectedRef.value), {deep:true});

let search = (search: string) => {
    snapshotStore.search(search);
};

</script>
