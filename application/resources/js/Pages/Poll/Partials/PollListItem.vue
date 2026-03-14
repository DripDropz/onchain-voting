<template>
    <div
        v-if="!deleted"
        class="group relative flex flex-col bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden"
    >
        <!-- Status accent bar -->
        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-xl" :class="statusBarColor" />

        <!-- Image with placeholder -->
        <div class="relative w-full h-32 overflow-hidden bg-gray-800">
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
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 to-transparent" />
        </div>

        <div class="flex flex-col flex-1 pl-5 pr-5 pt-5 pb-4 gap-3">
            <!-- Header row: title + status badge -->
            <div class="flex items-start justify-between gap-4">
                <Link
                    :href="route('polls.view', { poll: poll.hash })"
                    class="text-lg font-semibold text-gray-900 dark:text-white leading-snug hover:text-sky-500 dark:hover:text-sky-400 transition-colors line-clamp-2"
                >
                    {{ poll.title || poll.question?.title }}
                </Link>
                <span
                    class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                    :class="statusBadgeClass"
                >
                    {{ statusLabel }}
                </span>
            </div>

            <!-- Description -->
            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">
                {{ descriptionPreview }}
            </p>

            <!-- Inline delete confirmation -->
            <div
                v-if="confirmingDelete"
                class="flex items-center gap-3 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 px-3 py-2 text-sm"
            >
                <ExclamationTriangleIcon class="w-4 h-4 shrink-0 text-red-500" />
                <span class="flex-1 text-red-700 dark:text-red-300 font-medium">Delete this poll?</span>
                <button
                    @click.prevent="deletePoll"
                    :disabled="deleteForm.processing"
                    class="px-3 py-1 rounded-md bg-red-500 hover:bg-red-600 text-white text-xs font-semibold disabled:opacity-50"
                >
                    {{ deleteForm.processing ? 'Deleting…' : 'Yes, delete' }}
                </button>
                <button
                    @click.prevent="confirmingDelete = false"
                    class="px-3 py-1 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    Cancel
                </button>
            </div>
        </div>

        <!-- Footer row -->
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
            <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
                <span class="flex items-center gap-1">
                    <UsersIcon class="w-3.5 h-3.5" />
                    {{ poll.responses_count ?? 0 }}
                    <span class="hidden sm:inline">votes</span>
                </span>
                <span class="flex items-center gap-1">
                    <CalendarIcon class="w-3.5 h-3.5" />
                    {{ formatDate(poll.created_at) }}
                </span>
            </div>

            <!-- Status-specific actions (owner only) -->
            <div v-if="isOwner" class="flex items-center gap-2">

                <!-- DRAFT: Manage, Edit, Delete, Submit -->
                <template v-if="poll.status === 'draft'">
                    <Link
                        :href="route('polls.manage', { poll: poll.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <Cog6ToothIcon class="w-3.5 h-3.5" />
                        Manage
                    </Link>
                    <Link
                        :href="route('polls.create.stepOne', { poll: poll.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <PencilIcon class="w-3.5 h-3.5" />
                        Edit
                    </Link>
                    <button
                        @click.prevent="confirmingDelete = true"
                        class="action-btn action-btn-danger"
                    >
                        <TrashIcon class="w-3.5 h-3.5" />
                        Delete
                    </button>
                    <button
                        @click.prevent="submitForReview"
                        :disabled="submitForm.processing"
                        class="action-btn action-btn-primary"
                    >
                        <RocketLaunchIcon class="w-3.5 h-3.5" />
                        Submit
                    </button>
                </template>

                <!-- PENDING: Manage + Under Review badge -->
                <template v-else-if="poll.status === 'pending'">
                    <Link
                        :href="route('polls.manage', { poll: poll.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <Cog6ToothIcon class="w-3.5 h-3.5" />
                        Manage
                    </Link>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50">
                        <ClockIcon class="w-3.5 h-3.5" />
                        Under Review
                    </span>
                </template>

                <!-- APPROVED: Manage, Publish -->
                <template v-else-if="poll.status === 'approved'">
                    <Link
                        :href="route('polls.manage', { poll: poll.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <Cog6ToothIcon class="w-3.5 h-3.5" />
                        Manage
                    </Link>
                    <button
                        @click.prevent="showPublishModal = true"
                        class="action-btn action-btn-primary"
                    >
                        <RocketLaunchIcon class="w-3.5 h-3.5" />
                        Publish
                    </button>
                </template>

                <!-- REJECTED: Manage, Edit & Resubmit -->
                <template v-else-if="poll.status === 'rejected'">
                    <Link
                        :href="route('polls.manage', { poll: poll.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <Cog6ToothIcon class="w-3.5 h-3.5" />
                        Manage
                    </Link>
                    <Link
                        :href="route('polls.create.stepOne', { poll: poll.hash })"
                        class="action-btn action-btn-primary"
                    >
                        <PencilIcon class="w-3.5 h-3.5" />
                        Edit &amp; Resubmit
                    </Link>
                </template>

                <!-- PUBLISHED: Manage + View public page -->
                <template v-else-if="poll.status === 'published'">
                    <Link
                        :href="route('polls.manage', { poll: poll.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <Cog6ToothIcon class="w-3.5 h-3.5" />
                        Manage
                    </Link>
                    <Link
                        :href="route('polls.view', { poll: poll.hash })"
                        class="action-btn action-btn-primary"
                    >
                        <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                        View
                    </Link>
                </template>

            </div>

            <!-- Non-owner: just show View for published polls -->
            <div v-else-if="poll.status === 'published'" class="flex items-center gap-2">
                <Link
                    :href="route('polls.view', { poll: poll.hash })"
                    class="action-btn action-btn-primary"
                >
                    <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                    View
                </Link>
            </div>
        </div>

        <Modal :show="showPublishModal" :modalType="'publish'">
            <PublishPoll :poll="poll" @close="showPublishModal = false" />
        </Modal>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import PollData = App.DataTransferObjects.PollData;

