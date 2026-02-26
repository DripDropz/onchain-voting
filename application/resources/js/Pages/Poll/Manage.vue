<template>
    <VoterLayout page="Manage Poll" :crumbs="crumbs">
        <div class="w-full">

            <!-- Status Strip -->
            <div class="flex border-b border-gray-800" :class="statusBanner.stripBg">
                <div class="w-1 shrink-0 self-stretch" :class="statusBanner.accent"></div>
                <div class="px-6 py-3 flex items-center gap-3 flex-1 min-w-0">
                    <component :is="statusBanner.icon" class="w-4 h-4 shrink-0" :class="statusBanner.iconColor" />
                    <p class="text-sm min-w-0" :class="statusBanner.textColor">
                        <span class="font-semibold uppercase text-xs tracking-widest mr-2 opacity-60">Status</span>
                        {{ statusBanner.message }}
                    </p>
                </div>
            </div>

            <!-- Page Header -->
            <div class="bg-gray-900 border-b border-gray-800 px-6 py-6">
                <div class="container mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-sky-500/15 border border-sky-500/20 shrink-0 mt-0.5">
                            <ChartPieIcon class="w-5 h-5 text-sky-400" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-widest mb-1">Managing Poll</p>
                            <h1 class="text-2xl font-bold text-white leading-tight">{{ poll.title }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <template v-if="poll.status === 'draft'">
                            <Link
                                :href="route('polls.create.stepOne', { poll: poll.hash })"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium border border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"
                            >
                                <PencilSquareIcon class="w-4 h-4" />
                                Edit Draft
                            </Link>
                            <button
                                @click.prevent="submitForReview()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold bg-amber-500 hover:bg-amber-400 text-white shadow-lg shadow-amber-500/20 transition-colors"
                            >
                                <PaperAirplaneIcon class="w-4 h-4" />
                                Submit for Review
                            </button>
                        </template>

                        <template v-else-if="poll.status === 'pending'">
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium bg-blue-500/10 border border-blue-500/30 text-blue-300 cursor-default select-none">
                                <ClockIcon class="w-4 h-4" />
                                Under Review
                            </span>
                        </template>

                        <template v-else-if="poll.status === 'approved'">
                            <button
                                @click.prevent="showPublishModal = true"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold bg-green-500 hover:bg-green-400 text-white shadow-lg shadow-green-500/20 transition-colors"
                            >
                                <RocketLaunchIcon class="w-4 h-4" />
                                Publish Poll
                            </button>
                        </template>

                        <template v-else-if="poll.status === 'rejected'">
                            <Link
                                :href="route('polls.create.stepOne', { poll: poll.hash })"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold bg-red-500 hover:bg-red-400 text-white shadow-lg shadow-red-500/20 transition-colors"
                            >
                                <PencilSquareIcon class="w-4 h-4" />
                                Edit &amp; Resubmit
                            </Link>
                        </template>

                        <template v-else-if="poll.status === 'published'">
                            <button
                                @click.prevent="showCloseModal = true"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium border border-gray-700 text-gray-400 hover:border-red-600/60 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                            >
                                <LockClosedIcon class="w-4 h-4" />
                                Close Poll
                            </button>
                            <Link
                                :href="route('polls.view', { poll: poll.hash })"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold bg-sky-500 hover:bg-sky-400 text-white shadow-lg shadow-sky-500/20 transition-colors"
                            >
                                <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                                View Public Page
                            </Link>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="px-6 py-8 bg-gray-950 min-h-screen">
                <div class="container mx-auto">
                    <div class="grid gap-6 lg:grid-cols-5">

                        <!-- Left column (wider): Poll Overview -->
                        <div class="lg:col-span-3 flex flex-col gap-6">
                            <div>
                                <div class="flex items-center gap-2.5 mb-4">
                                    <UsersIcon class="w-4 h-4 text-sky-400 shrink-0" />
                                    <h2 class="text-xs font-semibold uppercase tracking-widest text-gray-400 whitespace-nowrap">Poll Overview</h2>
                                    <div class="flex-1 h-px bg-gray-800" />
                                </div>
                                <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
                                    <div class="p-6 border-b border-gray-800">
                                        <PollSupporters :poll="poll" />
                                    </div>
                                    <div class="p-6">
                                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-4">Poll Details</p>
                                        <PollGoals :poll="poll" />
                                    </div>
                                </div>
                            </div>

                            <!-- Poll Question Preview -->
                            <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
                                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-4">Question</p>
                                <p class="text-lg font-semibold text-white mb-4">{{ poll.question?.title }}</p>
                                <ul class="space-y-2">
                                    <li
                                        v-for="choice in poll.question?.choices"
                                        :key="choice.hash"
                                        class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                                    >
                                        {{ choice.title }}
                                        <span v-if="choice.responses_count !== undefined && choice.responses_count > 0" class="text-sm text-gray-500 ml-2">
                                            ({{ choice.responses_count }} votes)
                                        </span>
                                    </li>
                                </ul>
                                <div v-if="poll.description" class="mt-4 pt-4 border-t border-gray-800">
                                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Description</p>
                                    <div class="text-sm text-gray-400" v-html="poll.description"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Right column (narrower): Criteria + Share -->
                        <div class="lg:col-span-2 flex flex-col gap-6">

                            <!-- Voting Criteria -->
                            <div>
                                <div class="flex items-center gap-2.5 mb-4">
                                    <AdjustmentsHorizontalIcon class="w-4 h-4 text-sky-400 shrink-0" />
                                    <h2 class="text-xs font-semibold uppercase tracking-widest text-gray-400 whitespace-nowrap">Voting Criteria</h2>
                                    <div class="flex-1 h-px bg-gray-800" />
                                </div>
                                <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
                                    <p class="text-sm text-gray-500 mb-5">Restrict who can vote by requiring an NFT or fungible token.</p>
                                    <Criteria :model="poll" mode="editable" />
                                </div>
                            </div>

                            <!-- Grow Your Poll / Share -->
                            <div>
                                <div class="flex items-center gap-2.5 mb-4">
                                    <MegaphoneIcon class="w-4 h-4 text-sky-400 shrink-0" />
                                    <h2 class="text-xs font-semibold uppercase tracking-widest text-gray-400 whitespace-nowrap">Grow Your Poll</h2>
                                    <div class="flex-1 h-px bg-gray-800" />
                                </div>
                                <div class="bg-gray-900 rounded-xl border border-gray-800 p-6 flex flex-col gap-5">
                                    <div>
                                        <p class="text-sm font-semibold text-white mb-1">Share This Poll</p>
                                        <p class="text-sm text-gray-500">Spread awareness by sharing your poll link with your community.</p>
                                    </div>
                                    <PollShareWidget :link="link" :title="poll.title" />
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <Modal :show="showCloseModal">
                <ClosePoll :poll="poll" @close="showCloseModal = false" />
            </Modal>
            <Modal :show="showPublishModal" :modalType="'publish'">
                <PublishPoll :poll="poll" @close="showPublishModal = false" />
            </Modal>
        </div>
    </VoterLayout>
</template>

<script lang="ts" setup>
import { computed, ref } from "vue";
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PollData = App.DataTransferObjects.PollData;
import { Link, useForm } from "@inertiajs/vue3";
import {
    PencilSquareIcon,
    PaperAirplaneIcon,
    RocketLaunchIcon,
    ArrowTopRightOnSquareIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    CheckBadgeIcon,
    ChartPieIcon,
    UsersIcon,
    AdjustmentsHorizontalIcon,
    MegaphoneIcon,
    LockClosedIcon,
} from "@heroicons/vue/20/solid";
import AlertService from "@/shared/Services/alert-service";
import Criteria from "@/shared/components/Criteria.vue";
import Modal from "@/Components/Modal.vue";
import PublishPoll from "./Partials/PublishPoll.vue";
import ClosePoll from "./Partials/ClosePoll.vue";
import PollGoals from "./Partials/PollGoals.vue";
import PollSupporters from "./Partials/PollSupporters.vue";
import PollShareWidget from "./Partials/PollShareWidget.vue";

const props = defineProps<{
    poll: PollData;
    crumbs?: [];
    actions?: [];
}>();

const showPublishModal = ref(false);
const showCloseModal = ref(false);
const form = useForm({
    status: props.poll?.status,
});

async function submitForReview() {
    try {
        await form.patch(route("polls.submit", { poll: props.poll.hash }));
        props.poll.status = "pending";
        AlertService.show(["Poll submitted for admin review"], "success");
    } catch (error) {
        AlertService.show(["There was an error submitting the poll"], "error");
    }
}

const link = route("polls.view", { poll: props.poll.hash });

const statusBanner = computed(() => {
    const s = props.poll.status;
    const configs: Record<string, any> = {
        draft: {
            stripBg:   "bg-amber-950/30",
            accent:    "bg-amber-500",
            icon:      ClockIcon,
            iconColor: "text-amber-400",
            textColor: "text-amber-200",
            message:   "Saved as a draft — submit for admin review when ready.",
        },
        pending: {
            stripBg:   "bg-blue-950/30",
            accent:    "bg-blue-500",
            icon:      ClockIcon,
            iconColor: "text-blue-400",
            textColor: "text-blue-200",
            message:   "Awaiting admin review — we'll notify you once a decision is made.",
        },
        approved: {
            stripBg:   "bg-green-950/30",
            accent:    "bg-green-500",
            icon:      CheckCircleIcon,
            iconColor: "text-green-400",
            textColor: "text-green-200",
            message:   "Approved — your poll is ready to publish and start collecting votes.",
        },
        rejected: {
            stripBg:   "bg-red-950/30",
            accent:    "bg-red-500",
            icon:      XCircleIcon,
            iconColor: "text-red-400",
            textColor: "text-red-200",
            message:   "Not approved. Please edit your poll and resubmit for another review.",
        },
        published: {
            stripBg:   "bg-sky-950/30",
            accent:    "bg-sky-500",
            icon:      CheckBadgeIcon,
            iconColor: "text-sky-400",
            textColor: "text-sky-200",
            message:   "Live — your poll is publicly visible and collecting votes.",
        },
        closed: {
            stripBg:   "bg-gray-950/30",
            accent:    "bg-gray-500",
            icon:      LockClosedIcon,
            iconColor: "text-gray-400",
            textColor: "text-gray-200",
            message:   "Closed — voting has ended for this poll.",
        },
    };
    return configs[s] ?? configs.draft;
});
</script>
