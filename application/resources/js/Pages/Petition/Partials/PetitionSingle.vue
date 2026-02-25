<template>
    <article :key="petition$.hash" class="w-full">

        <!-- ── HERO ── -->
        <div class="relative w-full h-72 lg:h-96 overflow-hidden bg-gray-900">
            <img
                v-if="petition$.image_url"
                :src="petition$.image_url"
                :alt="petition$.title"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <div
                v-else
                class="absolute inset-0 bg-gradient-to-br from-sky-950 via-gray-900 to-gray-950"
            />
            <!-- gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/50 to-transparent" />

            <!-- title block on hero -->
            <div class="absolute bottom-0 left-0 right-0 px-6 lg:px-10 pb-7 pt-16">
                <h1 class="text-2xl lg:text-4xl font-extrabold text-white leading-tight drop-shadow-lg max-w-3xl">
                    {{ petition$.title }}
                </h1>
                <div class="flex items-center gap-2 mt-2.5 text-sm text-gray-300">
                    <UserCircleIcon class="w-4 h-4 text-gray-400 shrink-0" />
                    <span class="font-medium text-white">{{ petition$.user?.name }}</span>
                    <span class="text-gray-500">·</span>
                    <CalendarIcon class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                    <span>{{ formatDate(petition$.created_at) }}</span>
                </div>
            </div>
        </div>

        <!-- ── STATS BAR ── (published petitions only) -->
        <div
            v-if="petition$.status === 'published'"
            class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-800 border-b border-gray-800 bg-gray-900/80 backdrop-blur-sm"
        >
            <div class="flex flex-col items-center justify-center px-4 py-4 gap-0.5">
                <p class="text-xl font-bold text-white tabular-nums">{{ (petition$.signatures_count ?? 0).toLocaleString() }}</p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Signatures</p>
            </div>
            <div class="flex flex-col items-center justify-center px-4 py-4 gap-0.5">
                <p class="text-xl font-bold text-white tabular-nums">{{ daysLive }}</p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Days Live</p>
            </div>
            <div class="flex flex-col items-center justify-center px-4 py-4 gap-0.5">
                <p class="text-xl font-bold tabular-nums" :class="allGoalsAchieved$ ? 'text-emerald-400' : 'text-sky-400'">
                    {{ allGoalsAchieved$ ? '100' : (currentGoalPercetage$ ?? 0) }}%
                </p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">To Next Goal</p>
            </div>
            <div class="flex flex-col items-center justify-center px-4 py-4 gap-0.5">
                <span
                    class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                    :class="liveStatusBadge.cls"
                >
                    <component :is="liveStatusBadge.icon" class="w-3.5 h-3.5" />
                    {{ liveStatusBadge.label }}
                </span>
                <p class="text-xs text-gray-500 uppercase tracking-wide mt-0.5">Status</p>
            </div>
        </div>

        <!-- ── MAIN CONTENT ── -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8 lg:grid lg:grid-cols-[1fr_360px] lg:gap-10 lg:items-start">

            <!-- LEFT: body + share -->
            <div class="space-y-10">

                <!-- Petition description (prose) -->
                <div
                    class="prose prose-sm sm:prose dark:prose-invert max-w-none
                           prose-headings:font-bold prose-headings:text-white
                           prose-p:text-gray-300 prose-p:leading-relaxed
                           prose-a:text-sky-400 prose-a:no-underline hover:prose-a:underline
                           prose-blockquote:border-sky-500 prose-blockquote:text-gray-400
                           prose-code:text-sky-300 prose-pre:bg-gray-800
                           prose-strong:text-white prose-li:text-gray-300
                           prose-table:text-gray-300 prose-th:text-white"
                    v-html="parseMarkdown(petition$.description ?? '')"
                />

                <!-- Share section (published only, not preview) -->
                <div v-if="petition$.status === 'published' && !isPreview" class="border-t border-gray-800 pt-8">
                    <div class="flex items-center gap-2 mb-5">
                        <MegaphoneIcon class="w-4 h-4 text-sky-400 shrink-0" />
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-400">Help this petition grow</h3>
                    </div>
                    <PetitionShareWidget :link="petitionLink" :title="petition$.title" />
                </div>
            </div>

            <!-- RIGHT: sticky sidebar -->
            <aside class="mt-8 lg:mt-0 lg:sticky lg:top-6 space-y-4">

                <!-- Ballot moved card -->
                <div v-if="petition$?.ballot" class="rounded-xl border border-gray-700 bg-gray-900 p-5 text-center space-y-3">
                    <TrophyIcon class="w-8 h-8 text-amber-400 mx-auto" />
                    <p class="text-base font-bold text-white">Petition moved to ballot</p>
                    <Link :href="route('ballot.view', { ballot: petition$.ballot.hash })">
                        <PrimaryButton :theme="'primary'" class="w-full justify-center gap-2">
                            View Ballot
                            <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                        </PrimaryButton>
                    </Link>
                </div>

                <template v-else>
                    <!-- Progress + Goals card -->
                    <div class="rounded-xl border border-gray-800 bg-gray-900 p-5 space-y-5">
                        <SignatureProgress :petition="petition$" />
                        <div v-if="hasGoals" class="border-t border-gray-800 pt-4">
                            <PetitionGoals :petition="petition$" />
                        </div>
                    </div>

                    <div
                        v-if="hasParticipationCriteria"
                        class="rounded-xl border border-gray-800 bg-gray-900 p-5 space-y-3"
                    >
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Participation Criteria</p>
                        <Criteria :model="petition$" :readonly="true" />
                    </div>

                    <!-- Sign form card -->
                    <div class="rounded-xl border border-gray-800 bg-gray-900 p-5">
                        <!-- Preview overlay -->
                        <div v-if="isPreview" class="relative">
                            <div class="pointer-events-none opacity-30 select-none">
                                <SignPetitionForm :signature="signature" :user="user" />
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900/90 border border-gray-700 text-white text-xs font-semibold">
                                    <LockClosedIcon class="w-3.5 h-3.5" />
                                    Sign form disabled in preview
                                </span>
                            </div>
                        </div>
                        <SignPetitionForm v-else :signature="signature" :user="user" />
                    </div>

                    <!-- Recent Signers (published, not preview, has signatures) -->
                    <div
                        v-if="petition$.status === 'published' && !isPreview && recentSignatures?.length"
                        class="rounded-xl border border-gray-800 bg-gray-900 p-5 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Recent Signers</p>
                            <span class="text-xs text-gray-500">
                                {{ (petition$.signatures_count ?? 0).toLocaleString() }} total
                            </span>
                        </div>
                        <ul class="space-y-2">
                            <li
                                v-for="sig in recentSignatures"
                                :key="sig.hash"
                                class="flex items-center gap-3"
                            >
                                <!-- Avatar -->
                                <div class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center"
                                     :class="sig.type === 'wallet' ? 'bg-sky-900/40 text-sky-400' : 'bg-purple-900/40 text-purple-400'">
                                    <WalletIcon v-if="sig.type === 'wallet'" class="w-3.5 h-3.5" />
                                    <EnvelopeIcon v-else class="w-3.5 h-3.5" />
                                </div>
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-mono text-gray-300 truncate">
                                        {{ sig.masked_address ?? 'Anonymous' }}
                                    </p>
                                    <p class="text-xs text-gray-600">{{ timeAgo(sig.created_at) }}</p>
                                </div>
                                <!-- Type badge -->
                                <span class="shrink-0 text-xs px-1.5 py-0.5 rounded font-medium"
                                      :class="sig.type === 'wallet' ? 'bg-sky-900/40 text-sky-400' : 'bg-purple-900/40 text-purple-400'">
                                    {{ sig.type === 'wallet' ? 'Wallet' : 'Email' }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </template>

            </aside>
        </div>
    </article>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
    UserCircleIcon,
    CalendarIcon,
    LockClosedIcon,
    MegaphoneIcon,
    EnvelopeIcon,
    TrophyIcon,
    CheckBadgeIcon,
    StarIcon,
    DocumentCheckIcon,
    SparklesIcon,
} from '@heroicons/vue/20/solid';
import { WalletIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline';
import { Link, usePage } from '@inertiajs/vue3';
import MarkdownIt from 'markdown-it';
import { storeToRefs } from 'pinia';
import { usePetitionSignatureStore } from '@/Pages/Petition/stores/petition-signature-store';
import SignatureData = App.DataTransferObjects.SignatureData;
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SignatureProgress from './SignatureProgress.vue';
import PetitionGoals from './PetitionGoals.vue';
import SignPetitionForm from './SignPetitionForm.vue';
import PetitionShareWidget from './PetitionShareWidget.vue';
import Criteria from '@/shared/components/Criteria.vue';

const props = defineProps<{
    signature?: SignatureData;
    isPreview?: boolean;
    recentSignatures?: Array<{
        hash: string;
        created_at: string;
        type: 'wallet' | 'email';
        masked_address: string | null;
    }>;
}>();

const page = usePage();
const user = computed(() => page.props.auth?.user);

const petitionSignatureStore = usePetitionSignatureStore();
const { petition$, allGoalsAchieved$, currentGoalPercetage$, visible$, featurePetition$, ballotEligible$ } = storeToRefs(petitionSignatureStore);

const md = new MarkdownIt({ html: false, breaks: true, linkify: true });

const parseMarkdown = (content: string) => md.render(content);

const formatDate = (dateString?: string | null): string => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
};

