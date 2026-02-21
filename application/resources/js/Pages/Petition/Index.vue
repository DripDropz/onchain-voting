<template>
    <VoterLayout page="Petitions" :crumbs="crumbs" :actions="[createPetitionAction]">
        <section class="w-full py-12 mx-auto container">
            <div class="inner-container">
                <h2 class="mb-6 text-3xl font-bold leading-tight text-center text-gray-800 xl:text-4xl dark:text-gray-200">
                    Petitions
                </h2>

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
                <PetitionBrowser v-if="currentTab === 'browse' && props.platformStats.publishedCount > 0" :context="'browse'" :params="{}" />

                <template v-else-if="!!user">
                    <template v-for="option in menuOptions" :key="option.value">
                        <PetitionBrowser
                            v-if="currentTab === option.value && option.value !== 'browse'"
                            :context="option.value"
                            :params="option.param"
                        />
                    </template>
                </template>

                <div v-else-if="currentTab !== 'signed' && currentTab !== 'browse'" class="py-16">
                    <LoginToView>
                        <span class="dark:text-white">Login to view your {{ currentTab }} petitions.</span>
                    </LoginToView>
                </div>
            </div>

            <!-- Empty browse state -->
            <template>
                <div
                    v-if="currentTab === 'browse' && props.platformStats.publishedCount === 0"
                    class="inner-container py-10"
                >
                    <!-- Petition lifecycle strip -->
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden mb-6">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                How petitions work
                            </h3>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch divide-y sm:divide-y-0 sm:divide-x divide-gray-100 dark:divide-gray-800">
                            <div
                                v-for="(step, index) in lifecycleSteps"
                                :key="step.label"
                                class="flex flex-1 flex-row sm:flex-col items-center gap-3 sm:gap-2 px-5 py-4 sm:py-5 sm:text-center"
                            >
                                <div class="shrink-0 flex items-center justify-center w-9 h-9 rounded-full" :class="step.bgClass">
                                    <component :is="step.icon" class="w-4.5 h-4.5" :class="step.iconClass" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5 sm:justify-center">
                                        <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">{{ index + 1 }}</span>
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ step.label }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 leading-snug">{{ step.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity note + CTA -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 px-6 py-5">
                        <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                            <ClockIcon class="w-5 h-5 shrink-0 text-sky-400" />
                            <span v-if="props.platformStats.reviewingCount > 0">
                                <strong class="text-gray-700 dark:text-gray-200">{{ props.platformStats.reviewingCount }}</strong>
                                {{ props.platformStats.reviewingCount === 1 ? 'petition is' : 'petitions are' }}
                                currently working through our review process. Check back soon.
                            </span>
                            <span v-else>
                                No active petitions yet. Be the first to start one and make your voice heard.
                            </span>
                        </div>
                        <a
                            v-if="user"
                            :href="route('petitions.create')"
                            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors"
                        >
                            <DocumentPlusIcon class="w-4 h-4" />
                            Create a Petition
                        </a>
                        <button
                            v-else
                            @click="showModal = true"
                            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors"
                        >
                            <DocumentPlusIcon class="w-4 h-4" />
                            Create a Petition
                        </button>
                    </div>
                </div>
            </template>

            <template v-if="user">
                <div
                    v-if="currentTab === 'signed' && signedCount === 0"
                    class="inner-container py-16"
                >
                    <EmptyState
                        title="No signed petitions yet"
                        description="Browse active petitions and add your voice to causes you care about."
                        action-label="Browse Petitions"
                        @action="changeTab('browse')"
                    >
                        <template #icon>
                            <PencilSquareIcon class="w-12 h-12 text-gray-300 dark:text-gray-600" />
                        </template>
                    </EmptyState>
                </div>

                <div
                    v-else-if="['pending', 'draft', 'active'].includes(currentTab) && activeTabCount === 0"
                    class="inner-container py-16"
                >
                    <EmptyState
                        :title="`No ${currentTab} petitions`"
                        :description="emptyStateDescription"
                        action-label="Create a Petition"
                        :action-href="route('petitions.create')"
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
                <PetitionConfirmation @close="showModal = false" />
            </Modal>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from "@inertiajs/vue3";
