<template>
    <section>
        <header class="mb-2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create Ballot</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Lets create a new Ballot!
            </p>
        </header>

        <form @submit.prevent="submit" class="relative">
            <div class="overflow-hidden border border-gray-300 rounded-lg shadow-sm dark:border-gray-700 focus-within:border-indigo-500 dark:focus-within:border-indigo-600 focus-within:ring-1 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-500">
                <label for="title" class="sr-only">Title</label>
                <input type="text" name="title" id="title" v-model="form.title"
                       class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-white dark:bg-gray-900" placeholder="Title" />

                <label for="description" class="sr-only">Description</label>
                <textarea rows="4" name="description" id="description" v-model="form.description" class="block w-full py-0 text-gray-900 bg-white border-0 resize-none dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6 dark:bg-gray-900" placeholder="Write a description..." />

                <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                    <label for="version" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">Version</label>
                    <input type="text" name="version" id="version" v-model="form.version"  placeholder="Version"
                           class="relative block w-full flex flex-1 border-0 pt-2.5 sm:text-sm sm:leading-6 font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-white dark:bg-gray-900 rounded-lg" />
                </div>

                <Listbox as="div" v-model="form.status" class="flex items-center gap-8 px-2 py-2 border-t border-gray-200 xl:px-3 dark:border-gray-700">
                    <ListboxLabel class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">Ballot Status</ListboxLabel>
                    <div class="relative flex flex-1 mt-2">
                        <ListboxButton class="relative w-full cursor-default rounded-md bg-white dark:bg-gray-900 py-1.5 pl-3 pr-10 text-left text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-700 sm:text-sm sm:leading-6">
                            <span class="block truncate">{{ form.status }}</span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                              <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true" />
                            </span>
                        </ListboxButton>

                        <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <ListboxOptions class="absolute z-10 w-full py-1 mt-1 overflow-auto text-base bg-white rounded-md shadow-lg max-h-60 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                <ListboxOption as="template" v-for="status in ballotStatuses" :key="status.case" :value="status.case" v-slot="{ active, selected }">
                                    <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                        <span class="capitalize" :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{ status.value }}</span>

                                        <span v-if="selected" :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                            <CheckIcon class="w-5 h-5" aria-hidden="true" />
                                      </span>
                                    </li>
                                </ListboxOption>
                            </ListboxOptions>
                        </transition>
                    </div>
                </Listbox>

                <Listbox as="div" v-model="form.type" class="flex items-center gap-8 px-2 py-2 border-t border-gray-200 xl:px-3 dark:border-gray-700">
                    <ListboxLabel class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">Ballot Type</ListboxLabel>
                    <div class="relative flex flex-1 mt-2">
                        <ListboxButton class="relative w-full cursor-default rounded-md bg-white dark:bg-gray-900 py-1.5 pl-3 pr-10 text-left text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-700 sm:text-sm sm:leading-6">
                            <span class="block truncate">{{ form.type }}</span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                              <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true" />
                            </span>
                        </ListboxButton>

                        <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <ListboxOptions class="absolute z-10 w-full py-1 mt-1 overflow-auto text-base bg-white rounded-md shadow-lg max-h-60 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                <ListboxOption as="template" v-for="type in ballotTypes" :key="type.id" :value="type" v-slot="{ active, selected }">
                                    <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                        <span class="capitalize" :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{ type.name }}</span>

                                        <span v-if="selected" :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                            <CheckIcon class="w-5 h-5" aria-hidden="true" />
                                      </span>
                                    </li>
                                </ListboxOption>
                            </ListboxOptions>
                        </transition>
                    </div>
                </Listbox>

                <!-- Spacer element to match the height of the toolbar -->
                <div aria-hidden="true">
                    <div class="py-3">
                        <div class="h-10" />
                    </div>
                    <div class="h-px" />
                    <div class="py-3">
                        <div class="py-px">
                            <div class="h-10" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-0 inset-x-px">
                <!-- Actions: These are just examples to demonstrate the concept, replace/wire these up however makes sense for your project. -->
                <div class="flex justify-end px-2 py-3 space-x-2 border-t border-gray-200 flex-nowrap sm:px-3 dark:border-gray-700">
                    <Listbox as="div" v-model="assigned" class="flex-shrink-0">
                        <ListboxLabel class="sr-only">Assign</ListboxLabel>
                        <div class="relative">
                            <ListboxButton class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 rounded-full whitespace-nowrap bg-gray-50 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900 sm:px-3">
                                <UserCircleIcon v-if="assigned.value === null" class="flex-shrink-0 w-5 h-5 text-gray-300 dark:text-gray-100 sm:-ml-1" aria-hidden="true" />

                                <img v-else :src="assigned.avatar" alt="" class="flex-shrink-0 w-5 h-5 rounded-full" />

                                <span :class="[assigned.value === null ? '' : 'text-gray-900  dark:text-gray-300', 'hidden truncate sm:ml-2 sm:block']">{{ assigned.value === null ? 'Assign' : assigned.name }}</span>
                            </ListboxButton>

                            <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                                <ListboxOptions class="absolute right-0 z-10 py-3 mt-1 overflow-auto text-base bg-white rounded-lg shadow max-h-56 w-52 dark:bg-gray-900 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ListboxOption as="template" v-for="assignee in assignees" :key="assignee.value" :value="assignee" v-slot="{ active }">
                                        <li :class="[active ? 'bg-gray-100  dark:bg-gray-700' : 'bg-white  dark:bg-gray-900', 'relative cursor-default select-none px-3 py-2']">
                                            <div class="flex items-center">
                                                <img v-if="assignee.avatar" :src="assignee.avatar" alt="" class="flex-shrink-0 w-5 h-5 rounded-full" />
                                                <UserCircleIcon v-else class="flex-shrink-0 w-5 h-5 text-gray-400 dark:text-gray-100" aria-hidden="true" />
                                                <span class="block ml-3 font-medium text-gray-400 truncate dark:text-gray-100">{{ assignee.name }}</span>
                                            </div>
                                        </li>
                                    </ListboxOption>
                                </ListboxOptions>
                            </transition>
                        </div>
                    </Listbox>


