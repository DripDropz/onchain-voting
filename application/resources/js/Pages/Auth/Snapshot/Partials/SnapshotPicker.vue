<template>
    <ModalRoute>
        <div class="p-1 bg-indigo-100 border-0 rounded-lg dark:bg-gray-700">
            <div v-if="!ballot?.snapshot" class="flex flex-col w-full gap-8 min-h-64">
                <div class="flex flex-col flex-shrink-0 w-full">
                    <div class="inline-flex w-full shadow-slate-300">
                        <div class="w-full">
                            <Multiselect placeholder="Search for snapshot" noOptionsText="Try typing more chars"
                                noResultsText="Try typing more chars" v-model="selectedRef" value-prop="hash" label="title"
                                mode="tags" @search-change="search" :closeOnSelect="true" :minChars="3"
                                :options="searchResults" :searchable="true" :classes="{
                                    container: 'multiselect border border-lg px-1 py-2 flex-wrap w-full dark:bg-gray-900 dark:border-gray-900 rounded-t-xl',
                                    containerOpen: 'rounded-t-xl',
                                    containerActive: 'shadow-none shadow-transparent box-shadow-none dark:bg-gray-900 rounded-t-xl',
                                    tagsSearch: 'w-full absolute top-0 left-0 inset-0 outline-none dark:bg-gray-900 dark:text-white focus:ring-0 appearance-none custom-input border-0 text-base font-sans bg-indigo-100 pl-1 rtl:pl-0 rtl:pr-1',
                                    tag: 'multiselect-tag bg-indigo-700 whitespace-normal ',
                                    tags: 'multiselect-tags px-2',
                                    dropdown: 'w-full dark:bg-gray-700',
                                    optionPointed: 'text-gray-800 bg-indigo-700 text-white',
                                    optionSelected: 'text-white bg-green-500',
                                    dropdownHidden: 'hidden',
                                }" />
                        </div>
                    </div>
                </div>
                <div class="flex justify-center w-full p-2 mt-auto">
                    <button v-if="selectedRef" @click.stop="addSnapshot()"
                        class="flex px-4 py-2 text-lg font-medium text-white bg-indigo-600 border border-transparent rounded-lg shadow-sm items-centerw-full xl:text-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="flex items-center justify-between ">
                            Add Snapshot
                        </span>
                    </button>
                </div>
            </div>
        </div>

    </ModalRoute>
</template>
<script setup lang="ts">
import Multiselect from '@vueform/multiselect';
import { ref } from 'vue';
import { useSnapshotStore } from "../store/snapshot-store"
import { storeToRefs } from 'pinia';
import { Ref } from 'vue';
import AlertService from '@/shared/Services/alert-service';
import BallotData = App.DataTransferObjects.BallotData;
import ModalRoute from '@/Components/ModalRoute.vue';
import { useModal } from "momentum-modal"
import AdminBallotService from '../../Ballot/Services/admin-ballot-service';

const props = defineProps<{
    ballot?: BallotData;
}>();

const { close } = useModal();

const snapshotStore = useSnapshotStore();
const { searchResults } = storeToRefs(snapshotStore)

let search = (search: string) => {
    snapshotStore.search(search);
};

let selectedRef: Ref<string | null> = ref(null);

let addSnapshot = async () => {
    if (!selectedRef.value?.[0] || !props.ballot?.hash) return;
    const data = {
        snapshot: selectedRef.value?.[0],
        ballot: props.ballot?.hash,
    }
    await AdminBallotService.linkSnapshot(data);
    AlertService.show(['Snapshot added successfully'], 'success');
    close();
}

</script>
../../Ballot/Services/admin-ballot-service
