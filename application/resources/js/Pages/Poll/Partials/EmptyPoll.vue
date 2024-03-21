<template>
    <div class="mx-auto max-w-xl">
        <h2 class="text-base font-semibold leading-6 text-slate-900 dark:text-slate-200">
            No published poll yet
        </h2>
        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
            Anyone can create and publish a poll to take a temperature check.
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
import { ClipboardDocumentCheckIcon, MegaphoneIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'

const items = [
    {
        name: 'Be the first to create a poll.',
        description: 'You will need to login with a Cardano wallet to create polls.',
        href: route('polls.create'),
        iconColor: 'bg-purple-500',
        icon: PencilSquareIcon,
    },
    {
        name: 'Sign a Petition!',
        description: 'Perhaps you were looking for a petition instead of a poll.',
        href: route('petitions.index'),
        iconColor: 'bg-pink-500',
        icon: ClipboardDocumentCheckIcon,
    }
]
</script>