<!--                    <Listbox as="div" v-model="labelled" class="flex-shrink-0">-->
<!--                        <ListboxLabel class="sr-only">Add a label</ListboxLabel>-->
<!--                        <div class="relative">-->
<!--                            <ListboxButton class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 rounded-full whitespace-nowrap bg-gray-50 hover:bg-gray-100 sm:px-3">-->
<!--                                <TagIcon :class="[labelled.value === null ? 'text-gray-300 dark:text-gray-700' : 'text-gray-500 dark:text-gray-200', 'h-5 w-5 flex-shrink-0 sm:-ml-1']" aria-hidden="true" />-->
<!--                                <span :class="[labelled.value === null ? '' : 'text-gray-900  dark:text-gray-300', 'hidden truncate sm:ml-2 sm:block']">{{ labelled.value === null ? 'Label' : labelled.name }}</span>-->
<!--                            </ListboxButton>-->

<!--                            <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">-->
<!--                                <ListboxOptions class="absolute right-0 z-10 py-3 mt-1 overflow-auto text-base bg-white rounded-lg shadow max-h-56 w-52 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">-->
<!--                                    <ListboxOption as="template" v-for="label in labels" :key="label.value" :value="label" v-slot="{ active }">-->
<!--                                        <li :class="[active ? 'bg-gray-100' : 'bg-white', 'relative cursor-default select-none px-3 py-2']">-->
<!--                                            <div class="flex items-center">-->
<!--                                                <span class="block font-medium truncate">{{ label.name }}</span>-->
<!--                                            </div>-->
<!--                                        </li>-->
<!--                                    </ListboxOption>-->
<!--                                </ListboxOptions>-->
<!--                            </transition>-->
<!--                        </div>-->
<!--                    </Listbox>-->

