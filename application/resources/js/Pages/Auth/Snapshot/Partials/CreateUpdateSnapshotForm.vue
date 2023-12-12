<template>
    <section class="flex items-center gap-4">
        <div class="w-2/3">
            <header class="mb-2">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create Snapshot</h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Lets create a new Snapshot!
                </p>
            </header>

            <form @submit.prevent="submitForm" class="relative">
                <div
                    class="overflow-hidden border border-gray-300 rounded-lg shadow-sm dark:border-gray-700 focus-within:border-sky-500 dark:focus-within:border-sky-600 focus-within:ring-1 focus-within:ring-sky-500 dark:focus-within:ring-sky-500">
                    <label for="title" class="sr-only">Title</label>
                    <input type="text" name="title" id="title" v-model="form.title"
                           class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-sky-100 dark:bg-gray-900"
                           placeholder="Title"/>

                    <label for="description" class="sr-only">Description</label>
                    <TextareaInput v-model="form.description" placeholder="Write a description..." id="description" rows="4" name="description" />

                    <div class="flex items-center gap-8 px-2 py-4 xl:px-3">
                        <label for="policy_id"
                               class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                               Policy ID</label>
                        <input type="text" name="policy_id" id="policy_id" v-model="form.policy_id" placeholder="Policy" disabled
                               class="relative w-full flex flex-1 border-0 pt-2.5 sm:text-sm sm:leading-6 font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-sky-100 dark:bg-gray-900 rounded-lg"/>
                    </div>

                    <Listbox as="div" v-model="form.status"
                             class="flex items-center gap-8 px-2 py-2 border-t border-gray-200 xl:px-3 dark:border-gray-700">
                        <ListboxLabel class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300 w-44">
                            Snapshot Status
                        </ListboxLabel>
                        <div class="relative flex flex-1 mt-2">
                            <ListboxButton
                                class="relative w-full cursor-default rounded-md bg-sky-100 dark:bg-gray-900 py-1.5 pl-3 pr-10 text-left text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-600 dark:focus:ring-sky-700 sm:text-sm sm:leading-6">
                                <span class="block truncate">{{ form.status }}</span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                  <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>
                                </span>
                            </ListboxButton>

                            <transition leave-active-class="transition duration-100 ease-in"
                                        leave-from-class="opacity-100"
                                        leave-to-class="opacity-0">
                                <ListboxOptions
                                    class="absolute z-10 w-full py-1 mt-1 overflow-auto text-base bg-sky-100 rounded-md shadow-lg max-h-60 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ListboxOption as="template" v-for="status in Object.values(snapshotStatuses)" :key="status"
                                                   :value="status" v-slot="{ active, selected }">
                                        <li :class="[active ? 'bg-sky-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                            <span class="capitalize"
                                                  :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{
                                                    status
                                                }}</span>

                                            <span v-if="selected"
                                                  :class="[active ? 'text-white' : 'text-sky-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
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
                            Snapshot Type
                        </ListboxLabel>
                        <div class="relative flex flex-1 mt-2">
                            <ListboxButton
                                class="relative w-full cursor-default rounded-md bg-sky-100 dark:bg-gray-900 py-1.5 pl-3 pr-10 text-left text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-600 dark:focus:ring-sky-700 sm:text-sm sm:leading-6">
                                <span class="block capitalize truncate">{{ form.type }}</span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                  <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>
                                </span>
                            </ListboxButton>

                            <transition leave-active-class="transition duration-100 ease-in"
                                        leave-from-class="opacity-100"
                                        leave-to-class="opacity-0">
                                <ListboxOptions
                                    class="absolute z-10 w-full py-1 mt-1 overflow-auto text-base bg-sky-100 rounded-md shadow-lg max-h-60 dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ListboxOption as="template" v-for="type in Object.values(snapshotTypes)" :key="type"
                                                   :value="type"
                                                   v-slot="{ active, selected }">
                                        <li :class="[active ? 'bg-sky-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                            <span class="capitalize"
                                                  :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{
                                                    type
                                                }}</span>

                                            <span v-if="selected"
                                                  :class="[active ? 'text-white' : 'text-sky-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
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
                        class="flex items-center justify-between px-2 py-3 space-x-3 border-t border-gray-200 dark:border-gray-700 sm:px-3">
                        <div class="flex">

                        </div>
                        <div class="flex-shrink-0">
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                                {{ snapshot ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</template>

<script setup lang="ts">
import {Link, router, useForm, usePage} from '@inertiajs/vue3';
import {ref, watch} from 'vue'
import {Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions} from '@headlessui/vue'
import {
    ChevronUpDownIcon,
    CheckIcon
} from '@heroicons/vue/20/solid'
import SnapshotData = App.DataTransferObjects.SnapshotData;
import SnapshotService from "@/Pages/Auth/Snapshot/Services/SnapshotService";
import AlertService from '@/shared/Services/alert-service';
import TextareaInput from '@/Components/TextareaInput.vue';

const props = defineProps<{
    snapshot?: SnapshotData;
}>();

const user = usePage().props.auth.user;

const snapshotTypes = ref([]);
SnapshotService.getSnapshotTypes().then((types) => {
    snapshotTypes.value = types;
});

const snapshotStatuses = ref([]);
SnapshotService.getSnapshotStatuses().then((statuses) => {
    snapshotStatuses.value = statuses;
});

const form = useForm({
    title: props?.snapshot?.title,
    description: props?.snapshot?.description ?? '',
    policy_id: props?.snapshot?.policy_id ?? 'lovelace',
    status: props?.snapshot?.status ?? 'published',
    type: props?.snapshot?.type ?? 'file'
});

function submitForm() {
    if (!props.snapshot?.hash) {
        form.post(route('admin.snapshots.create'), {
            onSuccess: () => {
                AlertService.show(['Snapshot created successfully'], 'success');
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
        form.patch(route('admin.snapshots.update', {snapshot: props.snapshot?.hash}), {
            onSuccess: () => {
                AlertService.show(['Snapshot updated successfully'], 'success');
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
