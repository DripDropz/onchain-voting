<template>
    <section class="flex items-start h-full gap-4">
        <div class="w-2/3">
            <header class="mb-2">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create Ballot</h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Lets create a new Ballot!
                </p>
            </header>

            <form @submit.prevent="submitForm" class="relative">
                <div
                    class="overflow-hidden border border-gray-300 rounded-lg shadow-sm dark:border-gray-700 focus-within:border-indigo-500 dark:focus-within:border-indigo-600 focus-within:ring-1 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-500">
                    <label for="title" class="sr-only">Title</label>
                    <input type="text" name="title" id="title" v-model="form.title"
                           class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-indigo-100 dark:bg-gray-900"
                           placeholder="Title"/>

                    <label for="description" class="sr-only">Description</label>
                    <TextareaInput rows="4" name="description" id="description" v-model="form.description" placeholder="Write a description..."/>

                    <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                        <label for="version"
                               class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">State
                            Date & Time</label>
                        <input type="datetime-local" name="version" id="version" v-model="form.started_at"
                               placeholder="Datetime"
                               class="relative w-full flex flex-1 border-0 pt-2.5 sm:text-sm sm:leading-6 font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 bg-indigo-100 dark:bg-gray-900 ring-1 ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-700 rounded-lg"/>
                    </div>
                    <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                        <label for="version"
                               class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">End
                            Date & Time</label>
                        <input type="datetime-local" name="version" id="version" v-model="form.ended_at"
                               placeholder="Datetime"
                               class="relative w-full flex flex-1 border-0 pt-2.5 sm:text-sm sm:leading-6 font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-indigo-100 dark:bg-gray-900 ring-1 ring-gray-300 dark:ring-gray-700  focus:ring-indigo-600 dark:focus:ring-indigo-700 rounded-lg"/>
                    </div>

                    <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                        <label for="version"
                               class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">Version</label>
                        <input type="text" name="version" id="version" v-model="form.version" placeholder="Version"
                               class="relative w-full flex flex-1 border-0 pt-2.5 sm:text-sm sm:leading-6 font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 bg-indigo-100 dark:bg-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-700 rounded-lg"/>
                    </div>

                    <Listbox as="div" @update:modelValue="value => form.status = value"
                             class="flex items-center gap-8 px-2 py-2 border-t border-gray-200 xl:px-3 dark:border-gray-700">
                        <ListboxLabel class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                            Ballot Status
                        </ListboxLabel>
                        <div class="relative flex flex-1 mt-2">
                            <ListboxButton
                                class="relative w-full cursor-default rounded-md bg-indigo-100 dark:bg-gray-900 py-1.5 pl-3 pr-10 text-left text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-700 sm:text-sm sm:leading-6">
                                <span class="block capitalize truncate">{{ form.status }}</span>
                                <span class="block capitalize truncate">{{ form.status }}</span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                  <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>
                                </span>
                            </ListboxButton>

                            <transition leave-active-class="transition duration-100 ease-in"
                                        leave-from-class="opacity-100" leave-to-class="opacity-0">
                                <ListboxOptions
                                    class="absolute z-10 w-full py-1 mt-1 overflow-auto text-base bg-indigo-100 rounded-md shadow-lg max-h-60 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ListboxOption as="template" v-for="status in Object.values(ballotStatuses)" :key="status"
                                                   :value="status" v-slot="{ active, selected }">
                                        <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                            <span class="capitalize"
                                                  :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                                                {{status }}
                                            </span>

                                            <span v-if="selected"
                                                  :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                                <CheckIcon class="w-5 h-5" aria-hidden="true"/>
                                          </span>
                                        </li>
                                    </ListboxOption>
                                </ListboxOptions>
                            </transition>
                        </div>
                    </Listbox>

                    <Listbox as="div" v-model="form.type"
                             class="flex items-center gap-8 px-2 py-2 border-t border-gray-200 xl:px-3 dark:border-gray-700">
                        <ListboxLabel class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                            Ballot Type
                        </ListboxLabel>
                        <div class="relative flex flex-1 mt-2">
                            <ListboxButton
                                class="relative w-full cursor-default rounded-md bg-indigo-100 dark:bg-gray-900 py-1.5 pl-3 pr-10 text-left text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-700 sm:text-sm sm:leading-6">
                                <span class="block truncate">{{ form.type }}</span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                  <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>
                                </span>
                            </ListboxButton>

                            <transition leave-active-class="transition duration-100 ease-in"
                                        leave-from-class="opacity-100"
                                        leave-to-class="opacity-0">
                                <ListboxOptions
                                    class="absolute z-10 w-full py-1 mt-1 overflow-auto text-base bg-indigo-100 rounded-md shadow-lg max-h-60 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ListboxOption as="template" v-for="type in Object.values(ballotTypes)" :key="type"
                                                   :value="type"
                                                   v-slot="{ active, selected }">
                                        <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                            <span class="capitalize"
                                                  :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                                                {{type}}
                                            </span>

                                            <span v-if="selected"
                                                  :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                                <CheckIcon class="w-5 h-5" aria-hidden="true"/>
                                          </span>
                                        </li>
                                    </ListboxOption>
                                </ListboxOptions>
                            </transition>
                        </div>
                    </Listbox>

                    <div aria-hidden="true">
                        <div class="py-3">
                            <div class="h-10"/>
                        </div>
                        <div class="h-px"/>
                        <div class="py-3">
                            <div class="py-px">
                                <div class="h-10"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-0 inset-x-px">
                    <div
                        class="flex justify-end px-2 py-3 space-x-2 border-t border-gray-200 flex-nowrap sm:px-3 dark:border-gray-700">
                        <Listbox as="div" v-model="assigned" class="flex-shrink-0">
                            <ListboxLabel class="sr-only">Assign</ListboxLabel>
                            <div class="relative">
                                <ListboxButton
                                    class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 rounded-full whitespace-nowrap bg-gray-50 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900 sm:px-3">
                                    <UserCircleIcon v-if="assigned.value === null"
                                                    class="flex-shrink-0 w-5 h-5 text-gray-300 dark:text-gray-100 sm:-ml-1"
                                                    aria-hidden="true"/>

                                    <img v-else :src="assigned.avatar" alt=""
                                         class="flex-shrink-0 w-5 h-5 rounded-full"/>

                                    <span
                                        :class="[assigned.value === null ? '' : 'text-gray-900  dark:text-gray-300', 'hidden truncate sm:ml-2 sm:block']">{{
                                            assigned.value === null ? 'Assign' : assigned.name
                                        }}</span>
                                </ListboxButton>

                                <transition leave-active-class="transition duration-100 ease-in"
                                            leave-from-class="opacity-100" leave-to-class="opacity-0">
                                    <ListboxOptions
                                        class="absolute right-0 z-10 py-3 mt-1 overflow-auto text-base bg-indigo-100 rounded-lg shadow max-h-56 w-52 dark:bg-gray-900 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                        <ListboxOption as="template" v-for="assignee in assignees" :key="assignee.value"
                                                       :value="assignee" v-slot="{ active }">
                                            <li :class="[active ? 'bg-gray-100  dark:bg-gray-700' : 'bg-indigo-100  dark:bg-gray-900', 'relative cursor-default select-none px-3 py-2']">
                                                <div class="flex items-center">
                                                    <img v-if="assignee.avatar" :src="assignee.avatar" alt=""
                                                         class="flex-shrink-0 w-5 h-5 rounded-full"/>
                                                    <UserCircleIcon v-else
                                                                    class="flex-shrink-0 w-5 h-5 text-gray-400 dark:text-gray-100"
                                                                    aria-hidden="true"/>
                                                    <span
                                                        class="block ml-3 font-medium text-gray-400 truncate dark:text-gray-100">{{
                                                            assignee.name
                                                        }}</span>
                                                </div>
                                            </li>
                                        </ListboxOption>
                                    </ListboxOptions>
                                </transition>
                            </div>
                        </Listbox>
                    </div>
                    <div
                        class="flex items-center justify-between px-2 py-3 space-x-3 border-t border-gray-200 dark:border-gray-700 sm:px-3">
                        <div class="flex">

                        </div>
                        <div class="flex-shrink-0">
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                {{ ballot ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="sticky w-1/3 top-1">
            <BallotPublishChecklist class="mt-16" :ballot="ballot" :status="status"/>
        </div>
    </section>
</template>

<script setup lang="ts">
import {Link, router, useForm, usePage} from '@inertiajs/vue3';
import {ref, watch} from 'vue'
import {Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions} from '@headlessui/vue'
import {
    UserCircleIcon,
    ChevronUpDownIcon,
    CheckIcon
} from '@heroicons/vue/20/solid'
import BallotData = App.DataTransferObjects.BallotData;
import {computed} from 'vue';
import AlertService from '@/shared/Services/alert-service';
import BallotPublishChecklist from './BallotPublishChecklist.vue';
import { Ref } from 'vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import AdminBallotService from '../Services/admin-ballot-service';

const props = defineProps<{
    status?: String;
    ballot?: BallotData;
}>();

const user = usePage().props.auth.user;
const questions = ref(props.ballot?.questions);

const hasPublishedQuestion = computed(() => {
    const ballotQuestions = questions.value?.filter((question, index) => {
        return question.status == 'published';
    });
    const questionsCount = ballotQuestions?.length ?? 0;
    return questionsCount > 0 ? true : false;
})

const hasPublishedQuestionChoice = computed(() => {
    const ballotQuestionsWithChoice = questions.value?.filter((question, index) => {
        if (question.status == 'published') {
            let choices = ref(question.choices);
            let choicesCount = choices.value?.length ?? 0;
            return choicesCount > 0;
        }
    });

    const questionsCount = ballotQuestionsWithChoice?.length ?? 0;
    return questionsCount > 0 ? true : false;
})


const assignees = [
    {name: 'Unassigned', value: null},
    {
        name: 'Wade Cooper',
        value: 'wade-cooper',
        avatar:
            'https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
    },
    // More items...
]
const labels = [

    {name: 'Unlabelled', value: null},
    {name: 'Engineering', value: 'engineering'},
    // More items...
];
const dueDates = [
    {name: 'No due date', value: null},
    {name: 'Today', value: 'today'},
    // More items...
];
const assigned = ref(assignees[0]);

const ballotTypes = ref({});
AdminBallotService.getBallotTypes().then((types) => {
    ballotTypes.value = types;
});

const ballotStatuses = ref({});
AdminBallotService.getBallotStatuses().then((statuses) => {
    ballotStatuses.value = statuses;
});

const form = useForm({
    hash: props?.ballot?.hash,
    title: props?.ballot?.title ?? '',
    description: props?.ballot?.description ?? '',
    version: props?.ballot?.version,
    status: props?.ballot?.status ?? 'Select Status',
    type: props?.ballot?.type ?? 'Select Type',
    started_at: props?.ballot?.started_at,
    ended_at: props?.ballot?.ended_at,
});


function submitForm() {
    if (!props.ballot?.hash) {
        form.post(route('admin.ballots.create'), {
            onSuccess: () => {
                AlertService.show(['Ballot created successfully'], 'success');
            },
            onError: (errors) => {
                AlertService.show(
                    Object
                    .entries(errors)
                    .map(([key, value]) => value)
                );
            },
        });
    } else {
        form.patch(route('admin.ballots.update', {ballot: props.ballot?.hash}), {
            onSuccess: () => {
                AlertService.show(['Ballot updated successfully'], 'success');
            },
            onError: (errors) => {
                AlertService.show(
                    Object
                    .entries(errors)
                    .map(([key, value]) => value)
                );
            },
        });
    }
}
</script>
@/Pages/Auth/Ballot/Services/admin-ballot-service
