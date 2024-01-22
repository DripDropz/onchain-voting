<template>
    <div class="border border-slate-900 dark:border-slate-700 dark:text-slate-100 my-8 rounded-lg flex flex-row">
        <div class="w-[75%]">
            <div class="p-4">
                <h2 class="text-2xl mb-4 font-bold">
                    <Link :href="route('petitions.view', {petition: petition.hash})">
                        {{ petition.title }}
                    </Link>
                </h2>
                <p>{{ petition.description }}</p>
            </div>
            <div
                class="p-4 border border-t- border-l-0 border-r-0 border-b-0  border-black dark:border-slate-700 flex flex-row items-center justify-between">
                <h2 class="text-xl font-bold">{{ petition.hash }}</h2>
                <div class="flex flex-row items-center gap-8">
                    <div class="flex flex-row items-center gap-2">
                        <UsersIcon class="h-6 w-6"/>
                        <p>{{petition.signatures_count ?? '-'}}</p>
                    </div>
                    <div class="flex flex-row items-center gap-2">
                        <EnvelopeIcon class="h-6 w-6"/>
                        <p>{{ formatDate(petition.created_at) }}</p>
                    </div>
                </div>
            </div>
        </div>
        <ul class="bg-slate-100 dark:bg-slate-700 w-[25%] flex flex-row items-center rounded-tr-lg rounded-br-lg justify-center">
            <li class="w-auto ocv-link">
                <img :src="voteAppLogo" alt="Open Chainvote App Logo" class="w-10 h-10">
            </li>
            <li class="w-auto ocv-link">
                <h1 class="font-bold tracking-tight sm:text-xl xl:text-3xl font-display text-slate-900 dark:text-slate-200">
                    ChainVote
                </h1>
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import {defineProps} from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import {UsersIcon, EnvelopeIcon} from '@heroicons/vue/20/solid';
import voteAppLogo from '../../../../images/openchainvote.png';
import {Link} from "@inertiajs/vue3";

defineProps<{
    petition: PetitionData;
}>();

const formatDate = (dateString: string): string => {
    const options = {month: '2-digit', day: '2-digit', year: '2-digit'};
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>
