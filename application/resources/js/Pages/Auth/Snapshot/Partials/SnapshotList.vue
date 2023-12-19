<template>
    <ul role="list" class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8">
        <li v-for="snapshot in snapshots.data" :key="snapshot.hash"
            class="overflow-hidden border border-gray-200 rounded-xl dark:border-gray-700">
            <div class="flex items-center p-6 border-b gap-x-4 border-gray-900/5 bg-gray-50 dark:bg-gray-800">
                <div class="flex gap-3 flex-fow">
                    <div class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">
                        {{ snapshot.title }}
                    </div>
<!--                    <BallotStatusBadge :snapshot="snapshot"></BallotStatusBadge>-->

                </div>
                <Menu as="div" class="relative ml-auto">
                    <MenuButton class="-m-2.5 block p-2.5 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Open options</span>
                        <EllipsisHorizontalIcon class="w-5 h-5" aria-hidden="true"/>
                    </MenuButton>
                    <transition enter-active-class="transition duration-100 ease-out"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition duration-75 ease-in"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0">
                        <MenuItems
                            class="absolute right-0 z-10 mt-0.5 w-32 origin-top-right rounded-md bg-sky-100 dark:bg-gray-700 py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                            <MenuItem v-slot="{ active }">
                                <Link :href="route('admin.snapshots.view', snapshot.hash)"
                                      :class="[active ? 'bg-gray-50 dark:bg-gray-900' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300']"
                                >View<span class="sr-only">, {{ snapshot.title }}</span></Link>
                            </MenuItem>
                            <MenuItem v-if="!snapshot.live" v-slot="{ active }">
                                <Link :href="route('admin.snapshots.edit', snapshot.hash)"
                                      :class="[active ? 'bg-gray-50 dark:bg-gray-900' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300']">
                                    Edit<span class="sr-only">, {{ snapshot.title }}</span>
                                </Link>
                            </MenuItem>
                            <MenuItem v-if="!snapshot.live" v-slot="{ active }">
                                <div :class="{ 'cursor-not-allowed' : snapshot.has_voting_powers}">
                                    <Link :href="route('admin.snapshots.powers.csv.upload', snapshot.hash)"
                                          :class="[active && !snapshot.has_voting_powers ? 'bg-gray-50 dark:bg-gray-900' : 'pointer-events-none', 'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300']">
                                        Import voting power snapshot (CSV)<span class="sr-only">, {{ snapshot.title }}</span>
                                    </Link>
                                </div>
                            </MenuItem>
                            <MenuItem v-if="!snapshot.live && !snapshot.ballot" v-slot="{ active }">
                                <a @click.prevent="deleteSnapshot(String(snapshot.hash))"
                                    :class="[active ? 'bg-red-50 dark:bg-red-900 cursor-pointer' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300']">
                                    Delete<span class="sr-only">, {{ snapshot.title }}</span>
                                </a>
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
            <dl class="px-6 py-4 -my-3 text-sm leading-6 bg-gray-100 divide-y divide-gray-100 dark:divide-gray-700 dark:bg-gray-900">
                <div class="flex justify-between py-3 gap-x-4">
                    <dt class="text-gray-600 dark:text-gray-200">Type</dt>
                    <dd class="text-gray-700 dark:text-gray-100">
                        <div class="font-medium text-gray-900 dark:text-gray-200">{{snapshot.type}}</div>
                    </dd>
                </div>

                <div class="flex justify-between py-3 gap-x-4">
                    <dt class="text-gray-600 dark:text-gray-200">Policy</dt>
                    <dd class="flex items-start gap-x-2">
                        <div class="font-medium text-gray-900 dark:text-gray-200">{{snapshot.policy_id}}</div>
                    </dd>
                </div>

                <div class="flex justify-between py-3 gap-x-4">
                    <dt class="text-gray-600 dark:text-gray-200">Created</dt>
                    <dd class="text-gray-700 dark:text-gray-100">
                        <time datetime="2020-01-07">{{snapshot.created_at}}</time>
                    </dd>
                </div>

                <div class="flex justify-between py-3 gap-x-4">
                    <dt class="text-gray-600 dark:text-gray-200">Status</dt>
                    <dd class="flex items-start gap-x-2">
                        <div class="font-medium text-gray-900 dark:text-gray-200">{{snapshot.status}}</div>
                    </dd>
                </div>
            </dl>
        </li>

        <li class="py-16 overflow-hidden border border-gray-400 border-dashed rounded-xl dark:border-gray-700 hover:border-sky-600">
            <Link as="button" :href="route('admin.snapshots.create')" class="flex flex-col items-center justify-center w-full h-full gap-2 px-6 py-4 leading-6 text-gray-500 text-md xl:text-xl dark:text-gray-400">
                <PlusIcon class="w-6 h-6" />
                <span>Create Snapshot</span>
            </Link>
        </li>
    </ul>
    <slot name="footer">
        <div class="flex flex-row items-center justify-between w-full pt-4">
            <div class="border-2 border-sky-600">
                <p class="p-4 text-sm text-gray-900 dark:text-gray-300">
                    {{ `Showing ${props.snapshots.meta.from} to ${(props.snapshots.meta.to < props.snapshots.meta.total) ?
                        props.snapshots.meta.to : props.snapshots.meta.total} of ${props.snapshots.meta.total} results` }} </p>
            </div>
            <Paginator :pagination="snapshots.meta" @paginated="(payload: number) => emit('current-page', payload)"
                @perPageUpdated="(payload: number) => emit('perr-page', payload)">
            </Paginator>
        </div>
    </slot>
</template>

<script setup lang="ts">
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue';
import {PlusIcon, EllipsisHorizontalIcon} from "@heroicons/vue/20/solid";
import {Link, router, useForm} from '@inertiajs/vue3';
import SnapshotData = App.DataTransferObjects.SnapshotData;
import AlertService from '@/shared/Services/alert-service';
import Pagination from "@/types/pagination";
import Paginator from "@/shared/components/Paginator.vue";

const props = defineProps<{
    snapshots: {
        data: SnapshotData[];
        links: [];
        meta: Pagination;

    };
}>();

const emit = defineEmits<{
    (e: 'current-page', page: number): void
    (e: 'perr-page', perPage: number): void
}>();

const form = useForm({
    status: 'published',
});

const deleteSnapshot = (snapshotHash: string) => {
    form.delete(route('admin.snapshots.destroy', {snapshot: snapshotHash}), {
        preserveScroll: true,
        onSuccess: () => {
            AlertService.show(['Snapshot Deleted successfully'], 'success');
        },
        onError: (errors) => {
            Object.entries(errors).forEach(([key, value]) => {
                AlertService.show([value], 'info');
            });
        },
        onFinish: () => setTimeout(() => {
            form.reset()
            window.location.reload()
        }, 1000 * 2)
    });
};
</script>
