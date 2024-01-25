<template>
    <ModalRoute>
        <div class="flex flex-col gap-3 p-4">
            <p class="">Remove <span class="italic font-bold">{{ rule.title }}</span> from <span class="italic font-bold">{{
                petition.title }}</span>
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
import PetitionData = App.DataTransferObjects.PetitionData;
import RuleData = App.DataTransferObjects.RuleData;
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ModalRoute from '@/Components/ModalRoute.vue';
import { useForm } from '@inertiajs/vue3';
import AlertService from '@/shared/Services/alert-service';
import { useModal } from 'momentum-modal';


const props = withDefaults(defineProps<{
    petition?: PetitionData;
    rule?: RuleData
}>(), {
});

let {close} = useModal()

let form = useForm({})

let removeRule = () => {
    form.post(route('petitions.rules.delete', { petition: props.petition.hash, rule: props.rule.hash }),
        {
            onSuccess: () => {
                AlertService.show(['Rule deleted'], 'success');
                close()
            },
            onError: (errors) => {
                AlertService.show(
                    Object.entries(errors).map(([key, value]) => value)
                );
            },
        })
}


</script>