import Modal from "@/Components/Modal.vue";
import PublishPoll from "./PublishPoll.vue";
import {
    UsersIcon,
    CalendarIcon,
    PencilIcon,
    TrashIcon,
    RocketLaunchIcon,
    ArrowTopRightOnSquareIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    Cog6ToothIcon,
} from "@heroicons/vue/20/solid";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import AlertService from "@/shared/Services/alert-service";
import { usePollStore } from "@/stores/poll-store";

const props = defineProps<{
    poll: PollData;
}>();

const descriptionPreview = computed(() => {
    // For polls, show the description or a truncated version of the question
    if (props.poll.description) {
        return props.poll.description;
    }
    // If no description, don't show anything or show question preview
    return '';
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isOwner = computed(() => !!user.value && props.poll?.user_id === user.value.id);

const pollStore = usePollStore();

const showPublishModal = ref(false);
const confirmingDelete = ref(false);
const deleted = ref(false);

const formatDate = (dateString: string): string => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString(undefined, {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};

const deleteForm = useForm({});
const submitForm = useForm({});

const deletePoll = () => {
    deleteForm.delete(route("polls.destroy", { poll: props.poll.hash }), {
        onSuccess: () => {
            deleted.value = true;
            pollStore.removePoll?.(props.poll.hash);
            AlertService.show(["Poll deleted."], "success");
        },
        onError: () => {
            AlertService.show(["Could not delete the poll."], "error");
            confirmingDelete.value = false;
        },
    });
};

const submitForReview = () => {
    submitForm.patch(route("polls.submit", { poll: props.poll.hash }), {
        onSuccess: () => {
            AlertService.show(["Poll submitted for review."], "success");
        },
        onError: () => {
            AlertService.show(["Could not submit the poll."], "error");
        },
    });
};

const statusLabel = computed(() => {
    const labels: Record<string, string> = {
        draft:     "Draft",
        pending:   "Under Review",
        approved:  "Approved",
        rejected:  "Rejected",
        published: "Published",
        closed:    "Closed",
    };
    return labels[props.poll.status] ?? props.poll.status;
});

const statusBarColor = computed(() => ({
    "bg-amber-400":  props.poll.status === "draft",
    "bg-blue-400":   props.poll.status === "pending",
    "bg-green-500":  props.poll.status === "approved",
    "bg-red-500":    props.poll.status === "rejected",
    "bg-sky-500":    props.poll.status === "published",
    "bg-gray-400":   props.poll.status === "closed",
}));

const statusBadgeClass = computed(() => ({
    "bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400":  props.poll.status === "draft",
    "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400":      props.poll.status === "pending",
    "bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400":  props.poll.status === "approved",
    "bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400":          props.poll.status === "rejected",
    "bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-400":          props.poll.status === "published",
    "bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400":         props.poll.status === "closed",
}));
</script>

<style scoped>
.action-btn {
    @apply inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors;
}
.action-btn-ghost {
    @apply bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200;
}
.action-btn-primary {
    @apply bg-sky-500 hover:bg-sky-600 text-white;
}
.action-btn-danger {
    @apply bg-red-100 hover:bg-red-200 dark:bg-red-900/40 dark:hover:bg-red-900/60 text-red-700 dark:text-red-400;
}
</style>
