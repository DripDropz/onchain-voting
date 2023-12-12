<template>
    <form class="relative" >
        <div class="flex flex-col gap-3 px-2 py-4 xl:px-3">
            <h2 class="text-lg xl:text-xl">New wallet</h2>
            <div>
                <NewBallotPolicyForm :form="form" :ballot="ballot" />
            </div>
        </div>

        <div class="flex flex-col justify-center gap-3 px-2 py-4 xl:px-3">
            <label for="script" class="text-lg xl:text-xl">Policy Script</label>
            <TextareaInput rows="5" name="script" id="script"
             :model-value="policyString" disabled readonly
             placeholder="Policy will be generated automagically..." />
        </div>

        <div
            class="flex items-center justify-between py-3 mt-2">
            <div class="flex">

            </div>
            <div class="flex-shrink-0">
                <button @click.prevent="saveSeedPhrase"
                        class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                        {{ ballot ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </form>
</template>
<script setup lang="ts">
import BallotData = App.DataTransferObjects.BallotData;
import PolicyData = App.DataTransferObjects.PolicyData;
import NewBallotPolicyForm from '@/Pages/Auth/Policies/Partials/NewBallotPolicyForm.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AlertService from '@/shared/Services/alert-service';

const props = defineProps<{
    ballot: BallotData;
    policy?: PolicyData;
    context: string;
}>();

let form = useForm({
    seedphrase: '',
    context: props.context,
});

const policyString = computed(() => {
    if (props.policy?.script) {
        return JSON.stringify(props.policy.script);
    }
    return ''
});

function saveSeedPhrase() {
    form.post(route('admin.ballots.policies.store', {ballot: props.ballot?.hash}), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                AlertService.show(['Wallet created. Policy generated successfully'], 'success');
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

</script>
