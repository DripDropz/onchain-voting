<template>
    <div>
        <template v-if="milestones.length">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-3">Signature Milestones</p>
            <div class="space-y-2">
                <div
                    v-for="(m, i) in milestones"
                    :key="i"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    :class="m.state === 'achieved'
                        ? 'bg-emerald-900/20 border border-emerald-700/30'
                        : m.state === 'current'
                            ? 'bg-sky-900/20 border border-sky-700/40'
                            : 'bg-gray-800/40 border border-gray-700/30'"
                >
                    <!-- Icon -->
                    <div
                        class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center"
                        :class="m.state === 'achieved'
                            ? 'bg-emerald-500/20 text-emerald-400'
                            : m.state === 'current'
                                ? 'bg-sky-500/20 text-sky-400'
                                : 'bg-gray-700 text-gray-500'"
                    >
                        <CheckCircleIcon v-if="m.state === 'achieved'" class="w-4 h-4" />
                        <SparklesIcon v-else-if="m.state === 'current'" class="w-4 h-4" />
                        <LockClosedIcon v-else class="w-3.5 h-3.5" />
                    </div>

                    <!-- Label + threshold -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold leading-none"
                           :class="m.state === 'achieved' ? 'text-emerald-400' : m.state === 'current' ? 'text-sky-300' : 'text-gray-400'">
                            {{ m.label }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ m.threshold?.toLocaleString() }} signatures</p>
                    </div>

                    <!-- State badge -->
                    <span
                        class="shrink-0 text-xs font-medium px-2 py-0.5 rounded-full"
                        :class="m.state === 'achieved'
                            ? 'bg-emerald-500/20 text-emerald-400'
                            : m.state === 'current'
                                ? 'bg-sky-500/20 text-sky-400'
                                : 'bg-gray-700 text-gray-500'"
                    >
                        {{ m.state === 'achieved' ? 'Reached' : m.state === 'current' ? 'Next' : 'Locked' }}
                    </span>
                </div>
            </div>
        </template>
        <template v-else>
            <p class="text-xs text-gray-500 italic">
                Admin will set signature goals during review
            </p>
        </template>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { CheckCircleIcon, LockClosedIcon, SparklesIcon } from '@heroicons/vue/20/solid';
import { storeToRefs } from 'pinia';
import PetitionData = App.DataTransferObjects.PetitionData;
import { usePetitionSignatureStore } from '@/Pages/Petition/stores/petition-signature-store';

const props = defineProps<{
    petition: PetitionData;
}>();

const petitionSignatureStore = usePetitionSignatureStore();
petitionSignatureStore.setPetition(props.petition);
const {
    petition$,
    visible$,
    featurePetition$,
    ballotEligible$,
} = storeToRefs(petitionSignatureStore);

type MilestoneState = 'achieved' | 'current' | 'locked';

interface Milestone {
    label: string;
    threshold: number;
    state: MilestoneState;
}

const milestones = computed<Milestone[]>(() => {
    const count = Number(petition$.value?.signatures_count ?? 0);
    const raw: Array<{ key: string; label: string; data: any }> = [
        { key: 'visible',          label: 'Visible on Browse',  data: visible$.value },
        { key: 'feature-petition', label: 'Featured Petition',  data: featurePetition$.value },
        { key: 'ballot-eligible',  label: 'Ballot Eligible',    data: ballotEligible$.value },
    ];

    const defined = raw.filter(m => m.data?.value2 != null);
    if (!defined.length) return [];

    let currentSet = false;
    return defined.map(m => {
        const threshold = Number(m.data.value2);
        let state: MilestoneState;
        if (count >= threshold) {
            state = 'achieved';
        } else if (!currentSet) {
            state = 'current';
            currentSet = true;
        } else {
            state = 'locked';
        }
        return { label: m.label, threshold, state };
    });
});
</script>
