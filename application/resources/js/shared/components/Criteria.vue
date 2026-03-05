<template>
    <div class="flex flex-col">
        <div v-if="isReadonlyMode" class="flex flex-col gap-3">
            <div
                v-if="configuredCriteria.length === 0"
                class="rounded-lg border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400"
            >
                No gated signing criteria configured.
            </div>

            <div
                v-for="criterion in configuredCriteria"
                :key="`readonly-${criterion.type}`"
                class="flex items-center gap-4 p-3 rounded-xl bg-slate-800/50 border border-slate-700/50"
            >
                <!-- Asset Image -->
                <div v-if="criterion.image_url" class="shrink-0">
                    <img 
                        :src="criterion.image_url" 
                        :alt="criterion.title || 'Asset preview'"
                        class="h-16 w-16 rounded-xl object-cover border border-slate-600 bg-slate-900"
                        @error="$event.target.style.display = 'none'"
                    />
                </div>
                <div v-else class="shrink-0 h-16 w-16 rounded-xl bg-slate-700/50 flex items-center justify-center border border-slate-600">
                    <span class="text-xs font-bold text-slate-500">{{ criterion.type.toUpperCase() }}</span>
                </div>

                <!-- Info -->
                <div class="flex flex-col gap-1 flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-white">{{ criterion.title || 'Untitled' }}</span>
                        <span 
                            class="px-2 py-0.5 rounded-full text-xs font-medium"
                            :class="criterion.type === 'nft' ? 'bg-purple-500/20 text-purple-300' : 'bg-emerald-500/20 text-emerald-300'"
                        >
                            {{ criterion.type.toUpperCase() }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <span class="font-mono">{{ truncatePolicyId(criterion.value2) }}</span>
                        <button 
                            @click="copyPolicyId(criterion.value2)"
                            class="p-1 rounded hover:bg-slate-700/50 transition-colors"
                            title="Copy full Policy ID"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="flex flex-col gap-2">
            <div
                v-for="(criterion, index) in criteriaRef"
                :key="`editable-${criterion.type}`"
                class="flex items-center gap-3 p-3 rounded-xl border transition-colors"
                :class="criterion.hash ? 'bg-slate-800/50 border-slate-700/50' : 'bg-transparent border-slate-700/30'"
            >
                <!-- Asset Image or Placeholder -->
                <div v-if="criterion.image_url && criterion.hash" class="shrink-0">
                    <img 
                        :src="criterion.image_url" 
                        :alt="criterion.title || 'Asset preview'"
                        class="h-14 w-14 rounded-xl object-cover border border-slate-600 bg-slate-900"
                        @error="$event.target.style.display = 'none'"
                    />
                </div>
                <div v-else class="shrink-0 h-14 w-14 rounded-xl bg-slate-700/30 flex items-center justify-center border border-slate-600/50">
                    <span class="text-xs font-bold text-slate-500">{{ criterion.type.toUpperCase() }}</span>
                </div>

                <!-- Info -->
                <div class="flex flex-col gap-0.5 flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-white">{{ criterion.name }}</span>
                        <span 
                            v-if="criterion.hash"
                            class="px-2 py-0.5 rounded-full text-xs font-medium"
                            :class="criterion.type === 'nft' ? 'bg-purple-500/20 text-purple-300' : 'bg-emerald-500/20 text-emerald-300'"
                        >
                            {{ criterion.type.toUpperCase() }}
                        </span>
                    </div>
                    <span v-if="criterion.title && criterion.hash" class="text-sm text-slate-400 truncate">
                        {{ criterion.title }}
                    </span>
                    <span
                        v-if="criterion.hash && criterion.value2"
                        class="font-mono text-xs text-slate-500"
                    >
                        {{ truncatePolicyId(criterion.value2) }}
                    </span>
                    <span v-else class="text-xs text-slate-500">
                        Not configured
                    </span>
                </div>

                <!-- Toggle -->
                <div class="shrink-0">
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
import AlertService from "@/shared/Services/alert-service";

type CriterionType = "nft" | "ft";

type CriteriaRow = {
    name: string;
    type: CriterionType;
    hash?: string | null;
    title?: string | null;
    value2?: string | null;
    image_url?: string | null;
    asset_metadata?: string | null;
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

const isPollModel = computed(() => {
    return Boolean((props.model as PollData | undefined)?.question !== undefined);
});

const ruleRoutes = computed(() => {
    if (isPollModel.value) {
        return {
            create: "polls.rules.create",
            remove: "polls.rules.removeRule",
        };
    }

    return {
        create: "petitions.rules.create",
        remove: "petitions.rules.removeRule",
    };
});

const modelRouteParam = computed(() => {
    return isPollModel.value ? "poll" : "petition";
});

const effectiveReturnRoute = computed(() => {
    if (props.returnRoute !== "petitions.manage") {
        return props.returnRoute;
    }

    return isPollModel.value ? "polls.manage" : "petitions.manage";
});

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
        props.model?.status === "pending" ||
        props.model?.status === "approved" ||
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

const truncatePolicyId = (policyId: string | null | undefined): string => {
    if (!policyId) return '';
    if (policyId.length <= 16) return policyId;
    return `${policyId.slice(0, 8)}...${policyId.slice(-8)}`;
};

const copyPolicyId = (policyId: string | null | undefined): void => {
    if (!policyId) return;
    navigator.clipboard.writeText(policyId).then(() => {
        AlertService.show(['Policy ID copied to clipboard'], 'success');
    }).catch(() => {
        AlertService.show(['Failed to copy Policy ID'], 'error');
    });
};

const makeRule = (toggleOn: boolean, type: CriterionType, hash?: string | null): void => {
    if (isReadonlyMode.value || isEditingDisabled.value || !props.model?.hash) {
        return;
    }

    const data = { type, returnRoute: effectiveReturnRoute.value };
    const routeParams = {
        [modelRouteParam.value]: props.model.hash,
    };

    if (toggleOn && !hash) {
        router.get(route(ruleRoutes.value.create, routeParams), data);
    } else if (!toggleOn && hash) {
        router.get(
            route(ruleRoutes.value.remove, {
                ...routeParams,
                rule: hash,
                returnRoute: effectiveReturnRoute.value,
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