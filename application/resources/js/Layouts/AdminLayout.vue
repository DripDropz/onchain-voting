<script setup lang="ts">
import {ref} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import GlobalAlertComponent from '../shared/components/GlobalAlertComponent.vue';
import DarkModeButton from '@/shared/components/DarkModeButton.vue';
import {Link} from '@inertiajs/vue3';
import {Modal} from 'momentum-modal';
import {useConfigStore} from "@/stores/config-store";
import {storeToRefs} from 'pinia';

const showingNavigationDropdown = ref(false);
let configStore = useConfigStore();
let {isDarkMode} = storeToRefs(configStore);
</script>

<template>
    <div :class="{'dark': isDarkMode }" v-if="!!$page.props?.auth?.user">
        <div class="min-h-screen text-slate-100 bg-slate-950 dark:bg-slate-950">
            <nav class="border-b bg-slate-900/95 border-slate-700 dark:bg-slate-900/95 dark:border-slate-700">
                <div class="border-b bg-rose-700 border-rose-500/70">
                    <div class="px-4 py-1 mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <p class="text-xs font-semibold tracking-widest text-center uppercase text-rose-50 sm:text-left">
                            {{ $page.props.adminContext?.label ?? 'Admin Console' }}
                        </p>
                    </div>
                </div>
                <!-- Primary Navigation Menu -->
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex items-center shrink-0">
                                <Link :href="route('home')">
                                    <ApplicationLogo
                                        class="block w-auto text-gray-800 fill-current h-9 dark:text-gray-200"/>
                                </Link>
                                <span class="hidden px-2 py-1 ml-3 text-xs font-semibold tracking-wide uppercase border rounded-md sm:inline-flex bg-rose-800/80 text-rose-100 border-rose-500/80">
                                    Admin
                                </span>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-6 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('admin.ballots.index')" :active="route().current('admin.ballots.index')">
                                    Ballots
                                </NavLink>
                                <NavLink :href="route('admin.petitions.index')" :active="route().current('admin.petitions.index')">
                                    Petitions
                                </NavLink>
                                <NavLink :href="route('admin.polls.index')" :active="route().current('admin.polls.index')">
                                    Polls
                                </NavLink>
                                <NavLink :href="route('admin.snapshots.index')" :active="route().current('admin.snapshots.index')">
                                    Snapshots
                                </NavLink>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <!-- Settings Dropdown -->
                                <div class="relative ml-3">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out border rounded-md text-slate-200 border-slate-600 bg-slate-800 hover:text-white hover:border-slate-500 focus:outline-none">
                                                    {{ $page.props.auth.user.name }}

                                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                              clip-rule="evenodd"/>
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <DropdownLink :href="route('admin.profile.edit')"> Profile</DropdownLink>
                                            <DropdownLink :href="route('admin.logout')" method="post" as="button">
                                                Log Out
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                            <div>
                                <DarkModeButton/>
                            </div>
                        </div>
                        <!-- Hamburger -->
                        <div class="flex items-center -mr-2 sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="inline-flex items-center justify-center p-2 transition duration-150 ease-in-out rounded-md text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none focus:bg-slate-800 focus:text-white">
                                <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6h16M4 12h16M4 18h16"/>
                                    <path :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                     class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <div class="px-4 pb-2 mb-1">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold tracking-widest uppercase border rounded-md bg-rose-800/80 text-rose-100 border-rose-500/80">
                                {{ $page.props.adminContext?.label ?? 'Admin Console' }}
                            </span>
                        </div>
                        <ResponsiveNavLink :href="route('admin.dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-slate-700 dark:border-slate-700">
                        <div class="px-4">
                            <div class="text-base font-medium text-slate-100 dark:text-slate-100">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-slate-400">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('admin.profile.edit')"> Profile</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="border-b shadow bg-slate-900 border-slate-700 dark:bg-slate-900 dark:border-slate-700" v-if="$slots.header">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <slot name="header"/>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="fixed top-0 left-0 z-50 flex items-end justify-end w-full h-full pointer-events-none">
                    <GlobalAlertComponent/>
                </div>

                <slot/>

                <div class="z-40">
                    <Modal/>
                </div>
            </main>
        </div>
    </div>
</template>
