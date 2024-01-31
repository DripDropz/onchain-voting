<template>
    <VoterLayout page="Polls">
        <template #header>
            <Nav :crumbs="props.crumbs" />
        </template>
        <section class="flex flex-col w-full py-12 m-auto">
            <div class="w-full inner-container">
                <div class="sm:rounded-lg">
                    <h2
                        class="mb-8 text-2xl font-bold leading-tight text-center text-gray-800 xl:text-4xl dark:text-gray-200">
                        Polls
                    </h2>
                    <div class="w-full">
                        <div
                            class="flex flex-row items-center justify-between border border-t-0 border-l-0 border-r-0 border-b-1">
                            <div class="">
                                <!-- Tabs for public users -->
                                <ul class="flex flex-row items-center gap-8 mb-2" v-if="!user">
                                    <li v-for="option in menuOptions" :key="option.name">
                                        <a @click="changeTab(option.value)" :class="getTabClass(option.value)">
                                            {{ option.value === 'browse' ? option.name : `${option.name} (3)` }}
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tabs for logged-in users -->
                                <ul class="flex flex-row items-center justify-between gap-8 mb-2" v-else>
                                    <li v-for="option in menuOptions" :key="option.name">
                                        <a @click="changeTab(option.value)" :class="getTabClass(option.value)">
                                            {{ option.value === 'browse' ? option.name : option.name + ' (3)' }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <button
                                    class="px-8 py-2 mb-2 font-semibold text-white rounded-lg bg-sky-500 hover:bg-slate-600 hover:cursor-pointer">Create
                                    poll</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div >
                <BrowsePolls :polls="polls" v-if="currentTab=='browse'"/>
            </div>

        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import VoterLayout from "@/Layouts/VoterLayout.vue";
import UserData = App.DataTransferObjects.UserData;
import PollData = App.DataTransferObjects.PollData;
import Nav from '../NavCrumbs.vue';
import PollList from './Partials/PollList.vue';
import BrowsePolls from './Partials/BrowsePolls.vue';

const props = withDefaults(defineProps<{
    user: UserData;
    crumbs: []
}>(), {
});

const selectedOption = '';

const polls = [
    {
        id: 1,
        name: 'Which is the best?',
        votes: 3042,
        options: [
            { name: 'Hosky' },
            { name: 'Bitcoin' },
            { name: 'Cardano' },
            { name: 'Ethereum' }
        ]
    },
    {
        id: 2,
        name: 'Were you a selected SPO for Midnight?',
        votes: 5672,
        options: [
            { name: 'Yes' },
            { name: 'No' }
        ]
    },
    {
        id: 3,
        name: 'When did you get the first payment',
        votes: 1123,
        options: [
            { name: '1st quarter' },
            { name: '2nd quarter' },
            { name: '3rd quarter' }
        ]
    }
]
const currentTab = ref('browse');

const changeTab = (tabName) => {
    currentTab.value = tabName;
};
const menuOptions = [
    { name: 'Browse', value: 'browse' },
    { name: 'Drafts', value: 'drafts' },
    { name: 'Active', value: 'active' },
    { name: 'Answered', value: 'answered' },
]
const getTabClass = (tabName) => {
    return {
        'border-b-2 border-sky-300 dark:border-sky-500 font-medium text-sky-300 dark:text-sky-300 focus:outline-none focus:border-sky-700 text-xl hover:cursor-pointer':
            currentTab.value === tabName,
        'border-b-2 border-transparent font-medium text-sky-300 hover:text-sky-500 text-slate-900 dark:hover:text-sky-300 dark:text-slate-200 hover:border-sky-500 hover:cursor-pointer dark:hover:border-sky-300 focus:text-sky-500 dark:focus:text-sky-300 focus:border-sky-500 dark:focus:border-sky-300 text-xl':
            currentTab.value !== tabName,
    };
};
</script>
