<template>
    <div class="p-4 bg-white dark:bg-gray-800 space-y-6">
        <div>
            <div
                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-sky-300"
            >
            <LockClosedIcon class="h-7 w-7" aria-hidden="true" />
            </div>
            <div class="mt-3 text-center sm:mt-5">
                <h1 class="font-semibold leading-6 text-gray-900 dark:text-white text-lg">
                    Are you sure you want to publish the petition?
                </h1>
            </div>
        </div>
        <div class="flex justify-center gap-10">
            <button
                type="button"
                class="mt-3 inline-flex justify-center rounded-md bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-100 sm:col-start-1 sm:mt-0"
                @click="$emit('close')"
                ref="cancelButtonRef"
            >Cancel</button>
            <button
                @click.prevent="publishPetition()"
                class="inline-flex justify-center rounded-md bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:col-start-2"
            >
                Publish
            </button>
        </div>
    </div>
</template>
<script setup lang="ts">
import { LockClosedIcon } from '@heroicons/vue/24/outline'
import { useForm } from '@inertiajs/vue3';
import AlertService from '@/shared/Services/alert-service';
import PetitionData = App.DataTransferObjects.PetitionData;

const props = defineProps<{
    petition: PetitionData;
}>();

const form = useForm({
    status: props?.petition?.status,
});

const emit = defineEmits<{(e: 'close'):void}>()

const publishPetition = async () => {
        try {
            await form.put(route("petitions.publish", { petition: props.petition?.hash }));
            props.petition.status = "published";
            AlertService.show(["Petition has been published"], "success");
            emit('close');
        } catch (error) {
            AlertService.show(["There was an error publishing the petition"], "error");
        }
};
</script>
