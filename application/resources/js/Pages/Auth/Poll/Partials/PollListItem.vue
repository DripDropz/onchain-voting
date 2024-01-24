<template>
    <div class="border border-slate-900 dark:border-slate-700 dark:text-slate-100 my-8 rounded-lg flex flex-row">
        <div class="w-2/3">
            <div class="p-4">
                <h2 class="text-2xl mb-4 font-bold">{{ poll.title }}</h2>
                <span>{{ poll.description }}</span>
            </div>
            <div
                class="p-4 border border-t- border-l-0 border-r-0 border-b-0  border-black dark:border-slate-700 flex flex-row items-center justify-between">
                <h2 class="text-sm font-bold">{{ poll.hash }}</h2>
                <div class="flex flex-row items-center gap-8">
                    <div class="flex flex-row items-center gap-2">
                        <UsersIcon class="h-6 w-6"/>
                        <p>0</p>
                    </div>
                    <div class="flex flex-row items-center gap-2">
                        <EnvelopeIcon class="h-6 w-6"/>
                        <span>{{ poll.created_at }}</span>
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
</template>

<script setup lang="ts">
import {defineProps, ref, computed} from 'vue';
import PollData = App.DataTransferObjects.PollData;
import {UsersIcon, EnvelopeIcon} from '@heroicons/vue/20/solid';
import voteAppLogo from '../../../../../images/openchainvote.png';

const props = defineProps<{
    poll: PollData;
}>();

</script>
