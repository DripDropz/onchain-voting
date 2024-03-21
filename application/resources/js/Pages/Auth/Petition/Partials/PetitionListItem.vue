<template>
    <div class="flex flex-row my-8 border rounded-lg border-slate-900 dark:border-slate-700 dark:text-slate-100">
        <div class="w-2/3">
            <div class="p-4">
                <h2 class="mb-4 text-2xl font-bold align-middle">
                    <Link
                        v-if="!petition?.ballot"
                        :href="route('admin.petitions.edit', { petition: petition.hash })"
                        class="font-semibold text-white hover:text-sky-500">
                        {{ petition.title }} <span class="opacity-30 text-sm">- {{ petition.status }}</span>
                    </Link>
                </h2>
                <span v-html="parseMarkdown(petition.description)"></span>
            </div>
            <div
                class="flex flex-row items-center justify-between p-4 border border-b-0 border-l-0 border-r-0 border-black border-t- dark:border-slate-700">
                <div class="flex flex-row items-center justify-between gap-8 w-full">
                    <h2 class="text-sm font-bold">{{ petition.hash }}</h2>

                    <div class="flex flex-row items-center gap-2">
                        <Link
                            :href="route('petitions.view', { petition: petition.hash })"
                            class="font-semibold text-sky-500 hover:text-slate-700 dark:hover:text-white">
                            <span>View</span>
                        </Link>

                        <Link
                            v-if="!petition?.ballot"
                            :href="route('admin.petitions.edit', { petition: petition.hash })"
                            class="font-semibold text-sky-500 hover:text-slate-700 dark:hover:text-white">
                            <span>Edit</span>
                        </Link>

                        <Link :href="route('admin.ballots.view', { ballot: petition?.ballot?.hash })"
                              v-if="petition.ballot">
                            <button :theme="'primary'"
                                    class="flex flex-row items-center gap-2 px-4 py-1 font-semibold border-2 rounded-lg text-sky-500 border-sky-500">
                                view ballot
                                <ArrowTopRightOnSquareIcon class="w-5 h-5"/>
                            </button>
                        </Link>
                    </div>

                    <div class="flex flex-row items-center gap-8">
                        <div class="flex flex-row items-center gap-2">
                            <UsersIcon class="w-6 h-6"/>
                            <p>{{ petition.signatures_count }}</p>
                        </div>
                        <div class="flex flex-row items-center gap-2">
                            <EnvelopeIcon class="w-6 h-6"/>
                            <span>
                                {{
                                    formatDate(petition.status === 'published' ? petition.started_at : petition.created_at)
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul
            class="flex flex-row items-center justify-center w-1/3 rounded-tr-lg rounded-br-lg bg-slate-200 dark:bg-slate-700">
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
import {defineProps} from 'vue';
import PetitionData = App.DataTransferObjects.PetitionData;
import {UsersIcon, EnvelopeIcon} from '@heroicons/vue/20/solid';
import voteAppLogo from '../../../../../images/openchainvote.png';
import {Link} from "@inertiajs/vue3";
import {ArrowTopRightOnSquareIcon} from '@heroicons/vue/20/solid';
import MarkdownIt from 'markdown-it';


defineProps<{
    petition: PetitionData;
}>();

const formatDate = (dateString: string): string => {
    const options = {month: '2-digit', day: '2-digit', year: '2-digit'};
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const md = new MarkdownIt();

const parseMarkdown = (content: string): string => {
    return md.render(content);
};

</script>