const timeAgo = (iso?: string | null): string => {
    if (!iso) return '';
    const diff = Date.now() - new Date(iso).getTime();
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return 'just now';
    if (mins < 60) return `${mins}m ago`;
    const hrs = Math.floor(mins / 60);
    if (hrs < 24) return `${hrs}h ago`;
    const days = Math.floor(hrs / 24);
    if (days < 30) return `${days}d ago`;
    return formatDate(iso);
};

const daysLive = computed(() => {
    const from = petition$.value?.started_at ?? petition$.value?.created_at;
    if (!from) return 0;
    return Math.max(0, Math.floor((Date.now() - new Date(from).getTime()) / 86400000));
});

const hasGoals = computed(() =>
    visible$.value?.value2 != null ||
    featurePetition$.value?.value2 != null ||
    ballotEligible$.value?.value2 != null
);

const hasParticipationCriteria = computed(() =>
    !!petition$.value?.rules?.find((rule) => rule.type === 'ft' || rule.type === 'nft')
);

const liveStatusBadge = computed(() => {
    if (petition$.value?.is_featured) {
        return { label: 'Featured', cls: 'bg-amber-500/20 text-amber-400 border-amber-500/30', icon: StarIcon };
    }
    if (petition$.value?.is_visible) {
        return { label: 'Visible', cls: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30', icon: CheckBadgeIcon };
    }
    return { label: 'Collecting', cls: 'bg-sky-500/20 text-sky-400 border-sky-500/30', icon: SparklesIcon };
});

const petitionLink = computed(() =>
    route('petitions.view', { petition: petition$.value?.hash })
);
</script>
