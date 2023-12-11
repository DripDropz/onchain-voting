<template>
    <form class="relative" @submit.prevent="submit">
        <div
            class="overflow-visible border border-gray-300 rounded-lg shadow-sm dark:border-gray-700 focus-within:border-indigo-500 dark:focus-within:border-indigo-600 focus-within:ring-1 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-500">
            <label for="title" class="sr-only">Title</label>
            <input type="text" name="title" id="title" v-model="form.title"
                   class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-indigo-100 dark:bg-gray-900"
                   placeholder="Title"/>

            <label for="description" class="sr-only">Description</label>
            <TextareaInput v-model="form.description" rows="4" placeholder="Write a description..." />

            <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                <label for="maxChoices"
                       class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">Max Choices</label>
                <input type="text" name="maxChoices" id="maxChoices" v-model="form.maxChoices"
                       class="relative block w-full flex flex-1 border-0 pt-2.5 sm:text-sm sm:leading-6 font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-indigo-100 dark:bg-gray-900 rounded-lg"/>
            </div>

            <Listbox as="div" v-model="form.ballot"
                     class="flex items-center gap-8 px-2 py-2 border-t border-gray-200 xl:px-3 dark:border-gray-700">
                <ListboxLabel class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                    Ballot
                </ListboxLabel>
                <div class="relative flex flex-1 mt-2">
                    <ListboxButton
                        class="relative w-full cursor-default rounded-md bg-indigo-100 dark:bg-gray-900 py-1.5 pl-3 pr-10 text-left text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-700 sm:text-sm sm:leading-6">
                        <span class="block truncate">{{ ballot.title }}</span>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                              <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>
                            </span>
                    </ListboxButton>

                    <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100"
                                leave-to-class="opacity-0">
                        <ListboxOptions
                            class="absolute z-10 w-full py-1 mt-1 overflow-auto text-base bg-indigo-100 rounded-md shadow-lg max-h-60 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                            <ListboxOption as="template" :value="ballot.hash"
                                           v-slot="{ active, selected }">
                                <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                        <span class="capitalize"
                                              :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{
                                                ballot.title
                                            }}</span>

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
                    Type
                </ListboxLabel>
                <div class="relative flex flex-1 mt-2">
                    <ListboxButton
                        class="relative w-full cursor-default rounded-md bg-indigo-100 dark:bg-gray-900 py-1.5 pl-3 pr-10 text-left text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-700 sm:text-sm sm:leading-6">
                        <span class="block truncate">{{ form.type ?? questionTypes[0] }}</span>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                              <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>
                            </span>
                    </ListboxButton>

                    <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100"
                                leave-to-class="opacity-0">
                        <ListboxOptions
                            class="absolute z-10 w-full py-1 mt-1 overflow-auto text-base bg-indigo-100 rounded-md shadow-lg max-h-60 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                            <ListboxOption as="template" :value="type.name" :key="type.name" v-for="type in questionTypes"
                                           v-slot="{ active, selected }">
                                <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                        <span class="capitalize"
                                              :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{
                                                type.name
                                            }}</span>

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

            <Listbox as="div" v-model="form.status"
                     class="flex items-center gap-8 px-2 py-2 border-t border-gray-200 xl:px-3 dark:border-gray-700">
                <ListboxLabel class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                    Status
                </ListboxLabel>
                <div class="relative flex flex-1 mt-2">
                    <ListboxButton
                        class="relative w-full cursor-default rounded-md bg-indigo-100 dark:bg-gray-900 py-1.5 pl-3 pr-10 text-left text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-700 sm:text-sm sm:leading-6">
                        <span class="block truncate">{{ form.status ?? questionStatuses[0] }}</span>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                              <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>
                            </span>
                    </ListboxButton>

                    <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100"
                                leave-to-class="opacity-0">
                        <ListboxOptions
                            class="absolute z-10 w-full py-1 mt-1 overflow-auto text-base bg-indigo-100 rounded-md shadow-lg max-h-60 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                            <ListboxOption as="template" :value="status.case" :key="status.case" v-for="status in questionStatuses"
                                           v-slot="{ active, selected }">
                                <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                        <span class="capitalize"
                                            :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{
                                            status.value
                                        }}</span>

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

            <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                <label for="supplemental"
                       class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">Supplemental Resource</label>
                <input type="text" name="supplemental" id="supplemental" v-model="form.supplemental" placeholder="https://"
                       class="relative block w-full flex flex-1 border-0 pt-2.5 sm:text-sm sm:leading-6 font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-indigo-100 dark:bg-gray-900 rounded-lg"/>
            </div>
        </div>

        <div
            class="flex items-center justify-between py-3 mt-2">
            <div class="flex">

            </div>
            <div class="flex-shrink-0">
                <button type="submit"
                        class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        {{ question ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </form>
</template>
<script setup lang="ts">
import {useForm} from '@inertiajs/vue3';
import BallotData = App.DataTransferObjects.BallotData;
import QuestionData = App.DataTransferObjects.QuestionData;
import {Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions} from '@headlessui/vue';
import {ChevronUpDownIcon, CheckIcon} from '@heroicons/vue/20/solid';
import AlertService from '@/shared/Services/alert-service';
import { useModal } from "momentum-modal"
import TextareaInput from '@/Components/TextareaInput.vue';

const props = defineProps<{
    ballot: BallotData;
    question?: QuestionData;
    questionTypes: string[];
    questionStatuses: string[];
}>();

const form = useForm({
    ballot: props.ballot?.hash,
    title: props.question?.title ?? null,
    description: props.question?.description ?? '',
    status: props.questionStatuses[2],
    type: props.questionTypes[0],
    maxChoices: props.question?.maxChoices ?? 1,
    supplemental: props.question?.supplemental ?? null,
});

const questionTypes = [
    {id: 1, name: 'single'},
    {id: 2, name: 'multiple'},
    {id: 2, name: 'ranked'},
];

const questionStatuses = [
    {case: 'draft', value: 'Draft'},
    {case: 'pending', value: 'Pending'},
    {case: 'published', value: 'Published'},
];

const { close } = useModal();

function submit() {
    if(!props.question?.hash){
        form.post(route('admin.ballots.questions.store', {ballot: props.ballot?.hash}), {
            preserveScroll: true,
            preserveState: true,
        onSuccess: () => {
                AlertService.show(['Question created successfully'], 'success');
                close();
            },
            onError: (errors) => {
                AlertService.show(
                    Object
                    .entries(errors)
                    .map(([key, value]) => value)
                );
            },
        });
    } else  {
        form.patch(route('admin.ballots.questions.update', {ballot: props.ballot?.hash, question: props.question?.hash}), {
            onSuccess: () => {
                AlertService.show(['Question updated successfully'], 'success');
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
