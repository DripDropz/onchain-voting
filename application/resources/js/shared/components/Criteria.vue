<template>
    <div class="flex flex-col p-2">
        <div v-if="isReadonlyMode" class="flex flex-col">
            <div
                v-if="configuredCriteria.length === 0"
                class="rounded-lg border border-dashed border-gray-300 px-3 py-2 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400"
            >
                No gated signing criteria configured.
            </div>

            <div
                v-for="criterion in configuredCriteria"
                :key="`readonly-${criterion.type}`"
                class="flex w-full flex-col gap-1 border-gray-400 border-opacity-40 py-2 text-sm dark:border-gray-600"
            >
                <span class="font-bold dark:text-white">{{ criterion.name }}</span>
                <span v-if="criterion.title" class="text-slate-500 dark:text-slate-400">{{ criterion.title }}</span>
                <span class="break-all font-mono text-xs text-slate-500 dark:text-slate-400">
                    Policy ID: {{ criterion.value2 }}
                </span>
            </div>
        </div>

        <div v-else>
            <div
                v-for="(criterion, index) in criteriaRef"
                :key="`editable-${criterion.type}`"
                class="flex w-full flex-row items-center justify-between gap-2 border-b border-gray-400 border-opacity-40 py-2 dark:border-gray-600"
            >
                <div class="flex flex-col gap-1 text-sm">
                    <span class="font-bold dark:text-white">{{ criterion.name }}</span>
                    <span
                        v-if="criterion.hash && criterion.value2"
                        class="break-all font-mono text-xs text-slate-500 dark:text-slate-400"
                    >
                        Policy ID: {{ criterion.value2 }}
                    </span>
                </div>
                <div>
                    <label
                        class="relative inline-flex items-center"
                        :for="`${criterion.type}-${index}`"
                        :class="[isEditingDisabled ? 'cursor-not-allowed' : 'cursor-pointer']"
                    >
                        <input
                            :id="`${criterion.type}-${index}`"
                            type="checkbox"
                            :value="criterion.type"
                            class="peer sr-only"
                            :disabled="isEditingDisabled"
                            :checked="!!criterion.hash"
                            @change="(e) => makeRule((e.target as HTMLInputElement).checked, criterion.type, criterion.hash)"
                        >
                        <div
                            class="h-6 w-11 rounded-full bg-gray-200 peer-focus:ring-4 peer-focus:ring-sky-300 after:absolute after:start-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-sky-400 peer-checked:after:translate-x-full peer-checked:after:border-white rtl:peer-checked:after:-translate-x-full dark:bg-gray-700 dark:border-gray-600 dark:peer-focus:ring-sky-800"
                        />
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import PetitionData = App.DataTransferObjects.PetitionData;
import PollData = App.DataTransferObjects.PollData;

type CriterionType = "nft" | "ft";

type CriteriaRow = {
    name: string;
    type: CriterionType;
    hash?: string | null;
    title?: string | null;
    value2?: string | null;
};

const props = withDefaults(
    defineProps<{
        model?: PetitionData | PollData;
        readonly?: boolean;
        returnRoute?: string;
        mode?: "editable" | "readonly";
    }>(),
    {
        readonly: false,
        returnRoute: "petitions.manage",
    }
);

const isReadonlyMode = computed(() => {
    if (props.mode) {
        return props.mode === "readonly";
    }

    return props.readonly;
});

const isEditingDisabled = computed(() => {
    return (
        !props.model?.hash ||
        isReadonlyMode.value ||
        props.model?.status === "published" ||
        props.model?.status === "closed" ||
        !!props.model?.ballot
    );
});

const criteria = computed<CriteriaRow[]>(() => [
    {
        name: "NFT",
        type: "nft",
        ...(props.model?.rules?.find((item) => item.type === "nft") ?? {}),
    },
    {
        name: "FT",
        type: "ft",
        ...(props.model?.rules?.find((item) => item.type === "ft") ?? {}),
    },
]);

const criteriaRef = ref<CriteriaRow[]>(criteria.value);

const configuredCriteria = computed(() => {
    return criteria.value.filter((criterion) => !!criterion.hash && !!criterion.value2);
});

const makeRule = (toggleOn: boolean, type: CriterionType, hash?: string | null): void => {
    if (isReadonlyMode.value || isEditingDisabled.value || !props.model?.hash) {
        return;
    }

    const data = { type, returnRoute: props.returnRoute };

    if (toggleOn && !hash) {
        router.get(route("petitions.rules.create", { petition: props.model.hash }), data);
    } else if (!toggleOn && hash) {
        router.get(
            route("petitions.rules.removeRule", {
                petition: props.model.hash,
                rule: hash,
                returnRoute: props.returnRoute,
            })
        );
    }
};

watch(
    () => criteria.value,
    (nextCriteria) => {
        criteriaRef.value = nextCriteria;
    },
    { deep: true }
);
</script>