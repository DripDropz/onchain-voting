<template >
    <div class="p-4 bg-white sm:p-8 dark:bg-gray-800 sm:rounded-lg">
        <div class="flex justify-between">
            <h2 class="font-bold">Create Ballot</h2>
            <PrimaryButton @click="emit('select-existing')">
                Choose existing instead
            </PrimaryButton>
        </div>
        <div class="flex flex-col p-2 overflow-hidden gap-y-4">
            <div>
                <p class="mb-2 "> Ballot Details</p>
                <label for="title" class="sr-only">Title</label>
                <TextInputVue type="text" name="title" id="title" v-model="form.ballotTitle"
                    class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-sky-100 dark:bg-gray-900 mb-2"
                    placeholder="Title" />

                <label for="description" class="sr-only">Description</label>
                <TextareaInput rows="4" name="description" id="description" v-model="form.ballotDescription"
                    placeholder="Ballot a description..." />
            </div>

            <div>
                <p class="mb-2 "> Question Details</p>
                <label for="title" class="sr-only">Title</label>
                <TextInputVue type="text" name="title" id="title" v-model="form.questionTitle"
                    class="block w-full border-0 pt-2.5 text-lg font-medium text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 bg-sky-100 dark:bg-gray-900"
                    placeholder="Title" />
            </div>
        </div>

        <div class="flex justify-center mt-4">
            <PrimaryButton :theme="'primary'" @click="emit('submit', form)">
                Save Ballot
            </PrimaryButton>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { InertiaForm, useForm } from '@inertiajs/vue3';
import TextareaInput from '@/Components/TextareaInput.vue';
import TextInputVue from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';


let form = {
    ballotTitle: '',
    ballotDescription: '',
    questionTitle: '',

};

const emit = defineEmits<{
    (event: 'select-existing'): void
    (event: 'submit', form): void
}>()

</script>