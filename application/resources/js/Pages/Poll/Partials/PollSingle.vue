<template>
    <article :key="poll.hash" class="w-full">

        <!-- ── HERO ── -->
        <div class="relative w-full h-72 lg:h-96 overflow-hidden bg-gray-900">
            <img
                v-if="poll.image_url"
                :src="poll.image_url"
                :alt="poll.title || poll.question?.title"
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
                    {{ poll.title || poll.question?.title }}
                </h1>
                <div class="flex items-center gap-2 mt-2.5 text-sm text-gray-300">
                    <UserCircleIcon class="w-4 h-4 text-gray-400 shrink-0" />
                    <span class="font-medium text-white">{{ poll.user?.name || 'Anonymous' }}</span>
                    <span class="text-gray-500">·</span>
                    <CalendarIcon class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                    <span>{{ formatDate(poll.created_at) }}</span>
                </div>
            </div>
        </div>

        <!-- ── STATS BAR ── (all polls, different data based on status) -->
        <div
            class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-800 border-b border-gray-800 bg-gray-900/80 backdrop-blur-sm"
        >
            <div class="flex flex-col items-center justify-center px-4 py-4 gap-0.5">
                <p class="text-xl font-bold text-white tabular-nums">{{ (poll.responses_count ?? 0).toLocaleString() }}</p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Votes</p>
            </div>
            <div class="flex flex-col items-center justify-center px-4 py-4 gap-0.5">
                <p class="text-xl font-bold text-white tabular-nums">{{ daysLive }}</p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Days Live</p>
            </div>
            <div class="flex flex-col items-center justify-center px-4 py-4 gap-0.5">
                <p class="text-xl font-bold text-sky-400 tabular-nums">{{ totalChoices }}</p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Options</p>
            </div>
            <div class="flex flex-col items-center justify-center px-4 py-4 gap-0.5">
                <span
                    class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                    :class="statusBadge.cls"
                >
                    <component :is="statusBadge.icon" class="w-3.5 h-3.5" />
                    {{ statusBadge.label }}
                </span>
                <p class="text-xs text-gray-500 uppercase tracking-wide mt-0.5">{{ statusBadge.subtext }}</p>
            </div>
        </div>

        <!-- ── MAIN CONTENT ── -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8 lg:grid lg:grid-cols-[1fr_360px] lg:gap-10 lg:items-start">

            <!-- LEFT: poll content + results -->
            <div class="space-y-10">

                <!-- Results Display for published polls -->
                <div v-if="poll.status === 'published'" class="space-y-6">
                    <div class="space-y-4">
                        <div
                            v-for="choice in poll.question?.choices"
                            :key="choice.hash"
                            class="space-y-2"
                        >
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-white font-medium">{{ choice.title }}</span>
                                <span class="text-gray-400">{{ getVotePercentage(choice.hash) }}% ({{ getVoteCount(choice.hash) }})</span>
                            </div>
                            <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-sky-500 rounded-full transition-all duration-500"
                                    :style="{ width: getVotePercentage(choice.hash) + '%' }"
                                />
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 pt-2">
                            Total votes: {{ (poll.responses_count ?? 0).toLocaleString() }}
                        </p>
                    </div>
                </div>

                <!-- Non-published status message with visual styling -->
                <div v-else class="rounded-xl border-2 border-dashed border-gray-700 bg-gray-900/30 p-8 text-center space-y-4">
                    <div class="flex justify-center">
                        <component :is="statusIcon" class="w-12 h-12" :class="statusIconColor" />
                    </div>
                    <div>
                        <p class="text-lg font-semibold" :class="statusTextColor">
                            {{ statusTitle }}
                        </p>
                        <p class="text-gray-400 mt-2 max-w-md mx-auto">
                            {{ statusDescription }}
                        </p>
                    </div>
                    <div v-if="poll.status === 'draft' || poll.status === 'rejected'" class="pt-2">
                        <Link
                            :href="route('polls.manage', { poll: poll.hash })"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-lg hover:bg-sky-500 transition-colors"
                        >
                            Manage Poll
                        </Link>
                    </div>
                    <div v-else-if="poll.status === 'approved'" class="pt-2">
                        <Link
                            :href="route('polls.manage', { poll: poll.hash })"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-500 transition-colors"
                        >
                            Publish Now
                        </Link>
                    </div>
                </div>

                <!-- Description -->
                <div
                    v-if="poll.description"
                    class="prose prose-sm sm:prose dark:prose-invert max-w-none
                           prose-headings:font-bold prose-headings:text-white
                           prose-p:text-gray-300 prose-p:leading-relaxed
                           prose-a:text-sky-400 prose-a:no-underline hover:prose-a:underline
                           prose-blockquote:border-sky-500 prose-blockquote:text-gray-400
                           prose-code:text-sky-300 prose-pre:bg-gray-800
                           prose-strong:text-white prose-li:text-gray-300"
                    v-html="parseMarkdown(poll.description)"
                />

                <!-- Share section (published only) -->
                <div v-if="poll.status === 'published'" class="border-t border-gray-800 pt-8">
                    <div class="flex items-center gap-2 mb-5">
                        <MegaphoneIcon class="w-4 h-4 text-sky-400 shrink-0" />
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-400">Share this poll</h3>
                    </div>
                    <PollShareWidget :link="pollLink" :title="poll.title || poll.question?.title" />
                </div>
            </div>

            <!-- RIGHT: sticky sidebar -->
            <aside class="mt-8 lg:mt-0 lg:sticky lg:top-6 space-y-4">

                <!-- Voting Form Card (like SignPetitionForm) -->
                <div v-if="poll.status === 'published'" class="rounded-xl border border-gray-800 bg-gray-900 p-5 space-y-4">
                    
                    <!-- Already voted - Show Receipt -->
                    <div v-if="hasUserVoted" class="space-y-4">
                        <div class="flex items-center gap-2 text-emerald-400">
                            <CheckCircleIcon class="w-5 h-5" />
                            <span class="font-semibold">Your Vote</span>
                        </div>
                        
                        <!-- Vote Receipt -->
                        <div class="rounded-lg bg-emerald-950/30 border border-emerald-800/50 p-4 space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                                    <CheckCircleIcon class="w-4 h-4" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-emerald-300">
                                        Successfully Voted
                                    </p>
                                    <p class="text-xs text-emerald-400/70 mt-1">
                                        {{ formatDate(userResponse?.created_at) }}
                                    </p>
                                </div>
                            </div>
                            <div class="border-t border-emerald-800/30 pt-3">
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">You selected</p>
                                <p class="text-sm font-semibold text-white">
                                    {{ userSelectedChoice?.title || 'Your vote' }}
                                </p>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-400">
                            Thank you for participating! Results are shown on the left.
                        </p>
                    </div>

                    <!-- Not logged in - Show Login Prompt -->
                    <div v-else-if="!user" class="space-y-4">
                        <div class="flex items-center gap-2 text-sky-400">
                            <SparklesIcon class="w-5 h-5" />
                            <span class="font-semibold">Cast your vote</span>
                        </div>
                        <div class="rounded-xl bg-gray-800/60 border border-gray-700 px-4 py-5 text-center space-y-3">
                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/15 border border-sky-500/20">
                                <WalletIcon class="w-5 h-5 text-sky-400" />
                            </div>
                            <p class="text-sm text-gray-400">Connect your wallet to vote on this poll</p>
                            <LoginToView>
                                <span class="sr-only">Login to vote</span>
                            </LoginToView>
                        </div>
                    </div>

                    <!-- Logged in, hasn't voted - Show Voting Options -->
                    <div v-else class="space-y-4">
                        <div class="flex items-center gap-2 text-sky-400">
                            <SparklesIcon class="w-5 h-5" />
                            <span class="font-semibold">Cast your vote</span>
                        </div>
                        
                        <!-- Voting Options -->
                        <div class="space-y-2">
                            <label
                                v-for="choice in poll.question?.choices"
                                :key="choice.hash"
                                class="flex items-center gap-3 p-3 rounded-lg border border-gray-800 bg-gray-800/50 cursor-pointer transition-all hover:border-sky-500/50 hover:bg-gray-800"
                                :class="{ 'border-sky-500 bg-sky-900/20': selectedChoice === choice.hash }"
                            >
                                <input
                                    type="radio"
                                    :value="choice.hash"
                                    v-model="selectedChoice"
                                    class="w-4 h-4 text-sky-500 border-gray-600 focus:ring-sky-500 focus:ring-offset-gray-900"
                                />
                                <span class="text-sm text-white font-medium">{{ choice.title }}</span>
                            </label>
                        </div>

                        <!-- Sign & Submit Vote Button (with wallet signing like petitions) -->
                        <VoteWithWallet 
                            :poll="poll" 
                            :selected-choice="selectedChoice"
                            @success="handleVoteSuccess"
                        />
                    </div>
                </div>

                <!-- Participation Criteria -->
                <div
                    v-if="hasParticipationCriteria"
                    class="rounded-xl border border-gray-800 bg-gray-900 p-5 space-y-3"
                >
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Participation Criteria</p>
                    <Criteria :model="poll" mode="readonly" />
                </div>

                <!-- Recent Voters (if available) -->
                <div
                    v-if="poll.status === 'published' && recentVotes?.length"
                    class="rounded-xl border border-gray-800 bg-gray-900 p-5 space-y-3"
                >
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Recent Votes</p>
                        <span class="text-xs text-gray-500">
                            {{ (poll.responses_count ?? 0).toLocaleString() }} total
                        </span>
                    </div>
                    <ul class="space-y-2">
                        <li
                            v-for="vote in recentVotes"
                            :key="vote.hash"
                            class="flex items-center gap-3"
                        >
                            <div class="shrink-0 w-7 h-7 rounded-full bg-sky-900/40 text-sky-400 flex items-center justify-center">
                                <WalletIcon class="w-3.5 h-3.5" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-mono text-gray-300 truncate">
                                    {{ vote.masked_address ?? 'Anonymous' }}
                                </p>
                                <p class="text-xs text-gray-600">{{ timeAgo(vote.created_at) }}</p>
                            </div>
                        </li>
                    </ul>
                </div>

            </aside>
        </div>
    </article>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import {
    UserCircleIcon,
    CalendarIcon,
    MegaphoneIcon,
    CheckCircleIcon,
    SparklesIcon,
    ClockIcon,
    PencilIcon,
    CheckIcon,
    XCircleIcon,
    RocketLaunchIcon,
    LockClosedIcon,
} from '@heroicons/vue/20/solid';
import { WalletIcon } from '@heroicons/vue/24/outline';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import MarkdownIt from 'markdown-it';
import PollData = App.DataTransferObjects.PollData;
import Criteria from '@/shared/components/Criteria.vue';
import PollShareWidget from './PollShareWidget.vue';
import AlertService from '@/shared/Services/alert-service';
import LoginToView from '@/shared/components/LoginToView.vue';
import VoteWithWallet from './VoteWithWallet.vue';

