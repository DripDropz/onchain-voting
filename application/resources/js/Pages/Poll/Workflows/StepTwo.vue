<template>
    <VoterLayout page="Create a poll" :crumbs="crumbs">
        <section class="w-full max-w-5xl mx-auto pt-12 px-4 space-y-6">
            <WorkflowProgress :current-step="2" />

            <div class="space-y-2 dark:text-white">
                <h1 class="text-4xl font-extrabold">Tell us about your poll</h1>
                <p class="text-lg text-gray-500 dark:text-gray-400">
                    Add a cover image and write your poll description.
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
                    </div>

                    <!-- Dropzone (shown when no image selected) -->
                    <label
                        v-else
                        for="cover-image"
                        class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-lg cursor-pointer transition-colors border-gray-300 dark:border-gray-600 hover:border-sky-400 dark:hover:border-sky-500 bg-gray-50 dark:bg-gray-800 hover:bg-sky-50 dark:hover:bg-sky-950/20"
                        @dragover.prevent
                        @drop.prevent="handleDrop"
                    >
                        <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-500 px-4 text-center">
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

                    <p v-if="imageError" class="text-sm text-red-500 dark:text-red-400">{{ imageError }}</p>
                    <p v-if="form.errors.cover_image" class="text-sm text-red-500 dark:text-red-400">{{ form.errors.cover_image }}</p>
                </div>

                <!-- Description editor -->
                <div class="space-y-2">
                    <label class="block font-medium text-base dark:text-white">Description</label>
                    <v-md-editor v-model="form.description" height="600px" lang="en-US" />
                    <p v-if="form.errors.description" class="text-sm text-red-500 dark:text-red-400">{{ form.errors.description }}</p>
                </div>

                <div class="flex flex-row justify-end gap-3 pb-8">
                    <Link
                        :href="route('polls.create.stepOne', { poll: poll.hash })"
                        class="inline-flex justify-center rounded-lg bg-white dark:bg-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm font-semibold shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600"
                    >
                        ← Back
                    </Link>
                    <button
                        @click.prevent="submit"
                        :disabled="form.processing"
                        class="inline-flex justify-center rounded-lg bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                    >
                        <span v-if="form.processing">Saving…</span>
                        <span v-else>Continue →</span>
                    </button>
                </div>
            </div>
        </section>
    </VoterLayout>
</template>

<script setup lang="ts">
import { ref } from "vue";
import VoterLayout from "@/Layouts/VoterLayout.vue";
import PollData = App.DataTransferObjects.PollData;
import { Link, router, useForm } from "@inertiajs/vue3";
import AlertService from "@/shared/Services/alert-service";
import enUS from "@kangc/v-md-editor/lib/lang/en-US";
import VueMarkdownEditor from "@kangc/v-md-editor";
import WorkflowProgress from "../Partials/WorkflowProgress.vue";
import { PhotoIcon, XMarkIcon } from "@heroicons/vue/20/solid";

const props = defineProps<{
    poll: PollData;
    crumbs?: [];
}>();

VueMarkdownEditor.lang.use("en-US", enUS);

const form = useForm({
    description: props.poll.description ?? "",
    cover_image: null as File | null,
    remove_cover_image: false,
});

const fileInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref<string | null>(props.poll.image_url ?? null);
const imageError = ref<string | null>(null);

const setFile = (file: File) => {
    if (!file.type.startsWith("image/")) {
        imageError.value = "Please select a valid image file.";
        return;
    }
    if (file.size > 4 * 1024 * 1024) {
        imageError.value = "Image must be smaller than 4 MB.";
        return;
    }

    imageError.value = null;
    form.cover_image = file;
    form.remove_cover_image = false;

    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
};

const handleDrop = (e: DragEvent) => {
    const file = e.dataTransfer?.files?.[0];
    if (file) setFile(file);
};

const handleFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) setFile(file);
};

const removeImage = () => {
    imagePreview.value = null;
    imageError.value = null;
    form.cover_image = null;
    if (props.poll.image_url) {
        form.remove_cover_image = true;
    }
    if (fileInput.value) fileInput.value.value = "";
};

function submit() {
    if (!form.description.trim()) {
        AlertService.show(["The poll description is required."], "info");
        return;
    }

    form.post(route("polls.update", { poll: props.poll.hash }), {
        onSuccess: () => {
            router.visit(route("polls.create.stepThree", { poll: props.poll.hash }));
        },
    });
}
</script>
