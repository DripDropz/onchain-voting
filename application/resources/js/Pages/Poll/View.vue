<template>
    <VoterLayout v-if="poll" page="Poll" :crumbs="crumbs" :actions="actions">
        <section class="w-full py-8">
            <div class="inner-container">
                <!-- Preview banner for owners viewing non-published polls -->
                <div
                    v-if="isOwner && poll.status !== 'published'"
                    class="mb-6 flex items-center gap-3 rounded-lg border px-4 py-3 text-sm font-medium"
                    :class="previewBannerClass"
                >
                    <span class="shrink-0 capitalize font-semibold">[{{ poll.status }}]</span>
                    <span>{{ previewBannerMessage }}</span>
                </div>

                <div v-if="poll.status === 'published' || isOwner">
                    <PollSingle
                        :poll="poll"
                        :recent-votes="recentVotes"
                    />
                </div>
                <div v-else class="flex flex-row justify-center my-8 border rounded-lg border-slate-900 dark:border-slate-700 dark:text-slate-100">
                    <p class="py-16 text-2xl font-semibold text-center dark:text-white">This poll is not yet published.</p>
                </div>
            </div>
        </section>
        <Modal :show="showCloseModal" @close="showCloseModal = false">
            <ClosePoll :poll="poll" @close="showCloseModal = false" />
        </Modal>
    </VoterLayout>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import PollData = App.DataTransferObjects.PollData;
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PollSingle from "./Partials/PollSingle.vue";
import Modal from "@/Components/Modal.vue";
import ClosePoll from "./Partials/ClosePoll.vue";

const props = defineProps<{
    poll: PollData;
    crumbs?: [];
    actions?: [];
    recentVotes?: Array<{
        hash: string;
        created_at: string;
        masked_address: string | null;
    }>;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);
const showCloseModal = ref(false);

const isOwner = computed(() => !!user.value && props.poll.user_id === user.value.id);

const previewBannerClass = computed(() => {
    const status = props.poll.status;
    if (status === 'draft')     return 'bg-amber-50 border-amber-300 text-amber-800 dark:bg-amber-900/30 dark:border-amber-600 dark:text-amber-300';
    if (status === 'pending')   return 'bg-blue-50 border-blue-300 text-blue-800 dark:bg-blue-900/30 dark:border-blue-600 dark:text-blue-300';
    if (status === 'approved')  return 'bg-green-50 border-green-300 text-green-800 dark:bg-green-900/30 dark:border-green-600 dark:text-green-300';
    if (status === 'rejected')  return 'bg-red-50 border-red-300 text-red-800 dark:bg-red-900/30 dark:border-red-600 dark:text-red-300';
    if (status === 'closed')    return 'bg-gray-50 border-gray-300 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300';
    return 'bg-gray-50 border-gray-300 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300';
});

const previewBannerMessage = computed(() => {
    const status = props.poll.status;
    if (status === 'draft')    return 'This is a draft — only you can see this preview. Submit it for review when ready.';
    if (status === 'pending')  return 'This poll is under review — only you can see this preview.';
    if (status === 'approved') return 'This poll has been approved — only you can see this preview. Publish it to make it public.';
    if (status === 'rejected') return 'This poll was rejected — only you can see this preview.';
    if (status === 'closed')   return 'This poll has been closed — voting has ended.';
    return 'Only you can see this preview.';
});
</script>