const props = defineProps<{
    poll: PollData;
    recentVotes?: Array<{
        hash: string;
        created_at: string;
        masked_address: string | null;
    }>;
}>();

const page = usePage();
const user = computed(() => page.props.auth?.user);

const md = new MarkdownIt({ html: false, breaks: true, linkify: true });
const parseMarkdown = (content: string) => md.render(content);

const selectedChoice = ref<string | null>(null);

const hasUserVoted = computed(() => {
    return (props.poll.user_responses?.length ?? 0) > 0;
});

const userResponse = computed(() => {
    return props.poll.user_responses?.[0] ?? null;
});

const userSelectedChoice = computed(() => {
    if (!userResponse.value?.choices?.length) return null;
    return userResponse.value.choices[0];
});

const totalChoices = computed(() => props.poll.question?.choices?.length ?? 0);

const getVoteCount = (choiceHash: string): number => {
    const choice = props.poll.question?.choices?.find(c => c.hash === choiceHash);
    return choice?.responses_count ?? 0;
};

const getVotePercentage = (choiceHash: string): number => {
    const total = props.poll.responses_count ?? 0;
    if (total === 0) return 0;
    const count = getVoteCount(choiceHash);
    return Math.round((count / total) * 100);
};

const handleVoteSuccess = () => {
    // Reload to show updated results and vote receipt
    window.location.reload();
};

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
    const from = props.poll.started_at ?? props.poll.created_at;
    if (!from) return 0;
    return Math.max(0, Math.floor((Date.now() - new Date(from).getTime()) / 86400000));
});

