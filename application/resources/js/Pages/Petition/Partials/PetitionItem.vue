<template>
    <div
        class="group relative flex flex-col bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden"
        :class="statusAccent"
    >
        <!-- Status accent bar -->
        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-xl" :class="statusBarColor" />

        <div class="flex flex-col flex-1 pl-5 pr-5 pt-5 pb-4 gap-3">
            <!-- Header row: title + status badge -->
            <div class="flex items-start justify-between gap-4">
                <Link
                    :href="petition?.user_id === user?.id && petition.status !== 'published'
                        ? route('petitions.manage', { petition: petition.hash })
                        : route('petitions.view', { petition: petition.hash })"
                    class="text-lg font-semibold text-gray-900 dark:text-white leading-snug hover:text-sky-500 dark:hover:text-sky-400 transition-colors line-clamp-2"
                >
                    {{ petition.title }}
                </Link>
                <span
                    class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                    :class="statusBadgeClass"
                >
                    {{ petition.status }}
                </span>
            </div>

            <!-- Description -->
            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">
                {{ petition.description }}
            </p>
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

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <Link
                    v-if="petition?.user_id === user?.id"
                    :href="route('petitions.manage', { petition: petition.hash })"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition-colors"
                >
                    <Cog6ToothIcon class="w-3.5 h-3.5" />
                    Manage
                </Link>

                <button
                    v-if="petition.user_id === user?.id && petition.status === 'approved'"
                    @click.prevent="publishPetition()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-sky-500 hover:bg-sky-600 text-white transition-colors"
                >
                    <RocketLaunchIcon class="w-3.5 h-3.5" />
                    Publish
                </button>

                <Link
                    v-else-if="petition.status === 'published'"
                    :href="route('petitions.view', { petition: petition.hash })"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-500 hover:bg-green-600 text-white transition-colors"
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
import { computed } from "vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import Modal from "@/Components/Modal.vue";
import PublishPetition from "./PublishPetition.vue";
import {
    UsersIcon,
    CalendarIcon,
    Cog6ToothIcon,
    RocketLaunchIcon,
    ArrowTopRightOnSquareIcon,
} from "@heroicons/vue/20/solid";
import { Link, useForm } from "@inertiajs/vue3";
import { useConfigStore } from "@/stores/config-store";
import { storeToRefs } from "pinia";
import AlertService from "@/shared/Services/alert-service";

const props = defineProps<{
    petition: PetitionData;
}>();

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString(undefined, {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};

let configStore = useConfigStore();
let { user, showPublishModal } = storeToRefs(configStore);

const form = useForm({
    status: props?.petition?.status,
});

const emit = defineEmits<{ (e: "close"): void }>();

const publishPetition = async () => {
    try {
        await form.put(route("petitions.publish", { petition: props.petition?.hash }));
        props.petition.status = "published";
        AlertService.show(["Petition has been published"], "success");
        emit("close");
    } catch (error) {
        AlertService.show(["There was an error publishing the petition"], "error");
    }
};

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

const statusAccent = computed(() => "");
</script>