<!--                    <Listbox as="div" v-model="dated" class="flex-shrink-0">-->
<!--                        <ListboxLabel class="sr-only">Add a due date</ListboxLabel>-->
<!--                        <div class="relative">-->
<!--                            <ListboxButton class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 rounded-full whitespace-nowrap bg-gray-50 hover:bg-gray-100 sm:px-3">-->
<!--                                <CalendarIcon :class="[dated.value === null ? 'text-gray-300  dark:text-gray-700' : 'text-gray-500  dark:text-gray-900', 'h-5 w-5 flex-shrink-0 sm:-ml-1']" aria-hidden="true" />-->
<!--                                <span :class="[dated.value === null ? '' : 'text-gray-900  dark:text-gray-300', 'hidden truncate sm:ml-2 sm:block']">{{ dated.value === null ? 'Due date' : dated.name }}</span>-->
<!--                            </ListboxButton>-->

<!--                            <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">-->
<!--                                <ListboxOptions class="absolute right-0 z-10 py-3 mt-1 overflow-auto text-base bg-white rounded-lg shadow max-h-56 w-52 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">-->
<!--                                    <ListboxOption as="template" v-for="dueDate in dueDates" :key="dueDate.value" :value="dueDate" v-slot="{ active }">-->
<!--                                        <li :class="[active ? 'bg-gray-100' : 'bg-white', 'relative cursor-default select-none px-3 py-2']">-->
<!--                                            <div class="flex items-center">-->
<!--                                                <span class="block font-medium truncate">{{ dueDate.name }}</span>-->
<!--                                            </div>-->
<!--                                        </li>-->
<!--                                    </ListboxOption>-->
<!--                                </ListboxOptions>-->
<!--                            </transition>-->
<!--                        </div>-->
<!--                    </Listbox>-->
                </div>
                <div class="flex items-center justify-between px-2 py-3 space-x-3 border-t border-gray-200 dark:border-gray-700 sm:px-3">
                    <div class="flex">

                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit"
                                class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Create</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</template>

<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Alert from '../../../models/alert';

defineProps<{
    status?: String;
    // ballot?: BallotData;
}>();

const user = usePage().props.auth.user;






import { ref } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CalendarIcon, PaperClipIcon, TagIcon, UserCircleIcon, ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/20/solid'
import axios from 'axios';
import {useGlobalAlert} from '../../../store/global-alert-store'

const assignees = [


    { name: 'Unassigned', value: null },
    {
        name: 'Wade Cooper',
        value: 'wade-cooper',
        avatar:
            'https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
    },
    // More items...
]
const labels = [


    { name: 'Unlabelled', value: null },
    { name: 'Engineering', value: 'engineering' },
    // More items...
]
const dueDates = [


    { name: 'No due date', value: null },
    { name: 'Today', value: 'today' },
    // More items...
]

const assigned = ref(assignees[0]);
const labelled = ref(labels[0]);
const dated = ref(dueDates[0]);
const ballotTypes = [
    { id: 1, name: 'snapshot' },
];
const ballotStatuses = [
    { case: 'draft', value: 'Draft' },
    { case: 'pending', value: 'Pending' },
    { case: 'published', value: 'Published' },
];


const form = useForm({
    title: '',
    description: '',
    version: '',
    status: ref(ballotStatuses[0].case),
    type: ref(ballotTypes[0].name),
});


const alertStore = useGlobalAlert();
let notification = {} as Alert

let setAlert = (message:string,type:string,) => {
    notification.message =message ;
    notification.type = type;
    notification.show = true
    alertStore.showAlert(notification)
}

let submit = () => {
    // console.log(usePage().props.ziggy.url);
    axios.post(`${usePage().props.ziggy.url}/ballots/create`,form)
    .then((res) => {
        if(res.status == 200){
            setAlert('Ballot created succesfully','success');
            router.get(`${usePage().props.ziggy.url}/ballots/${res.data}`)
        }
        
    })
    .catch((e) => {
        console.log(e?.response?.data?.message);
        setAlert(e?.response?.data?.message,'info');         
    })
}

</script>