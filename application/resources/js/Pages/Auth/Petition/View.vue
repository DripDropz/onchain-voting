<template>
    <Head title="Petition" />

    <AdminLayout>
        <template #header>
            <Nav :crumbs="props.crumbs" />
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

                <!-- Header card -->
                <div class="p-6 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold leading-tight xl:text-3xl">{{ petition.title }}</h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                By <span class="font-medium text-gray-700 dark:text-gray-300">{{ petition.user?.name }}</span>
                                &mdash; created {{ formatDate(petition.created_at) }}
                            </p>
                        </div>
                        <div class="mt-3 sm:mt-0">
                            <span :class="statusBadgeClass" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize">
                                {{ petition.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6 lg:flex-row">
                    <!-- Left column: description + image -->
                    <div class="flex flex-col flex-1 gap-6">
                        <div class="p-6 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white">
                            <h2 class="mb-4 text-lg font-semibold">Petition Description</h2>
                            <div v-if="petition.image_url" class="mb-6">
                                <img :src="petition.image_url" alt="Petition image" class="object-cover w-full rounded-lg max-h-64" />
                            </div>
                            <div class="prose dark:prose-invert max-w-none" v-html="parseMarkdown(petition.description ?? '')"></div>
                        </div>
                    </div>

                    <!-- Right column: stats + criteria -->
                    <div class="flex flex-col gap-6 lg:w-80">
                        <!-- Signature count -->
                        <div class="p-6 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white">
                            <h2 class="mb-2 text-lg font-semibold">Signatures</h2>
                            <p class="text-4xl font-bold text-sky-500">{{ petition.signatures_count ?? 0 }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">total signatures collected</p>
                        </div>

                        <!-- Petition criteria / goals -->
                        <div class="p-6 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white">
                            <h2 class="mb-4 text-lg font-semibold">Petition Criteria</h2>
                            <Criteria :model="petition" />
                        </div>

                        <!-- Admin actions -->
                        <div class="p-6 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white" v-if="!petition.ballot">
                            <h2 class="mb-4 text-lg font-semibold">Admin Actions</h2>
                            <div class="flex flex-col gap-3">
                                <Link :href="route('admin.petitions.edit', { petition: petition.hash })"
                                    class="inline-flex items-center justify-center gap-x-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 text-sm">
                                    Edit Petition
                                </Link>
                                <button
                                    v-if="petition.status !== 'approved' && petition.status !== 'published'"
                                    @click="approve"
                                    class="inline-flex items-center justify-center gap-x-2 rounded-md bg-sky-500 px-4 py-2.5 font-semibold text-white shadow-sm hover:bg-sky-600 text-sm">
                                    Approve Petition
                                </button>
                                <button
                                    v-if="petition.status !== 'rejected' && petition.status !== 'published'"
                                    @click="reject"
                                    class="inline-flex items-center justify-center gap-x-2 rounded-md border border-red-400 bg-white dark:bg-gray-800 px-4 py-2.5 font-semibold text-red-500 shadow-sm hover:bg-red-50 dark:hover:bg-red-950 text-sm">
                                    Reject Petition
                                </button>
                                <Link
                                    v-if="petition.status === 'approved' && moveToBallot"
                                    :href="route('admin.petitions.toBallot', { petition: petition.hash })"
                                    class="inline-flex items-center justify-center gap-x-2 rounded-md bg-emerald-500 px-4 py-2.5 font-semibold text-white shadow-sm hover:bg-emerald-600 text-sm">
                                    Move to Ballot
                                </Link>
                            </div>
                        </div>

                        <div class="p-6 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg dark:text-white" v-else>
                            <h2 class="mb-4 text-lg font-semibold">Ballot</h2>
                            <p class="mb-3 text-sm text-gray-500 dark:text-gray-400">This petition has been moved to a ballot.</p>
                            <Link :href="route('ballot.view', { ballot: petition.ballot.hash })"
                                class="inline-flex items-center justify-center gap-x-2 rounded-md bg-sky-500 px-4 py-2.5 font-semibold text-white shadow-sm hover:bg-sky-600 text-sm">
                                View Ballot
                                <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>

<script lang="ts" setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Nav from '../Breadcrumbs.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ArrowTopRightOnSquareIcon } from '@heroicons/vue/20/solid';
import MarkdownIt from 'markdown-it';
import moment from 'moment-timezone';
import Criteria from '@/shared/components/Criteria.vue';
import AlertService from '@/shared/Services/alert-service';
import compareValues from '@/utils/compare-values';
import PetitionData = App.DataTransferObjects.PetitionData;

const props = defineProps<{
    petition: PetitionData;
    crumbs: []
}>();

const form = useForm({
    status: props.petition.status,
});

const md = new MarkdownIt();
const parseMarkdown = (content: string): string => md.render(content);

const formatDate = (dateString?: string | null): string => {
    if (!dateString) return '';
    return moment(dateString).format('Do MMMM YYYY');
};

const moveToBallot = computed(() => {
    const minSigs = props.petition.petition_goals?.['ballot-eligible']?.['value2'];
    const operator = props.petition.petition_goals?.['ballot-eligible']?.['operator'];
    if (!minSigs || !operator) return false;
    return compareValues(props.petition.signatures_count, minSigs, operator);
});

const statusBadgeClass = computed(() => {
    const map: Record<string, string> = {
        draft: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        pending: 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
        approved: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        rejected: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        published: 'bg-sky-100 text-sky-700 dark:bg-sky-900 dark:text-sky-300',
    };
    return map[props.petition.status] ?? 'bg-gray-100 text-gray-700';
});

const approve = () => {
    form.status = 'approved';
    form.patch(route('admin.petitions.update', { petition: props.petition.hash }), {
        onSuccess: () => AlertService.show(['Petition approved successfully'], 'success'),
        onError: () => AlertService.show(['Failed to approve petition'], 'error'),
    });
};

const reject = () => {
    form.status = 'rejected';
    form.patch(route('admin.petitions.update', { petition: props.petition.hash }), {
        onSuccess: () => AlertService.show(['Petition rejected'], 'success'),
        onError: () => AlertService.show(['Failed to reject petition'], 'error'),
    });
};
</script>
