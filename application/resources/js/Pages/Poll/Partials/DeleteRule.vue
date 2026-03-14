<template>
    <ModalRoute>
        <div class="flex flex-col gap-3 p-4">
            <p>
                Remove <span class="italic font-bold">{{ rule.title }}</span> from
                <span class="italic font-bold">{{ poll.title }}</span>
            </p>
            <div class="flex justify-center">
                <PrimaryButton :theme="'primary'" @click="removeRule">
                    Remove Rule
                </PrimaryButton>
            </div>
        </div>
    </ModalRoute>
</template>

<script lang="ts" setup>
import PollData = App.DataTransferObjects.PollData;
import RuleData = App.DataTransferObjects.RuleData;
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ModalRoute from "@/Components/ModalRoute.vue";
import { useForm } from "@inertiajs/vue3";
import AlertService from "@/shared/Services/alert-service";
import { useModal } from "momentum-modal";

const props = withDefaults(
    defineProps<{
        poll?: PollData;
        rule?: RuleData;
    }>(),
    {}
);

const { close } = useModal();
const form = useForm({});

function removeRule() {
    form.post(
        route("polls.rules.delete", { poll: props.poll.hash, rule: props.rule.hash }),
        {
            onSuccess: () => {
                AlertService.show(["Rule deleted"], "success");
                close();
            },
            onError: (errors) => {
                AlertService.show(Object.entries(errors).map(([, value]) => value));
            },
        }
    );
}
</script>