import UserData = App.DataTransferObjects.UserData;
import PetitionData = App.DataTransferObjects.PetitionData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import Modal from '@/Components/Modal.vue';
import PetitionConfirmation from './Partials/PetitionConfirmation.vue';
import LoginToView from "@/shared/components/LoginToView.vue";
import { useConfigStore } from '@/stores/config-store';
import { storeToRefs } from 'pinia';
import PetitionBrowser from './Partials/PetitionBrowser.vue';
import {
    PencilSquareIcon,
    DocumentPlusIcon,
    ClockIcon,
    CheckCircleIcon,
    MagnifyingGlassIcon,
    DocumentTextIcon,
    UsersIcon,
    CheckBadgeIcon,
    RocketLaunchIcon,
    PencilIcon,
} from "@heroicons/vue/24/outline";

const props = withDefaults(
    defineProps<{
        petitions?: PetitionData[];
        user: UserData;
        crumbs: [];
        actions: [];
        counts: any;
        platformStats: {
            totalPetitions: number;
            reviewingCount: number;
            publishedCount: number;
            totalSignatures: number;
        };
    }>(),
    {}
);

let configStore = useConfigStore();
let { showModal } = storeToRefs(configStore);

const createPetitionAction = computed(() =>
    props.user
        ? { label: 'Create Petition', link: route('petitions.create') }
        : { label: 'Create Petition', clickAction: 'showModal' }
);

const currentTab = ref('browse');

const changeTab = (tabName: string) => {
    currentTab.value = tabName;
};

const menuOptions = [
    { name: "Browse",  value: "browse",  count: props.counts.allCount,     param: {} },
    { name: "Drafts",  value: "draft",   count: props.counts.draftCount,   param: { statusfilter: ['draft'] } },
    { name: "Active",  value: "active",  count: props.counts.activeCount,  param: { statusfilter: ['active'] } },
    { name: "Pending", value: "pending", count: props.counts.pendingCount,  param: { hasPending: true } },
    { name: "Signed",  value: "signed",  count: props.counts.signedCount,   param: { hasSigned: true } },
];

// Public users only see Browse; logged-in users see all tabs
const visibleTabs = computed(() =>
    props.user ? menuOptions : menuOptions.filter(o => o.value === 'browse')
);

const signedCount = computed(() =>
    props.counts.signedCount ?? 0
);

const activeTabCount = computed(() => {
    const opt = menuOptions.find(o => o.value === currentTab.value);
    return opt?.count ?? 0;
});

const emptyStateDescription = computed(() => {
    if (currentTab.value === 'draft') return 'Start drafting a petition to raise awareness for a cause you care about.';
    if (currentTab.value === 'pending') return 'Petitions you have submitted for admin review will appear here.';
    return 'Published and active petitions you own will appear here.';
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
        value: props.platformStats?.totalPetitions ?? 0,
        label: 'Total Petitions',
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
        value: props.platformStats?.totalSignatures ?? 0,
        label: 'Signatures Collected',
    },
]);

const lifecycleSteps = [
    {
        label: 'Draft',
        description: 'Write and prepare your petition',
        icon: PencilIcon,
        bgClass: 'bg-amber-100 dark:bg-amber-900/30',
        iconClass: 'text-amber-600 dark:text-amber-400',
    },
    {
        label: 'Under Review',
        description: 'Admins check content and eligibility',
        icon: ClockIcon,
        bgClass: 'bg-blue-100 dark:bg-blue-900/30',
        iconClass: 'text-blue-600 dark:text-blue-400',
    },
    {
        label: 'Approved',
        description: 'Ready to be published publicly',
        icon: CheckBadgeIcon,
        bgClass: 'bg-green-100 dark:bg-green-900/30',
        iconClass: 'text-green-600 dark:text-green-400',
    },
    {
        label: 'Published',
        description: 'Live and open for signatures',
        icon: RocketLaunchIcon,
        bgClass: 'bg-sky-100 dark:bg-sky-900/30',
        iconClass: 'text-sky-600 dark:text-sky-400',
    },
];

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
