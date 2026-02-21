<template>
    <VoterLayout page="Manage Petition" :crumbs="crumbs" :actions="actions">
        <div class="w-full">

            <!-- Status Banner -->
            <div class="w-full px-6 py-5" :class="statusBanner.bg">
                <div class="container mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-9 h-9 rounded-full" :class="statusBanner.iconBg">
                            <component :is="statusBanner.icon" class="w-5 h-5" :class="statusBanner.iconColor" />
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider" :class="statusBanner.labelColor">Status</p>
                            <p class="text-sm font-semibold" :class="statusBanner.textColor">{{ statusBanner.message }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <template v-if="petition.status === 'draft'">
                            <Link
                                :href="route('petitions.create.stepOne', { petition: petition.hash })"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium border"
                                :class="statusBanner.secondaryBtn"
                            >
                                <PencilIcon class="w-4 h-4" />
                                Edit
                            </Link>
                            <button
                                @click.prevent="submitForReview()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold"
                                :class="statusBanner.primaryBtn"
                            >
                                <PaperAirplaneIcon class="w-4 h-4" />
                                Submit for Review
                            </button>
                        </template>

                        <template v-else-if="petition.status === 'approved'">
                            <button
                                @click.prevent="publishPetition()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold"
                                :class="statusBanner.primaryBtn"
                            >
                                <RocketLaunchIcon class="w-4 h-4" />
                                Publish Petition
                            </button>
                        </template>

                        <template v-else-if="petition.status === 'rejected'">
                            <Link
                                :href="route('petitions.create.stepOne', { petition: petition.hash })"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold"
                                :class="statusBanner.primaryBtn"
                            >
                                <PencilIcon class="w-4 h-4" />
                                Edit &amp; Resubmit
                            </Link>
                        </template>

                        <template v-else-if="petition.status === 'published'">
                            <Link
                                :href="route('petitions.view', { petition: petition.hash })"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold"
                                :class="statusBanner.primaryBtn"
                            >
                                <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                                View Public Page
                            </Link>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Petition title -->
            <div class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-6 py-4">
                <div class="container mx-auto">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ petition.title }}</h1>
                </div>
            </div>

            <!-- Main content -->
            <div class="px-6 py-10 bg-gray-50 dark:bg-gray-950 min-h-screen">
                <div class="container mx-auto flex flex-col gap-8">

                    <!-- Profile Overview -->
                    <section>
                        <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">
                            Profile Overview
                        </h2>
                        <div class="grid gap-6 lg:grid-cols-2">
                            <PetitionSupporters :petition="petition" />
                            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                                <p class="font-semibold text-gray-800 dark:text-white mb-3">Petition Goals</p>
                                <PetitionGoals :petition="petition" />
                            </div>
                        </div>
                    </section>

                    <!-- Grow Petition + Criteria -->
                    <section class="grid gap-6 lg:grid-cols-2">

                        <!-- Share -->
                        <div>
                            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">
                                Grow Your Petition
                            </h2>
                            <div class="flex flex-col gap-5 p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-white">Share Petition</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Share your petition to spread awareness of the cause.
                                    </p>
                                </div>

                                <!-- Copy link -->
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center flex-1 gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-600 dark:text-gray-400 min-w-0">
                                        <LinkIcon class="w-4 h-4 shrink-0 text-gray-400" />
                                        <span class="truncate">{{ link }}</span>
                                    </div>
                                    <button
                                        @click="copy(link)"
                                        class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    >
                                        <ClipboardIcon class="w-4 h-4" />
                                        Copy
                                    </button>
                                </div>

                                <!-- Share page link -->
                                <Link :href="`share`">
                                    <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors">
                                        <ShareIcon class="w-4 h-4" />
                                        Share on Social Media
                                    </button>
                                </Link>
                            </div>
                        </div>

                        <!-- Criteria -->
                        <div>
                            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">
                                Petition Criteria
                            </h2>
                            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                                <Criteria :model="petition" />
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <Modal :show="showModal">
                <ClosePetition :petition="petition" @close="showModal = false" />
            </Modal>
            <Modal :show="showPublishModal" :modalType="'publish'">
                <PublishPetition :petition="petition" @close="showPublishModal = false" />
            </Modal>
        </div>
    </VoterLayout>
</template>

<script lang="ts" setup>
import { computed } from "vue";
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import { Link, useForm } from "@inertiajs/vue3";
import {
    PencilIcon,
    PaperAirplaneIcon,
    RocketLaunchIcon,
    ArrowTopRightOnSquareIcon,
    LinkIcon,
    ClipboardIcon,
    ShareIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    XCircleIcon,
    CheckBadgeIcon,
} from "@heroicons/vue/20/solid";
import AlertService from "@/shared/Services/alert-service";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Criteria from "@/shared/components/Criteria.vue";
import Modal from "@/Components/Modal.vue";
import ClosePetition from "./Partials/ClosePetition.vue";
import PublishPetition from "./Partials/PublishPetition.vue";
import PetitionGoals from "./Partials/PetitionGoals.vue";
import PetitionSupporters from "@/Pages/Petition/Partials/PetitionSupporters.vue";
import { useConfigStore } from "@/stores/config-store";
import { storeToRefs } from "pinia";