const hasParticipationCriteria = computed(() =>
    !!props.poll?.rules?.find((rule) => rule.type === 'ft' || rule.type === 'nft')
);

const statusBadge = computed(() => {
    const status = props.poll.status;
    if (status === 'published') {
        return { 
            label: 'Live', 
            subtext: 'Status',
            cls: 'bg-sky-500/20 text-sky-400 border border-sky-500/30', 
            icon: SparklesIcon 
        };
    }
    if (status === 'draft') {
        return { 
            label: 'Draft', 
            subtext: 'Edit to submit',
            cls: 'bg-amber-500/20 text-amber-400 border border-amber-500/30', 
            icon: PencilIcon 
        };
    }
    if (status === 'pending') {
        return { 
            label: 'Review', 
            subtext: 'Awaiting approval',
            cls: 'bg-blue-500/20 text-blue-400 border border-blue-500/30', 
            icon: ClockIcon 
        };
    }
    if (status === 'approved') {
        return { 
            label: 'Approved', 
            subtext: 'Ready to publish',
            cls: 'bg-green-500/20 text-green-400 border border-green-500/30', 
            icon: CheckIcon 
        };
    }
    if (status === 'rejected') {
        return { 
            label: 'Rejected', 
            subtext: 'Edit & resubmit',
            cls: 'bg-red-500/20 text-red-400 border border-red-500/30', 
            icon: XCircleIcon 
        };
    }
    if (status === 'closed') {
        return { 
            label: 'Closed', 
            subtext: 'Voting ended',
            cls: 'bg-gray-500/20 text-gray-400 border border-gray-500/30', 
            icon: LockClosedIcon 
        };
    }
    return { 
        label: status, 
        subtext: 'Status',
        cls: 'bg-gray-500/20 text-gray-400 border border-gray-500/30', 
        icon: SparklesIcon 
    };
});

