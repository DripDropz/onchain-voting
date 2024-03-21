<template>
    <div class="mx-auto max-w-xl">
        <h2 class="text-base font-semibold leading-6 text-slate-900 dark:text-slate-200">
            No visible or featured petitions yet
        </h2>
        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
            For a petition to show in this list it must meet at least one signature threshold.
            If you don't see the petition you are looking for, reach out the project or group for a direct link.
        </p>

        <ul role="list" class="mt-6 divide-y divide-gray-200 border-b border-t border-gray-200">
            <li v-for="(item, itemIdx) in items" :key="itemIdx">
                <div class="group relative flex items-start space-x-3 py-4">
                    <div class="flex-shrink-0">
                        <span :class="[item.iconColor, 'inline-flex h-10 w-10 items-center justify-center rounded-lg']">
                          <component :is="item.icon" class="h-6 w-6 text-white" aria-hidden="true" />
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-medium text-slate-900 dark:text-slate-200">
                            <a :href="item.href">
                                <span class="absolute inset-0" aria-hidden="true" />
                                {{ item.name }}
                            </a>
                        </div>
                        <p class="text-sm text-slate-700 dark:text-slate-300">
                            {{ item.description }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 self-center">
                        <ChevronRightIcon class="h-5 w-5 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                    </div>
                </div>
            </li>
        </ul>
        <div class="mt-6 flex">
            <a :href="route('ballots.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Or check open ballots to see if petition was moved to a ballot.
                <span aria-hidden="true"> &rarr;</span>
            </a>
        </div>
    </div>
</template>

<script setup>
import {ChevronRightIcon} from '@heroicons/vue/20/solid'
import { CalendarIcon, CommandLineIcon, MegaphoneIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'

const items = [
    {
        name: 'Log in and Create a Petition',
        description: 'Admin will need to approve before petition can be published.',
        href: route('petitions.create'),
        iconColor: 'bg-pink-500',
        icon: PencilSquareIcon,
    },
    {
        name: 'Sign a Poll!',
        description: 'Perhaps you were looking for a poll instead of a petition.',
        href: route('polls.index'),
        iconColor: 'bg-purple-500',
        icon: MegaphoneIcon,
    },
    // {
    //     name: 'Event',
    //     description: 'Like a conference all about you that no one will care about.',
    //     href: '#',
    //     iconColor: 'bg-yellow-500',
    //     icon: CalendarIcon,
    // },
]
</script>
