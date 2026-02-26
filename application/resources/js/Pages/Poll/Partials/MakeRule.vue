<template>
    <ModalRoute :max-width-class="'max-w-[55rem]'">
        <div class="flex flex-col h-full p-6 border border-slate-200 gap-y-4 dark:border-slate-700">
            <p class="text-xl font-bold leading-tight xl:text-2xl">Configure Poll Gate</p>
            <div class="flex flex-col gap-2">
                <p>Policy Name</p>
                <TextInput class="w-full" :Placeholder="'Title here...'" v-model="form.title" />
            </div>

            <div class="flex flex-col gap-2">
                <p>Policy ID</p>
                <TextInput class="w-full" :Placeholder="'Paste 56-character policy ID...'" v-model="form.policy" />
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Voters qualify if their wallet holds any asset under this policy.
                </p>
            </div>

            <div class="flex flex-col gap-4">
                <div v-if="!assetData && working">
                    <p>Validating policy and fetching sample asset data...</p>
                </div>

                <div class="w-full gap-4" v-if="assetData">
                    <p class="mb-4 font-bold">
                        We found {{ assetCount }} assets under this policy.
                    </p>
                    <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">
                        This rule saves only the policy ID. Any asset under this policy satisfies the gate.
                    </p>
                    <div class="flex justify-end w-full">
                        <PrimaryButton :theme="'primary'" @click="saveRule">
                            Save Rule
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </ModalRoute>
</template>

<script lang="ts" setup>
import ModalRoute from "@/Components/ModalRoute.vue";
import TextInput from "@/Components/TextInput.vue";
import axios from "axios";
import { ref, watch } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import PollData = App.DataTransferObjects.PollData;
import AlertService from "@/shared/Services/alert-service";
import { useModal } from "momentum-modal";

const props = withDefaults(
    defineProps<{
        poll?: PollData;
        type?: string;
    }>(),
    {}
);

const assetData = ref();
const working = ref(false);
const assetCount = ref();
const { close } = useModal();

const form = useForm({
    policy: "",
    title: "",
    type: props.type,
});

const policyPattern = /^[0-9a-fA-F]{56}$/;

async function query() {
    working.value = true;
    try {
        const res = (await axios.get(route("frost.asset", { policy: form.policy }))).data;
        assetData.value = res.asset;
        assetCount.value = res.assetCount;
    } catch (error) {
        assetData.value = null;
        assetCount.value = null;
    } finally {
        working.value = false;
    }
}

function saveRule() {
    form.post(route("polls.rules.saveRule", { poll: props.poll.hash }), {
        onSuccess: () => {
            AlertService.show(["Rule created"], "success");
            close();
        },
        onError: (errors) => {
            AlertService.show(Object.entries(errors).map(([, value]) => value));
        },
    });
}

watch(
    () => form.policy,
    () => {
        const trimmedPolicy = form.policy.trim();
        form.policy = trimmedPolicy;

        if (!policyPattern.test(trimmedPolicy)) {
            assetData.value = null;
            assetCount.value = null;
            working.value = false;
            return;
        }

        query();
    }
);
</script>