const statusIcon = computed(() => {
    const status = props.poll.status;
    if (status === 'draft') return PencilIcon;
    if (status === 'pending') return ClockIcon;
    if (status === 'approved') return RocketLaunchIcon;
    if (status === 'rejected') return XCircleIcon;
    if (status === 'closed') return LockClosedIcon;
    return SparklesIcon;
});

const statusIconColor = computed(() => {
    const status = props.poll.status;
    if (status === 'draft') return 'text-amber-400';
    if (status === 'pending') return 'text-blue-400';
    if (status === 'approved') return 'text-green-400';
    if (status === 'rejected') return 'text-red-400';
    if (status === 'closed') return 'text-gray-400';
    return 'text-sky-400';
});

const statusTextColor = computed(() => {
    const status = props.poll.status;
    if (status === 'draft') return 'text-amber-300';
    if (status === 'pending') return 'text-blue-300';
    if (status === 'approved') return 'text-green-300';
    if (status === 'rejected') return 'text-red-300';
    if (status === 'closed') return 'text-gray-300';
    return 'text-white';
});

const statusTitle = computed(() => {
    const status = props.poll.status;
    if (status === 'draft') return 'Draft Poll';
    if (status === 'pending') return 'Under Review';
    if (status === 'approved') return 'Approved & Ready';
    if (status === 'rejected') return 'Not Approved';
    return 'Poll';
});

const statusDescription = computed(() => {
    const status = props.poll.status;
    if (status === 'draft') return 'This poll is in draft mode. You can edit it and submit for review when ready.';
    if (status === 'pending') return 'This poll is currently being reviewed by our team. You\'ll be notified once a decision is made.';
    if (status === 'approved') return 'Great news! Your poll has been approved. Publish it now to start collecting votes.';
    if (status === 'rejected') return 'This poll didn\'t meet our guidelines. Edit it and submit again for review.';
    return '';
});

const pollLink = computed(() =>
    route('polls.view', { poll: props.poll.hash })
);
</script>
