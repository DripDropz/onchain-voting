<template>
  <div class="flex">
    <div>
      <div class="flex flex-row items-center justify-between border border-l-0 border-r-0 border-t-0 border-b-1">
        <div>

       <ul class="flex flex-row items-center gap-8 mb-2 w-[100vh]">
        <li v-for="option in menuOptions" :key="option.name">
      <a @click="changeTab(option.value)" :class="getTabClass(option.value)">
        {{ option.name }} ({{ getCountForTab(option.value) }})
      </a>
    </li>
      </ul>
        </div>
        <div>
          <button class="font-semibold py-2 px-8 rounded-lg mb-2 border dark:border-white border-black text-black dark:text-white hover:bg-gray-200 transition duration-300 ease-in-out">Settings</button>
        </div>
      </div>
      <div v-for="petition in petitions" :key="petition.hash" class="border border-slate-900 dark:border-slate-700 dark:text-slate-100 my-8 rounded-lg flex flex-row">
        <div class="w-2/3">
          <div class="p-4">
            <h2 class="text-2xl mb-4 font-bold">
              <Link :href="route('admin.petitions.view', {petition: petition.hash})">
                {{ petition.title }}
              </Link>
            </h2>
            <span>{{ petition.description }}</span>
          </div>
          <div class="p-4 border border-t- border-l-0 border-r-0 border-b-0  border-black dark:border-slate-700 flex flex-row items-center justify-between">
            <h2 class="text-sm font-bold">{{ petition.hash}}</h2>
            <div class="flex flex-row items-center gap-8">
              <div class="flex flex-row items-center gap-2">
                <UsersIcon class="h-6 w-6" />
                <p>0</p>
              </div>
              <div class="flex flex-row items-center gap-2">
                <EnvelopeIcon class="h-6 w-6" />
                <span>{{ petition.created_at }}</span>
              </div>
            </div>
          </div>
        </div>
        <ul class="bg-slate-200 dark:bg-slate-700 w-1/3 flex flex-row items-center rounded-tr-lg rounded-br-lg justify-center">
          <li class="w-auto ocv-link">
            <img :src="voteAppLogo" alt="Logo" class="w-10 h-10 filter grayscale">
        </li>
        
          <li class="w-auto ocv-link">
            <h1 class="font-bold tracking-tight sm:text-xl xl:text-3xl font-display text-slate-900 dark:text-slate-200">
              ChainVote
            </h1>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, ref,computed } from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import { UsersIcon, EnvelopeIcon } from '@heroicons/vue/20/solid';
import voteAppLogo from '../../../../../images/openchainvote.png';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    petitions:  PetitionData[];
}>();
const currentTab = ref('drafts');

const emit = defineEmits<{
    (e: 'curr-paage', page: number): void
    (e: 'per-paage', perPage: number): void
}>();

const getCountForTab = (tabName) => {
  const count = filteredPetitions(tabName).length;
  return count > 0 ? count : '0';
};

const drafts = computed(() => props.petitions.filter((petition: PetitionData) => petition.status === 'draft'));
const active = computed(() => props.petitions.filter((petition: PetitionData) => petition.status === 'published'));

const filteredPetitions = (tabName) => {
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
    { name: 'Review', value: 'drafts' },
    { name: 'Active',  value: 'published'},
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
