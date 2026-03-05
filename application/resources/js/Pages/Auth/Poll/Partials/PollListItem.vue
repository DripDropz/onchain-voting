<template>
    <div class="group relative flex flex-col bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
        <!-- Status accent bar -->
        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-xl" :class="statusBarColor" />

        <!-- Image with placeholder -->
        <div class="relative w-full h-32 overflow-hidden bg-gray-800">
            <img
                v-if="poll.image_url"
                :src="poll.image_url"
                :alt="poll.title"
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
                    {{ poll.title }}
                </Link>
                <span
                    class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                    :class="statusBadgeClass"
                >
                    {{ statusLabel }}
                </span>
            </div>

            <!-- Description preview -->
            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">
                {{ descriptionPreview }}
            </p>

            <!-- Hash -->
            <p class="text-xs font-mono text-gray-400 dark:text-gray-600">{{ poll.hash }}</p>
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

            <div class="flex items-center gap-2">
                <!-- Approve/Reject for pending polls -->
                <template v-if="poll.status === 'pending'">
                    <button
                        @click="review('approved')"
                        class="action-btn action-btn-success"
                    >
                        <CheckIcon class="w-3.5 h-3.5" />
                        Approve
                    </button>
                    <button
                        @click="review('rejected')"
                        class="action-btn action-btn-danger"
                    >
                        <XMarkIcon class="w-3.5 h-3.5" />
                        Reject
                    </button>
                </template>

                <!-- View public page -->
                <Link
                    :href="route('polls.view', { poll: poll.hash })"
                    class="action-btn action-btn-ghost"
                >
                    <EyeIcon class="w-3.5 h-3.5" />
                    View
                </Link>

                <!-- Manage poll -->
                <Link
                    :href="route('polls.manage', { poll: poll.hash })"
                    class="action-btn action-btn-primary"
                >
                    <PencilIcon class="w-3.5 h-3.5" />
                    Manage
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import MarkdownIt from 'markdown-it';
import PollData = App.DataTransferObjects.PollData;
import { Link, useForm } from "@inertiajs/vue3";
import AlertService from '@/shared/Services/alert-service';
import {
    UsersIcon,
    CalendarIcon,
    EyeIcon,
    PencilIcon,
    CheckIcon,
    XMarkIcon,
} from '@heroicons/vue/20/solid';

const md = new MarkdownIt({ html: false, breaks: true, linkify: false });

const props = defineProps<{
    poll: PollData;
}>();

const form = useForm({
    status: props?.poll?.status ?? 'draft',
});

const review = async (status: 'approved' | 'rejected') => {
    form.status = status;
    try {
        await form.put(route('admin.polls.update', { poll: props.poll?.hash }));
        props.poll.status = status;
        AlertService.show([`Poll ${status} successfully`], 'success');
    } catch (error) {
        AlertService.show([`Failed to mark poll as ${status}. Please try again.`], 'error');
    }
};

const descriptionPreview = computed(() => {
    const html = md.render(props.poll.description ?? '');
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent ?? div.innerText ?? '';
});

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const statusLabel = computed(() => {
    const labels: Record<string, string> = {
        draft:     'Draft',
        pending:   'Under Review',
        approved:  'Approved',
        rejected:  'Rejected',
        published: 'Published',
        closed:    'Closed',
    };
    return labels[props.poll.status] ?? props.poll.status;
});

const statusBarColor = computed(() => ({
    'bg-amber-400':  props.poll.status === 'draft',
    'bg-blue-400':   props.poll.status === 'pending',
    'bg-green-500':  props.poll.status === 'approved',
    'bg-red-500':    props.poll.status === 'rejected',
    'bg-sky-500':    props.poll.status === 'published',
    'bg-gray-400':   props.poll.status === 'closed',
}));

const statusBadgeClass = computed(() => ({
    'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400':  props.poll.status === 'draft',
    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400':      props.poll.status === 'pending',
    'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400':  props.poll.status === 'approved',
    'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400':          props.poll.status === 'rejected',
    'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-400':          props.poll.status === 'published',
    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400':         props.poll.status === 'closed',
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
.action-btn-success {
    @apply bg-emerald-500 hover:bg-emerald-600 text-white;
}
.action-btn-danger {
    @apply bg-red-500 hover:bg-red-600 text-white;
}
</style>
