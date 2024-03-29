<template>
    <ul role="list" class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8">
        <li v-for="snapshot in snapshots" :key="snapshot.hash"
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
    </ul>
</template>

<script setup lang="ts">
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue';
import {PlusIcon, EllipsisHorizontalIcon} from "@heroicons/vue/20/solid";
import {Link, router, useForm} from '@inertiajs/vue3';
import SnapshotData = App.DataTransferObjects.SnapshotData;
import AlertService from '@/shared/Services/alert-service';

const props = defineProps<{
    snapshots:  SnapshotData[];
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