const props = defineProps<{
    petition: PetitionData;
    crumbs: [];
    actions: [];
}>();

let configStore = useConfigStore();
let { showModal, showPublishModal } = storeToRefs(configStore);

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

const submitForReview = async () => {
    try {
        await form.patch(route("petitions.submit", { petition: props.petition?.hash }));
        props.petition.status = "pending";
        AlertService.show(["Petition submitted for admin review"], "success");
    } catch (error) {
        AlertService.show(["There was an error submitting the petition"], "error");
    }
};

const copy = (text: string) => {
    try {
        navigator.clipboard.writeText(text);
        AlertService.show(["Link copied to clipboard"], "success");
    } catch {
        AlertService.show(["Could not copy link"], "error");
    }
};

const link = route("petitions.view", { petition: props.petition.hash });

const statusBanner = computed(() => {
    const s = props.petition.status;
    const configs: Record<string, any> = {
        draft: {
            bg:          "bg-amber-50 dark:bg-amber-950/40 border-b border-amber-200 dark:border-amber-800",
            iconBg:      "bg-amber-100 dark:bg-amber-900/60",
            icon:        ClockIcon,
            iconColor:   "text-amber-600 dark:text-amber-400",
            labelColor:  "text-amber-600 dark:text-amber-400",
            textColor:   "text-amber-800 dark:text-amber-200",
            message:     "Saved as a draft — submit for admin review when ready.",
            primaryBtn:  "bg-amber-500 hover:bg-amber-600 text-white",
            secondaryBtn:"border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/40",
        },
        pending: {
            bg:          "bg-blue-50 dark:bg-blue-950/40 border-b border-blue-200 dark:border-blue-800",
            iconBg:      "bg-blue-100 dark:bg-blue-900/60",
            icon:        ClockIcon,
            iconColor:   "text-blue-600 dark:text-blue-400",
            labelColor:  "text-blue-600 dark:text-blue-400",
            textColor:   "text-blue-800 dark:text-blue-200",
            message:     "Awaiting admin review — we'll notify you once a decision is made.",
            primaryBtn:  "bg-blue-500 hover:bg-blue-600 text-white",
            secondaryBtn:"border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300",
        },
        approved: {
            bg:          "bg-green-50 dark:bg-green-950/40 border-b border-green-200 dark:border-green-800",
            iconBg:      "bg-green-100 dark:bg-green-900/60",
            icon:        CheckCircleIcon,
            iconColor:   "text-green-600 dark:text-green-400",
            labelColor:  "text-green-600 dark:text-green-400",
            textColor:   "text-green-800 dark:text-green-200",
            message:     "Your petition has been approved — publish it to start collecting signatures.",
            primaryBtn:  "bg-green-500 hover:bg-green-600 text-white",
            secondaryBtn:"border-green-300 dark:border-green-700 text-green-700 dark:text-green-300",
        },
        rejected: {
            bg:          "bg-red-50 dark:bg-red-950/40 border-b border-red-200 dark:border-red-800",
            iconBg:      "bg-red-100 dark:bg-red-900/60",
            icon:        XCircleIcon,
            iconColor:   "text-red-600 dark:text-red-400",
            labelColor:  "text-red-600 dark:text-red-400",
            textColor:   "text-red-800 dark:text-red-200",
            message:     "Your petition was not approved. Edit it and resubmit for another review.",
            primaryBtn:  "bg-red-500 hover:bg-red-600 text-white",
            secondaryBtn:"border-red-300 dark:border-red-700 text-red-700 dark:text-red-300",
        },
        published: {
            bg:          "bg-sky-50 dark:bg-sky-950/40 border-b border-sky-200 dark:border-sky-800",
            iconBg:      "bg-sky-100 dark:bg-sky-900/60",
            icon:        CheckBadgeIcon,
            iconColor:   "text-sky-600 dark:text-sky-400",
            labelColor:  "text-sky-600 dark:text-sky-400",
            textColor:   "text-sky-800 dark:text-sky-200",
            message:     "Your petition is live and collecting signatures.",
            primaryBtn:  "bg-sky-500 hover:bg-sky-600 text-white",
            secondaryBtn:"border-sky-300 dark:border-sky-700 text-sky-700 dark:text-sky-300",
        },
    };
    return configs[s] ?? configs.draft;
});
</script>
