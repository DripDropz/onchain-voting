<template>
    <div class="flex flex-row my-8 border rounded-lg border-slate-900 dark:border-slate-700 dark:text-slate-100">
        <div class="w-[75%]">
            <div class="p-4">
                <h2 class="mb-4 text-2xl font-bold">
                    <Link :href="route('petitions.view', { petition: petition.hash })
                        ">
                    {{ petition.title }}
                    </Link>
                </h2>
                <p>{{ petition.description }}</p>
            </div>
            <div class="flex flex-row items-center justify-between p-4 border border-b-0 border-l-0 border-r-0 border-black border-t- dark:border-slate-700">
                <div class="flex flex-row items-center justify-between gap-8 w-full">
                    <h2 class="text-sm font-bold">{{ petition.hash }}</h2>

                    <div class="flex flex-row items-center gap-2">
                        <Link
                            v-if="petition?.user_id === user?.id"
                            :href="route('petitions.manage', { petition: petition.hash })"
                            class="font-semibold text-sky-500 hover:text-slate-700 dark:hover:text-white">
                            <span>Manage</span>
                        </Link>
                        <div>

                        <button
                            v-if="petition.user_id === user?.id && petition.status === 'approved'"
                            @click.prevent="publishPetition()"
                            class="font-semibold text-sky-500 hover:text-slate-700 dark:hover:text-white">
                            <span>Publish</span>
                        </button>
                        <span v-else-if="petition.status === 'published'" class="font-bold leading-tight text-green-500">Published</span>
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-8">
                        <div class="flex flex-row items-center gap-2">
                            <UsersIcon class="w-6 h-6"/>
                            <p>{{ petition.signatures_count ?? "-" }}</p>
                        </div>
                        <div class="flex flex-row items-center gap-2">
                            <EnvelopeIcon class="w-6 h-6" />
                            <p>{{ petition.status === 'published' ? 'Published' : (petition.status === 'closed' ? 'Closed' : formatDate(petition.created_at)) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul
            class="bg-slate-100 dark:bg-slate-700 w-[25%] flex flex-row items-center rounded-tr-lg rounded-br-lg justify-center">
            <li class="w-auto ocv-link">
                <img :src="voteAppLogo" alt="Open Chainvote App Logo" class="w-10 h-10" />
            </li>
            <li class="w-auto ocv-link">
                <h1 class="font-bold tracking-tight sm:text-xl xl:text-3xl font-display text-slate-900 dark:text-slate-200">
                    ChainVote
                </h1>
            </li>
        </ul>
        <Modal :show="showPublishModal" :modalType="'publish'">
            <PublishPetition :petition="petition" @close="showPublishModal = false" />
        </Modal>
    </div>
</template>

<script setup lang="ts">
import { defineProps } from "vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import Modal from "@/Components/Modal.vue";
import PublishPetition from "./PublishPetition.vue";
import { UsersIcon, EnvelopeIcon } from "@heroicons/vue/20/solid";
import voteAppLogo from "../../../../images/openchainvote.png";
import { Link, router, useForm } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { useConfigStore } from "@/stores/config-store";
import { storeToRefs } from "pinia";
import AlertService from "@/shared/Services/alert-service";

const props = defineProps<{
    petition: PetitionData;
}>();

const formatDate = (dateString: string): string => {
    const options = { month: "2-digit", day: "2-digit", year: "2-digit" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

let configStore = useConfigStore()
let { user, showPublishModal } = storeToRefs(configStore);

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
