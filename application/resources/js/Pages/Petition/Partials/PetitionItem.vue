<template>
    <div
        v-if="!deleted"
        class="group relative flex flex-col bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden"
    >
        <!-- Status accent bar -->
        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-xl" :class="statusBarColor" />

        <div class="flex flex-col flex-1 pl-5 pr-5 pt-5 pb-4 gap-3">
            <!-- Header row: title + status badge -->
            <div class="flex items-start justify-between gap-4">
                <Link
                    :href="route('petitions.view', { petition: petition.hash })"
                    class="text-lg font-semibold text-gray-900 dark:text-white leading-snug hover:text-sky-500 dark:hover:text-sky-400 transition-colors line-clamp-2"
                >
                    {{ petition.title }}
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
                <span class="flex-1 text-red-700 dark:text-red-300 font-medium">Delete this petition?</span>
                <button
                    @click.prevent="deletePetition"
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
                    {{ petition.signatures_count ?? 0 }}
                    <span class="hidden sm:inline">signatures</span>
                </span>
                <span class="flex items-center gap-1">
                    <CalendarIcon class="w-3.5 h-3.5" />
                    {{ formatDate(petition.created_at) }}
                </span>
            </div>

            <!-- Status-specific actions (owner only) -->
            <div v-if="isOwner" class="flex items-center gap-2">

                <!-- DRAFT: Manage, Edit, Delete -->
                <template v-if="petition.status === 'draft'">
                    <Link
                        :href="route('petitions.manage', { petition: petition.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <Cog6ToothIcon class="w-3.5 h-3.5" />
                        Manage
                    </Link>
                    <Link
                        :href="route('petitions.create.stepOne', { petition: petition.hash })"
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
                </template>

                <!-- PENDING: Manage + Under Review badge -->
                <template v-else-if="petition.status === 'pending'">
                    <Link
                        :href="route('petitions.manage', { petition: petition.hash })"
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

                <!-- APPROVED: Manage, Revert to Draft, Publish -->
                <template v-else-if="petition.status === 'approved'">
                    <Link
                        :href="route('petitions.manage', { petition: petition.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <Cog6ToothIcon class="w-3.5 h-3.5" />
                        Manage
                    </Link>
                    <button
                        @click.prevent="revertToDraft"
                        :disabled="revertForm.processing"
                        class="action-btn action-btn-ghost"
                    >
                        <ArrowUturnLeftIcon class="w-3.5 h-3.5" />
                        Revert to Draft
                    </button>
                    <button
                        @click.prevent="showPublishModal = true"
                        class="action-btn action-btn-primary"
                    >
                        <RocketLaunchIcon class="w-3.5 h-3.5" />
                        Publish
                    </button>
                </template>

                <!-- REJECTED: Manage, Edit & Resubmit -->
                <template v-else-if="petition.status === 'rejected'">
                    <Link
                        :href="route('petitions.manage', { petition: petition.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <Cog6ToothIcon class="w-3.5 h-3.5" />
                        Manage
                    </Link>
                    <Link
                        :href="route('petitions.create.stepOne', { petition: petition.hash })"
                        class="action-btn action-btn-primary"
                    >
                        <PencilIcon class="w-3.5 h-3.5" />
                        Edit &amp; Resubmit
                    </Link>
                </template>

                <!-- PUBLISHED: Manage + View public page -->
                <template v-else-if="petition.status === 'published'">
                    <Link
                        :href="route('petitions.manage', { petition: petition.hash })"
                        class="action-btn action-btn-ghost"
                    >
                        <Cog6ToothIcon class="w-3.5 h-3.5" />
                        Manage
                    </Link>
                    <Link
                        :href="route('petitions.view', { petition: petition.hash })"
                        class="action-btn action-btn-primary"
                    >
                        <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                        View
                    </Link>
                </template>

            </div>

            <!-- Non-owner: just show View for published petitions -->
            <div v-else-if="petition.status === 'published'" class="flex items-center gap-2">
                <Link
                    :href="route('petitions.view', { petition: petition.hash })"
                    class="action-btn action-btn-primary"
                >
                    <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                    View
                </Link>
            </div>
        </div>

        <Modal :show="showPublishModal" :modalType="'publish'">
            <PublishPetition :petition="petition" @close="showPublishModal = false" />
        </Modal>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import MarkdownIt from "markdown-it";
import PetitionData = App.DataTransferObjects.PetitionData;

const md = new MarkdownIt({ html: false, breaks: true, linkify: false });
import Modal from "@/Components/Modal.vue";
import PublishPetition from "./PublishPetition.vue";
import {
    UsersIcon,
    CalendarIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
    RocketLaunchIcon,
    ArrowTopRightOnSquareIcon,
    ArrowUturnLeftIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    Cog6ToothIcon,
} from "@heroicons/vue/20/solid";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import AlertService from "@/shared/Services/alert-service";
import { usePetitionStore } from "@/stores/petition-store";

const props = defineProps<{
    petition: PetitionData;
}>();

const descriptionPreview = computed(() => {
    const html = md.render(props.petition.description ?? "");
    const div = document.createElement("div");
    div.innerHTML = html;
    return div.textContent ?? div.innerText ?? "";
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isOwner = computed(() => !!user.value && props.petition?.user_id === user.value.id);

const petitionStore = usePetitionStore();

const showPublishModal = ref(false);
const confirmingDelete = ref(false);
const deleted = ref(false);

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString(undefined, {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};

const deleteForm = useForm({});
const revertForm = useForm({});

const deletePetition = () => {
    deleteForm.delete(route("petitions.destroy", { petition: props.petition.hash }), {
        onSuccess: () => {
            deleted.value = true;
            petitionStore.removePetition?.(props.petition.hash);
            AlertService.show(["Petition deleted."], "success");
        },
        onError: () => {
            AlertService.show(["Could not delete the petition."], "error");
            confirmingDelete.value = false;
        },
    });
};

const revertToDraft = () => {
    revertForm.patch(route("petitions.revert", { petition: props.petition.hash }), {
        onSuccess: () => {
            AlertService.show(["Petition reverted to draft."], "success");
        },
        onError: () => {
            AlertService.show(["Could not revert the petition."], "error");
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
    return labels[props.petition.status] ?? props.petition.status;
});

const statusBarColor = computed(() => ({
    "bg-amber-400":  props.petition.status === "draft",
    "bg-blue-400":   props.petition.status === "pending",
    "bg-green-500":  props.petition.status === "approved",
    "bg-red-500":    props.petition.status === "rejected",
    "bg-sky-500":    props.petition.status === "published",
    "bg-gray-400":   props.petition.status === "closed",
}));

const statusBadgeClass = computed(() => ({
    "bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400":  props.petition.status === "draft",
    "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400":      props.petition.status === "pending",
    "bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400":  props.petition.status === "approved",
    "bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400":          props.petition.status === "rejected",
    "bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-400":          props.petition.status === "published",
    "bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400":         props.petition.status === "closed",
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
