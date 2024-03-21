<template>
    <VoterLayout page="Polls" :crumbs="crumbs" :actions="actions">
        <section class="w-full py-12 container">
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
                                            {{
                                                option.value === "browse"
                                                ? option.name
                                                : `${option.name}`
                                            }}
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tabs for logged-in users -->
                                <ul class="flex flex-row items-center justify-between gap-8 mb-2" v-else>
                                    <li v-for="option in menuOptions" :key="option.name">
                                        <a @click="changeTab(option.value)" :class="getTabClass(option.value)">
                                            {{`${option.name} (${option.count ?? 0})` }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-full inner-container">
                <BrowsePolls v-if="currentTab == 'browse'" :context="'browse'" :params="{'status': 'published'}" />

                <template v-else-if="!!user" v-for="option in menuOptions" :key="option.name">
                    <BrowsePolls v-if="currentTab == option.value && option.value!='browse'" :context="option.value" :params="option.param" />
                </template>
                <div v-else class="py-16">
                    <LoginToView>
                        <span class="dark:text-white"> Login to view your {{ currentTab }} polls.</span>
                    </LoginToView>
                </div>
            </div>
            <div v-if="user && currentTab === 'answered' && (!menuOptions.find(option => option.value === 'answered') || menuOptions.find(option => option.value === 'answered').count === 0)" class="py-16">
                <div class="h-full inner-container justify-center text-center border-2 border-dashed border-gray-300">
                    <div class="py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">
                          You haven't answered any polls.
                        </h3>
                        <div class="mt-6 pb-3">
                            <PrimaryButton :theme="'primary'" @click="changeTab('browse')">
                                Browse Polls
                                <PlusIcon class="w-5 h-5" />
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else-if="user && ['pending', 'active'].includes(currentTab) && (!menuOptions.find(option => option.value === currentTab) || menuOptions.find(option => option.value === currentTab).count === 0)"
                class="py-16">
                <div
                    class="h-full inner-container justify-center text-center rounded-lg border-2 border-dashed border-gray-300">
                    <div class="py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200 capitalize">No {{currentTab}} Polls</h3>
                        <div class="mt-6 pb-3">
                            <Link :href="route('polls.create')">
                                <PrimaryButton :theme="'primary'">
                                    Create Poll
                                    <PlusIcon class="w-5 h-5" />
                                </PrimaryButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import VoterLayout from "@/Layouts/VoterLayout.vue";
import UserData = App.DataTransferObjects.UserData;
import PollData = App.DataTransferObjects.PollData;
import BrowsePolls from "./Partials/BrowsePolls.vue";
import LoginToView from '@/shared/components/LoginToView.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {PlusIcon} from "@heroicons/vue/20/solid";

const props = withDefaults(
    defineProps<{
        polls?: PollData[];
        user: UserData;
        crumbs: [];
        actions: []
        counts: any;
    }>(),
    {}
);

const currentTab = ref("browse");

const changeTab = (tabName) => {
    currentTab.value = tabName;
};
const menuOptions = [
    {
        name: "Browse",
        value: "browse",
        count: props.counts.allCount,
        param:{}
    },
    {
        name: "Active",
        value: "active",
        count: props.counts.activeCount,
        param: { hasActive: true }
    },
    {
        name: "Pending",
        value: "pending",
        count: props.counts.pendingCount,
        param: { hasPending: true }
    },
    {
        name: "Answered",
        value: "answered",
        count: props.counts.answeredCount,
        param: { hasAnswered: true }
    },
];
const getTabClass = (tabName) => {
    return {
        "border-b-2 border-sky-300 dark:border-sky-500 font-medium text-sky-300 dark:text-sky-300 focus:outline-none focus:border-sky-700 text-xl hover:cursor-pointer":
            currentTab.value === tabName,
        "border-b-2 border-transparent font-medium text-sky-300 hover:text-sky-500 text-slate-900 dark:hover:text-sky-300 dark:text-slate-200 hover:border-sky-500 hover:cursor-pointer dark:hover:border-sky-300 focus:text-sky-500 dark:focus:text-sky-300 focus:border-sky-500 dark:focus:border-sky-300 text-xl":
            currentTab.value !== tabName,
    };
};

</script>
