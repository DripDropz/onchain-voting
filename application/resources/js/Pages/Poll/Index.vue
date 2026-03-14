<template>
    <VoterLayout page="Polls" :crumbs="crumbs" :actions="[]">
        <section class="w-full py-12 mx-auto container pb-24 md:pb-12">
            <div class="inner-container">
                <h2 class="mb-6 text-3xl font-bold leading-tight text-center text-gray-800 xl:text-4xl dark:text-gray-200">
                    Polls
                </h2>
                <div class="flex justify-center mb-8">
                    <div class="w-full max-w-4xl p-5 border shadow-sm rounded-2xl bg-gradient-to-r from-sky-50 via-cyan-50 to-sky-100 border-sky-200 dark:from-sky-900/35 dark:via-cyan-900/25 dark:to-sky-900/35 dark:border-sky-700/60">
                        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                            <div class="text-center sm:text-left">
                                <p class="text-sm font-semibold tracking-wide uppercase text-sky-700 dark:text-sky-300">
                                    Ready to get community sentiment?
                                </p>
                                <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">
                                    Start a poll and gather votes from your community.
                                </p>
                            </div>
                            <Link
                                v-if="user"
                                :href="route('polls.create')"
                                class="inline-flex items-center gap-2.5 px-6 py-3.5 text-sm font-semibold tracking-wide text-white uppercase transition-colors rounded-xl shadow-md bg-sky-600 hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-sky-50 dark:focus:ring-offset-slate-900"
                            >
                                <DocumentPlusIcon class="w-5 h-5" />
                                Create Poll
                            </Link>
                            <button
                                v-else
                                type="button"
                                @click="showModal = true"
                                class="inline-flex items-center gap-2.5 px-6 py-3.5 text-sm font-semibold tracking-wide text-white uppercase transition-colors rounded-xl shadow-md bg-sky-600 hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-sky-50 dark:focus:ring-offset-slate-900"
                            >
                                <DocumentPlusIcon class="w-5 h-5" />
                                Create Poll
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Platform stats bar -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
                    <div
                        v-for="stat in platformStatCards"
                        :key="stat.label"
                        class="flex flex-col items-center gap-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-4 shadow-sm"
                    >
                        <component :is="stat.icon" class="w-5 h-5 text-sky-500" />
                        <span class="text-2xl font-bold text-gray-800 dark:text-gray-100 tabular-nums">
                            {{ stat.value.toLocaleString() }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 text-center leading-tight">
                            {{ stat.label }}
                        </span>
                    </div>
                </div>

                <!-- Tab bar -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap gap-1 -mb-px">
                        <li v-for="option in visibleTabs" :key="option.value">
                            <button
                                @click="changeTab(option.value)"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-t-lg transition-colors focus:outline-none"
                                :class="getTabClass(option.value)"
                            >
                                {{ option.name }}
                                <span
                                    v-if="user && option.count !== undefined"
                                    class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-semibold"
                                    :class="currentTab === option.value
                                        ? 'bg-sky-500 text-white'
                                        : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                                >
                                    {{ option.count ?? 0 }}
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab content -->
            <div class="h-full inner-container">
                <BrowsePolls v-if="currentTab === 'browse'" :context="'browse'" :params="{'status': 'published'}" />

                <template v-else-if="!!user">
                    <template v-for="option in menuOptions" :key="option.value">
                        <BrowsePolls
                            v-if="currentTab === option.value && option.value !== 'browse'"
                            :context="option.value"
                            :params="option.param"
                        />
                    </template>
                </template>

                <div v-else-if="currentTab !== 'answered'" class="py-16">
                    <LoginToView>
                        <span class="dark:text-white">Login to view your {{ currentTab }} polls.</span>
                    </LoginToView>
                </div>
            </div>

            <template v-if="user">
                <div
                    v-if="currentTab === 'answered' && answeredCount === 0"
                    class="inner-container py-16"
                >
                    <EmptyState
                        title="No answered polls yet"
                        description="Browse active polls and cast your vote on topics you care about."
                        action-label="Browse Polls"
                        @action="changeTab('browse')"
                    >
                        <template #icon>
                            <CheckCircleIcon class="w-12 h-12 text-gray-300 dark:text-gray-600" />
                        </template>
                    </EmptyState>
                </div>

                <div
                    v-else-if="['pending', 'draft', 'active'].includes(currentTab) && activeTabCount === 0"
                    class="inner-container py-16"
                >
                    <EmptyState
                        :title="`No ${currentTab} polls`"
                        :description="emptyStateDescription"
                        action-label="Create a Poll"
                        :action-href="route('polls.create')"
                    >
                        <template #icon>
                            <DocumentPlusIcon
                                v-if="currentTab === 'draft'"
                                class="w-12 h-12 text-gray-300 dark:text-gray-600"
                            />
                            <ClockIcon
                                v-else-if="currentTab === 'pending'"
                                class="w-12 h-12 text-gray-300 dark:text-gray-600"
                            />
                            <CheckCircleIcon
                                v-else
                                class="w-12 h-12 text-gray-300 dark:text-gray-600"
                            />
                        </template>
                    </EmptyState>
                </div>
            </template>

            <Modal :show="showModal">
                <PollConfirmation @close="showModal = false" />
            </Modal>
        </section>
        <div class="fixed z-40 md:hidden bottom-6 right-4">
            <Link
                v-if="user"
                :href="route('polls.create')"
                class="inline-flex items-center gap-2 px-4 py-3 text-xs font-semibold tracking-wide text-white uppercase transition-colors rounded-full shadow-lg bg-sky-600 hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
            >
                <DocumentPlusIcon class="w-4 h-4" />
                Create Poll
            </Link>
            <button
                v-else
                type="button"
                @click="showModal = true"
                class="inline-flex items-center gap-2 px-4 py-3 text-xs font-semibold tracking-wide text-white uppercase transition-colors rounded-full shadow-lg bg-sky-600 hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
            >
                <DocumentPlusIcon class="w-4 h-4" />
                Create Poll
            </button>
        </div>
    </VoterLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { Link, usePage } from "@inertiajs/vue3";
import UserData = App.DataTransferObjects.UserData;
import PollData = App.DataTransferObjects.PollData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import Modal from '@/Components/Modal.vue';
import PollConfirmation from './Partials/PollConfirmation.vue';
import LoginToView from "@/shared/components/LoginToView.vue";
import { useConfigStore } from '@/stores/config-store';
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from 'pinia';
import BrowsePolls from './Partials/BrowsePolls.vue';
import {
    DocumentPlusIcon,
    ClockIcon,
    CheckCircleIcon,
    DocumentTextIcon,
    UsersIcon,
    RocketLaunchIcon,
} from "@heroicons/vue/24/outline";

const props = withDefaults(
    defineProps<{
        polls?: PollData[];
        user: UserData;
        crumbs: [];
        actions: [];
        counts: {
            draftCount: number;
            activeCount: number;
            pendingCount: number;
            answeredCount: number;
            allCount: number;
        };
        platformStats: {
            totalPolls: number;
            reviewingCount: number;
            publishedCount: number;
            totalVotes: number;
        };
    }>(),
    {}
);

let configStore = useConfigStore();
let { showModal } = storeToRefs(configStore);
const pollStore = usePollStore();

// Get current user from page props
const page = usePage();
const currentUserId = computed(() => page.props.auth?.user?.id);

// Reset poll store data when user changes to ensure fresh data
onMounted(() => {
    pollStore.resetAllContexts();
});

// Also watch for user changes and reset when they occur
watch(currentUserId, (newId, oldId) => {
    if (newId !== oldId) {
        pollStore.resetAllContexts();
    }
});

const currentTab = ref('browse');

const changeTab = (tabName: string) => {
    if (tabName === 'browse' && currentTab.value !== 'browse') {
        pollStore.reloadContext('browse', {}).then();
    }

    currentTab.value = tabName;
};

const menuOptions = [
    { name: "Browse",  value: "browse",  count: props.counts?.allCount ?? 0,     param: {} },
    { name: "Drafts",  value: "draft",   count: props.counts?.draftCount ?? 0,   param: { hasDraft: true } },
    { name: "Active",  value: "active",  count: props.counts?.activeCount ?? 0,  param: { hasActive: true } },
    { name: "Pending", value: "pending", count: props.counts?.pendingCount ?? 0, param: { hasPending: true } },
    { name: "Answered",  value: "answered",  count: props.counts?.answeredCount ?? 0,   param: { hasAnswered: true } },
];

// Public users only see Browse; logged-in users see all tabs
const visibleTabs = computed(() =>
    props.user ? menuOptions : menuOptions.filter(o => o.value === 'browse')
);

const answeredCount = computed(() =>
    props.counts?.answeredCount ?? 0
);

const activeTabCount = computed(() => {
    const opt = menuOptions.find(o => o.value === currentTab.value);
    return opt?.count ?? 0;
});

const emptyStateDescription = computed(() => {
    if (currentTab.value === 'draft') return 'Start drafting a poll to gather community sentiment on a topic you care about.';
    if (currentTab.value === 'pending') return 'Polls you have submitted for admin review will appear here.';
    return 'Published and active polls you own will appear here.';
});

const getTabClass = (tabName: string) => {
    if (currentTab.value === tabName) {
        return 'border-b-2 border-sky-500 text-sky-600 dark:text-sky-400 dark:border-sky-400';
    }
    return 'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600';
};

const platformStatCards = computed(() => [
    {
        icon: DocumentTextIcon,
        value: props.platformStats?.totalPolls ?? 0,
        label: 'Total Polls',
    },
    {
        icon: ClockIcon,
        value: props.platformStats?.reviewingCount ?? 0,
        label: 'Under Review',
    },
    {
        icon: RocketLaunchIcon,
        value: props.platformStats?.publishedCount ?? 0,
        label: 'Live & Published',
    },
    {
        icon: UsersIcon,
        value: props.platformStats?.totalVotes ?? 0,
        label: 'Votes Collected',
    },
]);

// Inline EmptyState component to avoid creating new files
const EmptyState = {
    props: ['title', 'description', 'actionLabel', 'actionHref'],
    emits: ['action'],
    template: `
        <div class="flex flex-col items-center justify-center text-center rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 py-16 px-6">
            <slot name="icon" />
            <h3 class="mt-4 text-base font-semibold text-gray-700 dark:text-gray-200">{{ title }}</h3>
            <p class="mt-1 text-sm text-gray-400 dark:text-gray-500 max-w-xs">{{ description }}</p>
            <div class="mt-6">
                <a v-if="actionHref" :href="actionHref"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors">
                    {{ actionLabel }}
                </a>
                <button v-else @click="$emit('action')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors">
                    {{ actionLabel }}
                </button>
            </div>
        </div>
    `,
};
</script>
