<template>
    <div class="top-0 z-30 lg:border-teal-light-300">
        <div class='relative'>
            <div class="flex flex-row justify-between gap-4 flex-nowrap">
                <nav class="flex overflow-x-auto " aria-label="Breadcrumb">
                    <ol role="list" class="flex space-x-0">
                        <li class="flex">
                            <div class="flex items-center">
                                <Link :href="route('home')" :class="{ 'text-sky-500': $page.url === route('home') }"
                                      class="flex flex-row items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                    Home
                                </Link>
                            </div>
                        </li>

                        <li v-show="(crumbsLength >= 1)"
                            v-for="(crumb, key) in crumbs"
                            class="flex">
                            <div v-if="!(crumbsLength-1 == key)" class="flex items-center">
                                <svg class="flex-shrink-0 w-6 h-full text-gray-200 dark:text-gray-700" viewBox="0 0 24 44"
                                     preserveAspectRatio="none" fill="currentColor"
                                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z"/>
                                </svg>
                                <a :href="crumb.link" v-if="crumb.external === true" :class="{ 'text-sky-500': $page.url === crumb.link }"
                                   class="inline-block ml-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
                                    {{ crumb.label }}
                                </a>
                                <Link :href="crumb.link" v-else  :class="{ 'text-sky-500': $page.url === crumb.link }"
                                      class="inline-block ml-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
                                    {{ crumb.label }}
                                </Link>
                            </div>
                            <div v-if="(crumbsLength-1 == key)" class="flex items-center">
                                <svg class="flex-shrink-0 w-6 h-full text-gray-200 dark:text-gray-700" viewBox="0 0 24 44"
                                     preserveAspectRatio="none" fill="currentColor"
                                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z"/>
                                </svg>
                                <span
                                    class="inline-block ml-4 text-sm font-medium text-sky-500 dark:text-sky-500 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
                                    {{ crumb.label }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

    </div>
</template>

<script lang="ts" setup>
import {Link} from '@inertiajs/vue3';
import {computed} from "vue";
import CrumbData = App.DataTransferObjects.CrumbData;


const props = withDefaults(
    defineProps<{
        crumbs: CrumbData[],
    }>(), {});
let crumbsLength = computed<number>(() => props.crumbs?.length);

</script>
