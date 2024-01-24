<template>
    <div>
        <div
            class="flex flex-row items-center justify-between border border-l-0 border-r-0 border-t-0 border-b-1 w-full">
            <div>

            </div>
            <div class="ml-auto">
                <button
                    class="font-semibold py-2 px-8 dark:text-white rounded-lg mb-2 border border-black dark:border-white text-black hover:bg-gray-200 transition duration-300 ease-in-out">
                    Settings
                </button>
            </div>
        </div>

        <div>
            <PollListItem v-for="poll in polls" :key="poll.hash" :poll="poll" />
        </div>
    </div>
</template>

<script setup lang="ts">
import {defineProps, ref, computed} from 'vue';
import PollData = App.DataTransferObjects.PollData;
import PollListItem from "@/Pages/Auth/Poll/Partials/PollListItem.vue";

const props = defineProps<{
    polls: PollData[];
}>();
const currentTab = ref('drafts');

const emit = defineEmits<{
    (e: 'curr-paage', page: number): void
    (e: 'per-paage', perPage: number): void
}>();

const getCountForTab = (tabName) => {
    const count = filteredPolls(tabName).length;
    return count > 0 ? count : '0';
};

const drafts = computed(() => props.polls.filter((poll: PollData) => poll.status === 'draft'));
const active = computed(() => props.polls.filter((poll: PollData) => poll.status === 'published'));

const filteredPolls = (tabName) => {
    switch (tabName) {
        case 'drafts':
            return drafts.value;
        case 'active':
            return active.value;
        default:
            return [];
    }
};

const changeTab = (tabName) => {
    currentTab.value = tabName;
};
const menuOptions = [
    {name: 'Review', value: 'drafts'},
    {name: 'Active', value: 'published'},
]

const getTabClass = (tabName) => {
    return {
        'border-b-2 border-sky-300 dark:border-sky-500 font-medium text-sky-300 dark:text-sky-300 focus:outline-none focus:border-sky-700 text-xl':
            currentTab.value === tabName,
        'border-b-2 border-transparent font-medium text-sky-300 hover:text-sky-500 text-slate-900 dark:hover:text-sky-300 dark:text-slate-200 hover:border-sky-500 dark:hover:border-sky-300 focus:text-sky-500 dark:focus:text-sky-300 focus:border-sky-500 dark:focus:border-sky-300 text-xl':
            currentTab.value !== tabName,
    };
};
</script>
