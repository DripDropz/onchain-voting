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
            <div
                class="flex flex-row items-center justify-between p-4 border border-b-0 border-l-0 border-r-0 border-black border-t- dark:border-slate-700">
                <div class="flex items-center gap-1">
                    <h2 class="text-xl font-bold">{{ petition.hash }}</h2>

                    <div @click.prevent="manage" v-if="petition?.user_id === user?.id">
                        <PrimaryButton :theme="'primary'">
                            <CogIcon aria class="w-4 h-4" />
                            <span>Manage</span>
                        </PrimaryButton>
                    </div>
                </div>

                <div class="flex flex-row items-center gap-8">
                    <div class="flex flex-row items-center gap-2">
                        <UsersIcon class="w-6 h-6" />
                        <p>{{ petition.signatures_count ?? "-" }}</p>
                    </div>
                    <div class="flex flex-row items-center gap-2">
                        <EnvelopeIcon class="w-6 h-6" />
                        <p>{{ formatDate(petition.created_at) }}</p>
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
    </div>
</template>

<script setup lang="ts">
import { defineProps, computed } from "vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import { UsersIcon, EnvelopeIcon, CogIcon } from "@heroicons/vue/20/solid";
import voteAppLogo from "../../../../images/openchainvote.png";
import { Link, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { usePage } from "@inertiajs/vue3";
import { useConfigStore } from "@/stores/config-store";
import { storeToRefs } from "pinia";
import AlertService from "@/shared/Services/alert-service";
import { Alert } from "flowbite-vue";

const props = defineProps<{
    petition: PetitionData;
}>();

const formatDate = (dateString: string): string => {
    const options = { month: "2-digit", day: "2-digit", year: "2-digit" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

let configStore = useConfigStore()
let { user } = storeToRefs(configStore);

let manage = () => {
    if (user.value.id == props.petition.user_id) {
        router.get(route('petitions.manage', {
            petition: props.petition.hash,
        }), {},
            {
                onError: () => {
                    AlertService.show(
                        Object
                            .entries(Error)
                            .map(([key, value]) => value)
                    );
                }
            })
    } else {
        AlertService.show(['You are not the owner Of this Petition'], 'error')
    }

}

const page = usePage();

</script>
