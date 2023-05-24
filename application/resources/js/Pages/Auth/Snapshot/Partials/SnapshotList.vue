<template>
    <ul role="list" class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8">
        <li v-for="snapshot in snapshots" :key="snapshot.hash"
            class="overflow-hidden rounded-xl border border-gray-200  dark:border-gray-700">
            <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6 dark:bg-gray-800">
                <div class="flex flex-fow gap-3">
                    <div class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">
                        {{ snapshot.title }}
                    </div>
<!--                    <BallotStatusBadge :snapshot="snapshot"></BallotStatusBadge>-->

                </div>
                <Menu as="div" class="relative ml-auto">
                    <MenuButton class="-m-2.5 block p-2.5 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Open options</span>
                        <EllipsisHorizontalIcon class="h-5 w-5" aria-hidden="true"/>
                    </MenuButton>
                    <transition enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95">
                        <MenuItems
                            class="absolute right-0 z-10 mt-0.5 w-32 origin-top-right rounded-md bg-white dark:bg-gray-700 py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
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
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
            <dl class="-my-3 divide-y divide-gray-100 dark:divide-gray-700 px-6 py-4 text-sm leading-6 bg-gray-100 dark:bg-gray-900">
                <div class="flex justify-between gap-x-4 py-3">
                    <dt class="text-gray-600 dark:text-gray-200">Type</dt>
                    <dd class="text-gray-700 dark:text-gray-100">
                        <div class="font-medium text-gray-900 dark:text-gray-200">{{snapshot.type}}</div>
                    </dd>
                </div>

                <div class="flex justify-between gap-x-4 py-3">
                    <dt class="text-gray-600  dark:text-gray-200">Policy</dt>
                    <dd class="flex items-start gap-x-2">
                        <div class="font-medium text-gray-900 dark:text-gray-200">{{snapshot.policy_id}}</div>
                    </dd>
                </div>

                <div class="flex justify-between gap-x-4 py-3">
                    <dt class="text-gray-600 dark:text-gray-200">Created</dt>
                    <dd class="text-gray-700 dark:text-gray-100">
                        <time datetime="2020-01-07">{{snapshot.created_at}}</time>
                    </dd>
                </div>

                <div class="flex justify-between gap-x-4 py-3">
                    <dt class="text-gray-600  dark:text-gray-200">Status</dt>
                    <dd class="flex items-start gap-x-2">
                        <div class="font-medium text-gray-900 dark:text-gray-200">{{snapshot.status}}</div>
                    </dd>
                </div>
            </dl>
        </li>

        <li class="overflow-hidden rounded-xl border border-dashed border-gray-400 dark:border-gray-700 hover:border-indigo-600 py-16">
            <Link as="button" :href="route('admin.snapshots.create')" class="px-6 py-4 text-md xl:text-xl text-gray-500 dark:text-gray-400 leading-6 flex flex-col justify-center items-center w-full h-full gap-2">
                <PlusIcon class="h-6 w-6" />
                <span>Create Snapshot</span>
            </Link>
        </li>
    </ul>
</template>

<script setup lang="ts">
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue';
import {PlusIcon, EllipsisHorizontalIcon} from "@heroicons/vue/20/solid";
import {Link, useForm} from '@inertiajs/vue3';
import setAlert from "@/utils/set-alert";
import {useGlobalAlert} from "@/store/global-alert-store";
import SnapshotData = App.DataTransferObjects.SnapshotData;

const props = defineProps<{
    snapshots: SnapshotData[];
}>();

const form = useForm({
    status: 'published',
});

const alertStore = useGlobalAlert();
</script>
