<template>
    <div class="relative flex flex-col justify-start px-10 py-6 pb-8 border border-gray-800 rounded-lg dark:text-gray-200 dark:border-gray-200">
        <h2 class="mb-4 text-2xl font-extrabold">{{ publicPoll.question?.title }}</h2>
        <div
            v-for="(choice, index) in publicPoll.question?.choices"
            :key="index" class="flex mb-8">
            <label class="w-full cursor-pointer">
                <input
                    type="radio"
                    v-model="form.selectedChoiceHash"
                    :name="choice.hash"
                    :value="choice.hash"
                    class="sr-only peer"
                    @click="toggleSelection(choice.hash)"
                />
                <span
                    class="block w-full p-3 border-2 rounded-lg dark:text-gray-200 hover:shadow peer-checked:border-sky-500 hover:bg-blue-100 dark:hover:bg-black"
                    :class="{ 'border-sky-500 bg-sky-500': userVotedChoiceTitles.includes(choice?.title) }"
                >
                    <span class="flex items-center justify-between">
                        <span class="pr-8 font-bold">{{
                            index + 1 + ". " + choice?.title
                        }}</span>
                    </span>
                </span>
            </label>
        </div>
        <div v-if="publicPoll.question.status !== 'published'" class="bg-white/10 py-1 px-4 rounded-md mb-4 text-sky-500">
             <p>Poll will be published when an admin approves it.</p>
        </div>
        <div class="mt-auto w-full bottom-0 flex justify-between items-center py-2">
            <span>{{ publicPoll.responses_count ?? 0 }} votes</span>
            <button v-if="publicPoll.user_responses.length < 1"
                @click.prevent="startVoting"
                class="px-4 py-2 mb-2 font-semibold text-white rounded-lg bg-sky-500 hover:bg-slate-600 hover:cursor-pointer ml-72">
                Vote
            </button>
        </div>
    </div>
    <Modal :show="showModal">
        <VoteConfirmation
            @close="showModal = false"
            :poll="publicPoll"
            @submit="submitVote"
        />
    </Modal>
</template>
<script setup lang="ts">
import { computed, ref, watch } from "vue";
import PollData = App.DataTransferObjects.PollData;
import { useForm, usePage } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import AlertService from "@/shared/Services/alert-service";
import VoteConfirmation from "./VoteConfirmation.vue";
import { usePollStore } from '@/stores/poll-store';
import { storeToRefs } from "pinia";

const props = defineProps<{
    poll: PollData;
}>();

const page = usePage();
let pollStore = usePollStore();
let { singlePublicPoll } = storeToRefs(pollStore);

let publicPoll = ref<PollData>(props.poll);

const user = computed(() => page.props.auth.user);

let showModal = ref(false);
let form = useForm({
    selectedChoiceHash: "",
    questionHash: publicPoll.value.question?.hash,
});

watch([()=>singlePublicPoll.value], () => {
    if(singlePublicPoll.value.hash === props.poll.hash){
        publicPoll.value = singlePublicPoll.value
    }
},{deep:true})

function startVoting() {
    if (!user.value) {
        AlertService.show(["Login to vote on the poll."], "info");
        return;
    }
    if (!form.selectedChoiceHash) {
        AlertService.show(
            ["No choice is selected to vote on the poll."],
            "info"
        );
        return;
    }
    showModal.value = true;
}

function submitVote() {
    form.post(route("polls.storeQuestionResponse", { poll: publicPoll.value.hash }), {
        onSuccess: () => {
            showModal.value = false;
            AlertService.show(["Vote submitted successfully."], "success");
            pollStore.loadPublicPoll(publicPoll.value.hash);
        },
        preserveScroll:true,
    });
}

const toggleSelection = (choiceHash) => {
    const currentSelection = choiceHash;
    if (form.selectedChoiceHash === currentSelection) {
        form.selectedChoiceHash = '';
    } else {
        form.selectedChoiceHash = currentSelection;
    }
};

const userVotedChoiceTitles = computed(() => {
  return publicPoll.value.user_responses.flatMap((response) => response.choices.map((choice) => choice.title));
});
</script>
