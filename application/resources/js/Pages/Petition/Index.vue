<template>
    <VoterLayout page="Petitions">
        <section class="py-12 mx-auto w-full">
            <div class="inner-container sm:px-6 lg:px-8">
                <div class="sm:rounded-lg">
                    <h2 class="mb-8 text-2xl font-bold leading-tight text-center text-gray-800 xl:text-4xl dark:text-gray-200">
                        Petitions
                    </h2>
                    <div class="w-full">
                        <div
                            class="flex flex-row items-center justify-between border border-t-0 border-l-0 border-r-0 border-b-1">
                            <div class="">
                                <!-- Tabs for public users -->
                                <ul class="flex flex-row items-center gap-8 mb-2" v-if="!user">
                                    <li v-for="option in menuOptions" :key="option.name">
                                        <a @click="changeTab(option.value)" :class="getTabClass(option.value)">
                                            {{ option.name }} ({{ getCountForTab(option.value) }})
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tabs for logged-in users -->
                                <ul class="flex flex-row items-center justify-between gap-8 mb-2" v-else>
                                    <li v-for="option in menuOptions" :key="option.name">
                                        <a @click="changeTab(option.value)" :class="getTabClass(option.value)">
                                            {{ option.name }} ({{ getCountForTab(option.value) }})
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <button
                                    class="px-8 py-2 mb-2 font-semibold text-white rounded-lg bg-sky-500 hover:bg-slate-600 hover:cursor-pointer">
                                    New
                                </button>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div v-if="currentTab === 'drafts'">
                                <div v-if="!user" class="flex flex-col items-center justify-center h-[500px] gap-16">
                                    <p class="text-2xl font-bold text-center dark:text-white">
                                        Login to view your draft petitions.
                                    </p>
                                    <Link :href="route('login.email')"
                                          class="inline-flex items-center px-8 py-2 font-semibold text-white border rounded-md shadow-sm gap-x-2 bg-sky-500 hover:bg-slate-600 hover:cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 border-sky-400">
                                        Login
                                    </Link>
                                </div>
                                <div v-else>
                                    <div v-if="filteredPetitions.length > 0">
                                        <PetitionList v-if="petitions" :petitions="drafts" :currentTab="currentTab"/>
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center h-[500px] gap-16">
                                        <p class="text-2xl font-bold text-center">No petitions</p>

                                        <Link :href="route('#')"
                                              class="inline-flex items-center px-8 py-2 font-semibold text-white border rounded-md shadow-sm gap-x-2 bg-sky-500 hover:bg-sky-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 border-sky-400">
                                            Login
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div v-else-if="currentTab === 'pending'">
                                <div v-if="filteredPetitions.length > 0">
                                    <PetitionList v-if="petitions" :petitions="pending" :currentTab="currentTab"/>
                                </div>
                                <div v-else class="flex flex-row items-center justify-center">
                                    <p class="text-2xl font-bold text-center">No pending petitions.</p>
                                </div>
                            </div>

                            <div v-else-if="currentTab === 'active'">
                                <div v-if="filteredPetitions.length > 0">
                                    <PetitionList v-if="petitions" :petitions="active" :currentTab="currentTab"/>
                                </div>
                                <div v-else class="flex flex-row items-center justify-center">
                                    <p class="text-2xl font-bold text-center">No active petitions.</p>
                                </div>
                            </div>
                            <div v-else-if="currentTab === 'signed'">
                                <div v-if="!user" class="flex flex-col items-center justify-center gap-16">
                                    <p class="text-2xl font-bold text-center dark:text-white">
                                        Login to view your signed petitions.
                                    </p>
                                    <Link :href="route('login.email')"
                                          class="inline-flex items-center px-8 py-2 font-semibold text-white border rounded-md shadow-sm gap-x-2 bg-sky-500 hover:bg-slate-600 hover:cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 border-sky-400">
                                        Login
                                    </Link>
                                </div>
                                <div v-else class="flex flex-row items-center justify-center">
                                    <PetitionList v-if="signedPetitions" :petitions="signed" :currentTab="currentTab"/>
                                    <p v-else class="text-2xl font-bold text-center dark:text-white">No signed petitions.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import {ref, computed} from 'vue';
import {Link} from "@inertiajs/vue3";
import PetitionData = App.DataTransferObjects.PetitionData;
import PetitionList from "@/Pages/Petition/Partials/PetitionList.vue"
import VoterLayout from "@/Layouts/VoterLayout.vue";
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const currentTab = ref('drafts');

const props = withDefaults(defineProps<{
    petitions: PetitionData[];
    signedPetitions: PetitionData[];
    currentTab?: string;
}>(), {});

const user = computed( () => page.props.auth.user );
const drafts = computed(() => props.petitions.filter((petition: PetitionData) => petition.status === 'draft'));
const pending = computed(() => props.petitions.filter((petition: PetitionData) => petition.status === 'pending'));
const active = computed(() => props.petitions.filter((petition: PetitionData) => petition.status === 'published'));
const signed = computed(() => props.signedPetitions); // Placeholder, update with your logic for signed petitions

const getCountForTab = (tabName: string) => {
    return filteredPetitions(tabName).length;
};

const filteredPetitions = (tabName: string) => {
    switch (tabName) {
        case 'drafts':
            return drafts.value;
        case 'pending':
            return pending.value;
        case 'active':
            return active.value;
        case 'signed':
            return signed.value;
        default:
            return [];
    }
};

const changeTab = (tabName: string) => {
    currentTab.value = tabName;
};
const menuOptions = [
    {name: 'Drafts', value: 'drafts'},
    {name: 'Pending', value: 'pending'},
    {name: 'Active', value: 'active'},
    {name: 'Signed', value: 'signed'},
]

const getTabClass = (tabName: string) => {
    return {
        'border-b-2 border-sky-300 dark:border-sky-500 font-medium text-sky-300 dark:text-sky-300 focus:outline-none focus:border-sky-700 text-xl hover:cursor-pointer':
            currentTab.value === tabName,
        'border-b-2 border-transparent font-medium text-sky-300 hover:text-sky-500 text-slate-900 dark:hover:text-sky-300 dark:text-slate-200 hover:border-sky-500 hover:cursor-pointer dark:hover:border-sky-300 focus:text-sky-500 dark:focus:text-sky-300 focus:border-sky-500 dark:focus:border-sky-300 text-xl':
            currentTab.value !== tabName,
    };
};
</script>
