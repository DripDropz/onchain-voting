<template>
    <VoterLayout page="Polls">
        <section class="py-12 m-auto w-full">
            <div class="inner-container sm:px-6 lg:px-8 w-full">
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
<!--                                <ul class="flex flex-row items-center justify-between gap-8 mb-2" v-else>-->
<!--                                    <li v-for="option in menuOptions" :key="option.name">-->
<!--                                        <a @click="changeTab(option.value)" :class="getTabClass(option.value)">-->
<!--                                            {{ option.value === 'browse' ? option.name : option.name + ' (3)' }}-->
<!--                                        </a>-->
<!--                                    </li>-->
<!--                                </ul>-->
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
            <div class="grid grid-cols-2 inner-container">
                <div v-for="(poll) in polls" :key="poll.id"
                    class="dark:text-gray-200 rounded-lg px-10 py-6 m-4 border border-gray-800 dark:border-gray-200 relative pb-16">
                    <h2 class="text-2xl mb-4 font-extrabold">{{ poll.name }}</h2>
                    <div v-for="(item, index) in poll.options" :key="index" class="mb-3 flex">
                        <label class="w-full cursor-pointer">
                            <input type="radio" class="sr-only peer" v-model="selectedOption" />
                            <span
                                class="w-full block p-3 dark:text-gray-200 border-2 rounded-lg hover:shadow peer-checked:border-sky-500">
                                <span class="flex items-center justify-between">
                                    <span class="pr-8 font-bold">{{ index + 1 + '. ' + item.name }}</span>
                                </span>
                            </span>
                        </label>
                    </div>
                    <div class="absolute py-2 items-center bottom-0">
                        <span>{{ poll.votes }} votes</span>
                        <button @click.prevent=""
                            class="px-4 py-2 mb-2 font-semibold text-white rounded-lg bg-sky-500 hover:bg-slate-600 hover:cursor-pointer ml-72">Vote</button>
                    </div>
                </div>
            </div>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import VoterLayout from "@/Layouts/VoterLayout.vue";
import UserData = App.DataTransferObjects.UserData;

const props = withDefaults(defineProps<{
    user: UserData;
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
