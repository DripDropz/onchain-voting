<template>
    <VoterLayout
        page="Petitions"
        :crumbs="crumbs"
        :actions="[{
                    label: 'Create Petition',
                    clickAction:'showModal'
                }]">
        <section class="w-full py-12 mx-auto">
            <div class="inner-container">
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
                                            {{
                                                `${option.name} (${option.count ?? 0})`
                                            }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-full inner-container">
                <PetitionBrowser v-if="currentTab == 'browse'" :context="'browse'" :params="{}" />

                <template v-else-if="!!user" v-for="option in menuOptions" :key="option.name">
                    <PetitionBrowser v-if="currentTab == option.value && option.value!='browse'" :context="option.value" :params="option.param" />
                </template>
                <div v-else class="py-16">
                    <LoginToView>
                        <span class="dark:text-white"> Login to view your {{ currentTab }} petitions.</span>
                    </LoginToView>
                </div>
            </div>
            <Modal :show="showModal">
                <PetitionConfirmation @close="showModal = false"/>
            </Modal>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import {ref} from 'vue';
import {Link} from "@inertiajs/vue3";
import UserData = App.DataTransferObjects.UserData;
import PetitionData = App.DataTransferObjects.PetitionData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import Modal from '@/Components/Modal.vue';
import PetitionConfirmation from './Partials/PetitionConfirmation.vue';
import LoginToView from "@/shared/components/LoginToView.vue";
import {useConfigStore} from '@/stores/config-store';
import {storeToRefs} from 'pinia';
import PetitionBrowser from './Partials/PetitionBrowser.vue';

let configStore = useConfigStore();
let {showModal} = storeToRefs(configStore);

const props = withDefaults(
    defineProps<{
        petitions?: PetitionData[];
        user: UserData;
        crumbs: [];
        actions: []
        counts: any;
    }>(),
    {}
);

const currentTab = ref('browse');

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
        name: "Drafts", 
        value: "draft", 
        count: props.counts.draftCount,
        param: { statusfilter: ['draft'] }
    },
    {
        name: "Active", 
        value: "active", 
        count: props.counts.activeCount,
        param: { statusfilter: ['published'] }
    },
    {
        name: "Pending", 
        value: "pending", 
        count: props.counts.pendingCount,
        param: { hasPending: true }
    },
    {
        name: "signed", 
        value: "signed", 
        count: props.counts.signedCount,
        param: { hasSigned: true }
    },
];

const getTabClass = (tabName) => {
    return {
        'border-b-2 border-sky-300 dark:border-sky-500 font-medium text-sky-300 dark:text-sky-300 focus:outline-none focus:border-sky-700 text-xl hover:cursor-pointer':
            currentTab.value === tabName,
        'border-b-2 border-transparent font-medium text-sky-300 hover:text-sky-500 text-slate-900 dark:hover:text-sky-300 dark:text-slate-200 hover:border-sky-500 hover:cursor-pointer dark:hover:border-sky-300 focus:text-sky-500 dark:focus:text-sky-300 focus:border-sky-500 dark:focus:border-sky-300 text-xl':
            currentTab.value !== tabName,
    };
};

</script>
