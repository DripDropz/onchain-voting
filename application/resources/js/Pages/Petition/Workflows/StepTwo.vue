<template>
    <VoterLayout page="Create a petition">
        <section class="w-full max-w-2xl mx-auto pt-12 px-4 space-y-6">
            <WorkflowProgress :current-step="2" />

            <div class="space-y-2 dark:text-white">
                <h1 class="text-4xl font-extrabold">Tell us about your petition</h1>
                <p class="text-lg text-gray-500 dark:text-gray-400">
                    Add a cover image and write your petition description.
                </p>
            </div>

            <div class="space-y-6">

                <!-- Cover image upload -->
                <div class="space-y-2">
                    <label class="block font-medium text-base dark:text-white">
                        Cover Image <span class="text-sm font-normal text-gray-400">(optional, max 4 MB)</span>
                    </label>

                    <!-- Preview -->
                    <div v-if="imagePreview" class="relative w-full">
                        <img
                            :src="imagePreview"
                            alt="Cover image preview"
                            class="w-full h-48 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
                        />
                        <button
                            type="button"
                            @click="removeImage"
                            class="absolute top-2 right-2 inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-900/70 hover:bg-gray-900/90 text-white transition-colors"
                            title="Remove image"
                        >
                            <XMarkIcon class="w-4 h-4" />
                        </button>
                        <span v-if="uploading" class="absolute inset-0 flex items-center justify-center rounded-lg bg-black/40">
                            <svg class="w-8 h-8 text-white animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                        </span>
                    </div>

                    <!-- Dropzone (shown when no image) -->
                    <label
                        v-else
                        for="cover-image"
                        class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                        :class="uploading
                            ? 'border-sky-300 dark:border-sky-700 bg-sky-50 dark:bg-sky-950/30 pointer-events-none'
                            : 'border-gray-300 dark:border-gray-600 hover:border-sky-400 dark:hover:border-sky-500 bg-gray-50 dark:bg-gray-800 hover:bg-sky-50 dark:hover:bg-sky-950/20'"
                        @dragover.prevent
                        @drop.prevent="handleDrop"
                    >
                        <div v-if="uploading" class="flex flex-col items-center gap-2 text-sky-500">
                            <svg class="w-8 h-8 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            <span class="text-sm font-medium">Uploading…</span>
                        </div>
                        <div v-else class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-500 px-4 text-center">
                            <PhotoIcon class="w-8 h-8" />
                            <p class="text-sm">
                                <span class="font-semibold text-sky-600 dark:text-sky-400">Click to upload</span>
                                or drag and drop
                            </p>
                            <p class="text-xs">PNG, JPG, GIF — max 4 MB</p>
                        </div>
                    </label>
                    <input
                        id="cover-image"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        ref="fileInput"
                        @change="handleFileChange"
                    />
                    <p v-if="uploadError" class="text-sm text-red-500 dark:text-red-400">{{ uploadError }}</p>
                </div>

                <!-- Description editor -->
                <div class="space-y-2">
                    <label class="block font-medium text-base dark:text-white">Description</label>
                    <v-md-editor v-model="form.description" height="400px" lang="en-US" />
                </div>

                <div class="flex flex-row justify-end gap-3 pb-8">
                    <Link
                        :href="route('petitions.create', { petition: petition.hash })"
                        class="inline-flex justify-center rounded-lg bg-white dark:bg-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm font-semibold shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600"
                    >
                        ← Back
                    </Link>
                    <button
                        @click.prevent="submit"
                        :disabled="form.processing || uploading"
                        class="inline-flex justify-center rounded-lg bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                    >
                        Continue →
                    </button>
                </div>
            </div>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import { ref } from "vue";
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import { Link, router, useForm } from "@inertiajs/vue3";
import AlertService from "@/shared/Services/alert-service";
import enUS from "@kangc/v-md-editor/lib/lang/en-US";
import VueMarkdownEditor from "@kangc/v-md-editor";
import WorkflowProgress from "../Partials/WorkflowProgress.vue";
import { PhotoIcon, XMarkIcon } from "@heroicons/vue/20/solid";
import axios from "axios";

const props = defineProps<{
    petition: PetitionData;
}>();

VueMarkdownEditor.lang.use("en-US", enUS);

const form = useForm({
    description: props.petition.description,
});

const fileInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref<string | null>(props.petition.image_url ?? null);
const uploading = ref(false);
const uploadError = ref<string | null>(null);

const handleDrop = (e: DragEvent) => {
    const file = e.dataTransfer?.files?.[0];
    if (file) uploadFile(file);
};

const handleFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) uploadFile(file);
};

const uploadFile = async (file: File) => {
    if (!file.type.startsWith("image/")) {
        uploadError.value = "Please select a valid image file.";
        return;
    }
    if (file.size > 4 * 1024 * 1024) {
        uploadError.value = "Image must be smaller than 4 MB.";
        return;
    }

    uploadError.value = null;
    uploading.value = true;

    // Show local preview immediately
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);

    try {
        const data = new FormData();
        data.append("image", file);

        await axios.post(
            route("petitions.uploadImage", { petition: props.petition.hash }),
            data,
            { headers: { "Content-Type": "multipart/form-data" } }
        );
    } catch {
        uploadError.value = "Image upload failed. Please try again.";
        imagePreview.value = null;
    } finally {
        uploading.value = false;
        if (fileInput.value) fileInput.value.value = "";
    }
};

const removeImage = () => {
    imagePreview.value = null;
    uploadError.value = null;
    if (fileInput.value) fileInput.value.value = "";
};

function submit() {
    if (form.description === "") {
        AlertService.show(["The petition's description is required."], "info");
        return;
    }

    form.patch(route("petitions.update", { petition: props.petition.hash }), {
        onSuccess: () => {
            router.visit(route("petitions.create.stepThree", { petition: props.petition.hash }));
        },
    });
}
</script>
